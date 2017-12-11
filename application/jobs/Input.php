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


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.input",
    "params": {
        "connection": "",
        "items": []
    }
}
*/

class Input extends \Flexio\Jobs\Base
{
    // job-global connection properties
    private $cached_connection_properties = null;
    private $process = null;

    private function getProcess()
    {
        return $this->process;
    }

    private function setProcess(\Flexio\IFace\IProcess $process)
    {
        $this->process = $process;
    }

    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // store a reference to the process
        $this->setProcess($process);

        // make sure we have a params node
        $job_definition = $this->getProperties();
        if (!isset($job_definition['params']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        $params = $job_definition['params'];


        // make fully qualified path, if necessary
        $location = $params['location'] ?? '';
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "No input items specified. Please add one or more input items, for example with file: ...");

        // many variants of the input job specify a job-scope connection
        foreach ($items as $item)
        {
            $this->runImport($item['connection_info'], $item['file_info']);
        }
    }

    private function resolveInputItems(array $params)
    {
        // if the items parameter isn't an array, the format is invalid
        $items = $params['items'] ?? false;
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

    private function runImport($connection_info, array $file_info) // connection_info is null for vfs
    {
        $connection_eid = $connection_info['eid'] ?? false;
        $connection_type = $connection_info['connection_type'] ?? false;

        // load the service

        if ($connection_info)
        {
            $connection_type = $connection_info['connection_type'] ?? null;
            if ($connection_type == \Flexio\Services\Factory::TYPE_HTTP)
                $service = \Flexio\Services\Http::create();
            else if ($connection_type == \Flexio\Services\Factory::TYPE_RSS)
                $service = \Flexio\Services\Http::create();
            else
                $service = \Flexio\Services\Factory::create($connection_info);
        }
         else
        {
            return $this->runRemoteFileImport(null, $file_info);
        }

        // route the request based on the connection type
        switch ($connection_type)
        {
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            // database data
            case \Flexio\Services\Factory::TYPE_MYSQL:
            case \Flexio\Services\Factory::TYPE_POSTGRES:
                return $this->runDatabaseImport($service, $file_info);

            case \Flexio\Services\Factory::TYPE_ELASTICSEARCH:
                return $this->runElasticSearchImport($service, $file_info);

            // api table type data
            case \Flexio\Services\Factory::TYPE_RSS:
            case \Flexio\Services\Factory::TYPE_MAILJET:
            case \Flexio\Services\Factory::TYPE_PIPELINEDEALS:
            case \Flexio\Services\Factory::TYPE_SOCRATA:
            case \Flexio\Services\Factory::TYPE_TWILIO:
                return $this->runApiTableImport($service, $file_info);

            // remote file type data
            case \Flexio\Services\Factory::TYPE_HTTP:
            case \Flexio\Services\Factory::TYPE_FTP:
            case \Flexio\Services\Factory::TYPE_SFTP:
            case \Flexio\Services\Factory::TYPE_DROPBOX:
            case \Flexio\Services\Factory::TYPE_BOX:
            case \Flexio\Services\Factory::TYPE_GOOGLEDRIVE:
            case \Flexio\Services\Factory::TYPE_GOOGLESHEETS:
            case \Flexio\Services\Factory::TYPE_GITHUB:
            case \Flexio\Services\Factory::TYPE_AMAZONS3:
                return $this->runRemoteFileImport($service, $file_info);
        }
    }

    private function runDatabaseImport($service, array $file_info) // TODO: set paramater type
    {
        if (!$service)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        // get the input
        $path = $file_info['path'];
        $structure = $service->describeTable($path);
        if (!$structure)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // create the output
        $stream_properties = $file_info;
        $stream_properties['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_properties['structure'] =  $structure;
        $outstream = $this->getProcess()->getStdout();
        $streamwriter = $outstream->getWriter();

        // create the iterator
        $iter = $service->queryAll($path);
        if (!$iter)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // transfer the data
        while (true)
        {
            $row = $iter->fetchRow();
            if (!$row)
                break;

            $result = $streamwriter->write(array_values($row));
            if ($result === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function runElasticSearchImport($service, array $file_info) // TODO: set paramater type
    {
        if (!$service)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        // TODO: implement
    }

    private function runApiTableImport($service, array $file_info) // TODO: set paramater type
    {
        if (!$service)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        // get the input
        $path = $file_info['path'];
        $structure = $service->describeTable($path);
        if (!$structure)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // create the output
        $stream_properties = $file_info;
        $stream_properties['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
        $stream_properties['structure'] =  $structure;
        $outstream = $this->getProcess()->getStdout();
        $streamwriter = $outstream->getWriter();

        // transfer the data
        $params = array();
        $params['path'] = $path;
        $service->read($params, function($row) use (&$streamwriter) {
            $streamwriter->write($row);
        });

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }

    private function runRemoteFileImport($service, array $file_info) // TODO: set paramater type
    {
        if (!$service)
        {
            $service = new \Flexio\Services\Vfs();
        }

        // get the input path
        $path = $file_info['path'];
        $stream_properties = $file_info;

        $mime_data_sample = '';
        $is_table = null;
        $streamwriter = false;
        $outstream = false;

        $service->read(array('path'=>$path), function($data) use (&$outstream, &$streamwriter, &$stream_properties, &$mime_data_sample, &$is_table) {

            if (is_null($is_table))
            {
                if (is_array($data))
                {
                    // $data payload contains tabular data
                    $is_table = true;
                    $structure = \Flexio\Base\Structure::create();

                    for ($i = 0; $i < count($data); ++$i)
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

                    $stream_properties['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
                    $outstream = $this->getProcess()->getStdout();
                    $outstream->setStructure($structure);
                    $streamwriter = $outstream->getWriter();
                }
                 else
                {
                    // $data payload contains binary/text data
                    $is_table = false;

                    $outstream = $this->getProcess()->getStdout();
                    $streamwriter = $outstream->getWriter();
                }
            }

            if ($streamwriter !== false)
                $streamwriter->write($data);

            // save a sample of data for determining the mime type
            if (!$is_table)
            {
                if (strlen($mime_data_sample) <= 1024)
                    $mime_data_sample .= $data;
                $length = strlen($data);
                return $length;
            }
        });

        if (!$streamwriter)
            $streamwriter = $outstream->getWriter();
        $streamwriter->close();

        // set the mime type
        if ($is_table)
            $mime_type = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
             else
            $mime_type = \Flexio\Base\ContentType::getMimeType($path, $mime_data_sample);


        // TODO: we want to identify json content, but this isn't easily distinguishable
        // from text if we only have a snippet of the data; ideally, we should get the
        // content type from the service, but this will take a some work to patch this
        // through; for now, if we have 'plain/text', see if the first part of the string
        // look like json (e.g is either '[' or '{')

        if ($mime_type === \Flexio\Base\ContentType::MIME_TYPE_TXT)
        {
            $mime_data_sample_clean = trim($mime_data_sample);
            $first_char = substr($mime_data_sample_clean,0,1);
            if ($first_char === '[' || $first_char === '{')
                $mime_type = \Flexio\Base\ContentType::MIME_TYPE_JSON;
        }

        if ($is_table)
        {
            $outstream->set(array('mime_type' => $mime_type));
        }
         else
        {
            $outstream->set(array('size' => $streamwriter->getBytesWritten(),
                                  'mime_type' => $mime_type));
        }
        $this->getProcess()->getStdout()->copy($outstream); // TODO: only set stdout? merge all content from all input items or only output last?
    }

    private function getConnectionInfoFromItem(array $params, array $item)
    {
        if (!isset($item['path']))
            return null;
        $path = $item['path'];

        // does the item's path contain a url?
        if (substr($path,0,7) == 'http://' || substr($path,0,8) == 'https://')
            return array('connection_type' => \Flexio\Services\Factory::TYPE_HTTP);

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

    private function getMatchingFileInfo($connection_info, array $item) : array   // connection_info is null for vfs
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
        $connection_type = $connection_info['connection_type'] ?? false;
        if ($connection_type == \Flexio\Services\Factory::TYPE_HTTP || $connection_type == \Flexio\Services\Factory::TYPE_RSS)
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


        $directory = dirname($path);
        $pattern = basename($path);

        if (is_null($connection_info))
        {
            $vfs = new \Flexio\Services\Vfs();

            $files_in_directory = $vfs->list($directory);
        }
         else
        {
            // get the path and the name part of the path; for now, assume that any wildcard
            // is only in the name part (i.e., don't allow directory searches); TODO: expand
            // to allow directory searches

            // get the service; TODO: error if we can't do it? similar to not getting the path?
            $service = \Flexio\Services\Factory::create($connection_info);
            if ($service === false)
                return array();

            $files_in_directory = $service->list($directory);
        }


        foreach ($files_in_directory as $file_item_info)
        {
            $is_dir = $file_item_info['is_dir'];
            $filepath = $file_item_info['path'];
            $filename = $file_item_info['name'];

            // don't match against directories
            if ($is_dir === true)
                continue;

            // look for the item -- pattern can be a filename or a wildcard
            if (\Flexio\Base\File::matchPath($filename, $pattern, true) === false)
                continue;

            // we found the item
            $file_info['path'] = $filepath;
            $file_info['name'] = $filename;
            $matching_paths[] = $file_info;
        }

        return $matching_paths;
    }

    private static function getSpreadsheetColumnName(int $idx) : string
    {
        $n = $idx % 26;
        $ch = chr(ord('A') + $n);
        $idx = intdiv($idx, 26);
        if ($idx > 0)
            return self::getSpreadsheetColumnName($idx-1) . $ch;
             else
            return $ch;
    }
}
