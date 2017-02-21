<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-16
 *
 * @package flexio
 * @subpackage Jobs
 */


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

class InputJob extends \Flexio\Jobs\Base
{
    // job-global connection properties
    private $cached_connection_properties = null;

    public function run()
    {
        // make sure we have a params node
        $job_definition = $this->getProperties();
        if (!isset($job_definition['params']))
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);
        $params = $job_definition['params'];

        // make fully qualified path, if necessary
        $location = isset_or($params['location'], '');
        if (strlen($location) > 0 && isset($params['items']))
        {
            $location .= '/';

            foreach ($params['items'] as &$item)
            {
                if (isset($item['path']))
                {
                    $path = $location . $item['path'];
                    while (strpos($path, '//') !== false)
                        $path = str_replace('//','/', $path);
                    $item['path'] = $path;
                }
                unset($item);
            }
        }


        // resolve the input items, converting path wildcards into explicit
        // paths and determining the appropriate connection type/eid for each item
        $items = $this->resolveInputItems($params);
        if ($items === false)
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);

        // input job adds new streams; add streams onto inputs we've already received
        $this->getOutput()->merge($this->getInput());

        // many variants of the input job specify a job-scope connection
        foreach ($items as $item)
        {
            $this->runImport($item['connection_info'], $item['file_info']);
        }
    }

    private function resolveInputItems($params)
    {
        // if the items parameter isn't an array, the format is invalid
        $items = isset_or($params['items'], false);
        if (!is_array($items))
            return false;

        $resolved_items = array();
        foreach ($items as $item)
        {
            // get the connection info
            $connection_info = $this->getConnectionInfoFromItem($params, $item);

            // get the file paths from the items
            $file_info_list = $this->getMatchingFileInfo($connection_info, $item);

            // add each of the matching paths to the list of items to import
            foreach ($file_info_list as $file_info)
            {
                // keep connection info separate from path info to make sure the
                // connection info doesn't leak into any streams that may be created
                // from the item
                $resolved_item = array();
                $resolved_item['connection_info'] = $connection_info;
                $resolved_item['file_info'] = $file_info;
                $resolved_items[] = $resolved_item;
            }
        }

        return $resolved_items;
    }

    private function runImport($connection_info, $file_info)
    {
        $connection_eid = isset_or($connection_info['eid'], false);
        $connection_type = isset_or($connection_info['connection_type'], false);

        // handle cases where data is already imported; if the input
        // connection is the default connection, then the data is internal, so
        // just set the output pointer to the existing location and we're done
        // TODO: this is experimental
        if ($connection_eid === \Flexio\Object\Connection::getDatastoreConnectionEid())
        {
            $stream_properties = $file_info;
            $outstream = \Flexio\Object\Stream::create($stream_properties);
            $this->getOutput()->push($outstream);
            return;
        }

        // load the service
        $service = \Store::load($connection_info);
        if ($service === false)
            return $this->fail(\Model::ERROR_NO_SERVICE, _(''), __FILE__, __LINE__);

        // route the request based on the connection type
        switch ($connection_type)
        {
            default:
                return $this->fail(\Model::ERROR_INVALID_PARAMETER, _(''), __FILE__, __LINE__);

            // upload
            case \Model::CONNECTION_TYPE_UPLOAD:
                return $this->runUpload($service, $file_info);

            // database data
            case \Model::CONNECTION_TYPE_MYSQL:
            case \Model::CONNECTION_TYPE_POSTGRES:
                return $this->runDatabaseImport($service, $file_info);

            // api table type data
            case \Model::CONNECTION_TYPE_RSS:
            case \Model::CONNECTION_TYPE_MAILJET:
            case \Model::CONNECTION_TYPE_PIPELINEDEALS:
            case \Model::CONNECTION_TYPE_SOCRATA:
            case \Model::CONNECTION_TYPE_TWILIO:
                return $this->runApiTableImport($service, $file_info);

            // remote file type data
            case \Model::CONNECTION_TYPE_HTTP:
            case \Model::CONNECTION_TYPE_FTP:
            case \Model::CONNECTION_TYPE_SFTP:
            case \Model::CONNECTION_TYPE_DROPBOX:
            case \Model::CONNECTION_TYPE_GOOGLEDRIVE:
            case \Model::CONNECTION_TYPE_AMAZONS3:
                return $this->runRemoteFileImport($service, $file_info);

            // api-specific type data
            case \Model::CONNECTION_TYPE_GOOGLESHEETS:
                return $this->runGoogleSheetsImport($service, $file_info);
        }
    }

    private function runUpload($service, $file_info)
    {
        // the data is already uploaded, so simply pass on the data to
        // the output
        $stream_properties = $file_info;
        $outstream = \Flexio\Object\Stream::create($stream_properties);
        $this->getOutput()->push($outstream);
    }

    private function runDatabaseImport($service, $file_info)
    {
        // get the input
        $path = $file_info['path'];
        $structure = $service->describeTable($path);
        if (!$structure)
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);

        // create the output
        $stream_properties = $file_info;
        $stream_properties['mime_type'] = \ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_properties['structure'] =  $structure;
        $outstream = self::createDatastoreStream($stream_properties);
        if ($outstream === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        $this->getOutput()->push($outstream);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        // create the iterator
        $iter = $service->queryAll($path);
        if (!$iter)
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);

        // transfer the data
        while (true)
        {
            $row = $iter->fetchRow();
            if (!$row)
                break;

            $result = $streamwriter->write(array_values($row));
            if ($result === false)
                return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function runApiTableImport($service, $file_info)
    {
        // get the input
        $path = $file_info['path'];
        $structure = $service->describeTable($path);
        if (!$structure)
            return $this->fail(\Model::ERROR_READ_FAILED, _(''), __FILE__, __LINE__);

        // create the output
        $stream_properties = $file_info;
        $stream_properties['mime_type'] = \ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_properties['structure'] =  $structure;
        $outstream = self::createDatastoreStream($stream_properties);
        if ($outstream === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        $this->getOutput()->push($outstream);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        // transfer the data
        $service->read($path, function($row) use (&$streamwriter) {
            $streamwriter->write($row);
        });

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function runRemoteFileImport($service, $file_info)
    {
        // get the input
        $path = $file_info['path'];
        $stream_properties = $file_info;
        $outstream = self::createDatastoreStream($stream_properties);
        if ($outstream === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        $this->getOutput()->push($outstream);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
        if ($streamwriter === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        $mime_data_sample = '';
        $service->read($path, function($data) use (&$streamwriter, &$mime_data_sample) {

            // save a sample of data for determining the mime type
            if (strlen($mime_data_sample) <= 1024)
                $mime_data_sample .= $data;

            $streamwriter->write($data);
            $length = strlen($data);
            return $length;
        });

        $streamwriter->close();

        // set the mime type
        $mime_type = \ContentType::getMimeType($path, $mime_data_sample);
        $outstream->setMimeType($mime_type);
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function runGoogleSheetsImport($service, $file_info)
    {
        // get the input
        $path = $file_info['path'];

        // create the output
        $stream_properties = $file_info;
        $stream_properties['mime_type'] = \ContentType::MIME_TYPE_FLEXIO_TABLE;
        $outstream = self::createDatastoreStream($stream_properties);
        if ($outstream === false)
            return $this->fail(\Model::ERROR_WRITE_FAILED, _(''), __FILE__, __LINE__);

        $this->getOutput()->push($outstream);

        $rownum = 0;
        $structure = \Flexio\Object\Structure::create();

        $streamwriter = false;
        $service->read($path, function($row) use (&$outstream, &$structure, &$streamwriter, &$rownum) {

            $rownum++;

            if ($rownum == 1)
            {
                for ($i = 0; $i < count($row); ++$i)
                {
                    $colname = self::getSpreadsheetColumnName($i);
                    $added_field = $structure->push(array(
                        'name' =>          $colname,
                        'type' =>          'text',
                        'width' =>         null,
                        'scale' =>         0
                    ));

                    // TODO: make sure the row we're inserting matches with any fieldname adjustments?
                }

                $outstream->setStructure($structure);
                $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

                if ($streamwriter !== false)
                    $streamwriter->write($row);
            }
             else
            {
                if ($streamwriter !== false)
                    $streamwriter->write($row);
            }
        });

        if ($streamwriter !== false)
        {
            $streamwriter->close();
            $outstream->setSize($streamwriter->getBytesWritten());
        }
    }

    private function createDatastoreStream($properties)
    {
        // get a default connection and path
        $properties['connection_eid'] = \Flexio\Object\Connection::getDatastoreConnectionEid();
        $properties['path'] = \Util::generateHandle();

        if (!\Eid::isValid($properties['connection_eid']))
            return false;

        $stream = \Flexio\Object\Stream::create($properties);
        return $stream;
    }

    private function getConnectionInfoFromItem($params, $item)
    {
        if (!isset($item['path']))
            return null;
        $path = $item['path'];

        // does the item's path contain a url?
        if (substr($path,0,7) == 'http://' || substr($path,0,8) == 'https://')
            return array('connection_type' => \Model::CONNECTION_TYPE_HTTP);

        if (isset($params['connection']))
        {
            if (isset($this->cached_connection_properties))
            {
                // used cached properties for 'from' connection
                $props = $this->cached_connection_properties;
            }
             else
            {
                $connection = \Flexio\Object\Connection::load($params['connection']);
                if ($connection === false)
                    return null;

                $props = $connection->get();
                $this->cached_connection_properties = $props;
            }

            return $props;
        }

        return null;
    }

    private function getMatchingFileInfo($connection_info, $item)
    {
        $matching_paths = array();

        // clean the item info from any connection info that may be specified
        $file_info = array();
        if (isset($item['name']))
            $file_info['name'] = $item['name'];
        if (isset($item['path']))
            $file_info['path'] = $item['path'];

        // make sure we have a path; TODO: error?
        if (!isset($file_info['path']))
            return array();

        // create default stream name if none is specified
        $path = $file_info['path'];
        if (!isset($file_info['name']))
            $file_info['name'] = basename($path);

        // if the connection type is http or rss, wildcard matches aren't supported, so
        // simply return; TODO: we should use some check on the service to see if the
        // notion of directory lookup is included so we don't have to keep adding these
        // types for other connections that may not support wildcard lookup
        $connection_type = isset_or($connection_info['connection_type'], false);
        if ($connection_type == \Model::CONNECTION_TYPE_HTTP || $connection_type == \Model::CONNECTION_TYPE_RSS)
        {
            $matching_paths[] = $file_info;
            return $matching_paths;
        }

        // only do wildcard matching if a wildcard is found in the filespec.  This also
        // prevents unnecessary directory lookups
        if (false === strpos($path, '*') && false === strpos($path, '?'))
        {
            $matching_paths[] = $file_info;
            return $matching_paths;
        }

        // get the path and the name part of the path; for now, assume that any wildcard
        // is only in the name part (i.e., don't allow directory searches); TODO: expand
        // to allow directory searches
        $directory = dirname($path);
        $pattern = basename($path);

        // get the service; TODO: error if we can't do it? similar to not getting the path?
        $service = \Store::load($connection_info);
        if ($service === false)
            return array();

        // TODO: check for straight-up existence of full path; if it exists, we have a single
        // item, so simply return that

/*
// TODO: handle renames for direct paths that don't have a name

        // if the name isn't specified, use the path name
        if (!isset($file_info['name']))
        {
            $file_info['name'] = basename($file_info['path']);

            // sanitize filename - remove bad characters from beginning and end; convert them to dashes in the middle
            $file_info['name'] = preg_replace("/^([^\w\s\d\-_~,;\[\]\(\).])/", '', $file_info['name']);
            $file_info['name'] = preg_replace("/([^\w\s\d\-_~,;\[\]\(\).])/", '-', $file_info['name']);
            $file_info['name'] = preg_replace("/([^\w\s\d\-_~,;\[\]\(\).])$/", '', $file_info['name']);
        }
*/
        $files_in_directory = $service->listObjects($directory);
        foreach ($files_in_directory as $file_item_info)
        {
            $is_dir = $file_item_info['is_dir'];
            $filepath = $file_item_info['path'];
            $filename = $file_item_info['name'];

            // don't match against directories
            if ($is_dir === true)
                continue;

            // look for the item -- pattern can be a filename or a wildcard
            if (\Util::matchPath($filename, $pattern, true) === false)
                continue;

            // we found the item
            $file_info['path'] = $filepath;
            $file_info['name'] = $filename;
            $matching_paths[] = $file_info;
        }

        return $matching_paths;
    }

    private static function getSpreadsheetColumnName($idx)
    {
        $n = $idx % 26;
        $ch = chr(ord('A') + $n);
        $idx = intdiv($idx, 26);
        if ($idx > 0)
            return self::getSpreadsheetColumnName($idx-1) . $ch;
             else
            return $ch;
    }


    // job definition info
    const MIME_TYPE = 'flexio.input';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.input",
        "params": {
            "connection": "",
            "items": []
        }
    }
EOD;
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.input"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
