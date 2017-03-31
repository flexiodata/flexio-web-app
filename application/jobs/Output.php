<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-18
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Output extends \Flexio\Jobs\Base
{
    // job-global connection properties
    private $cached_connection_properties = null;
    private $streams = [];



    public function run()
    {
        // make sure we have a params node
        $job_definition = $this->getProperties();
        if (!isset($job_definition['params']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        $params = $job_definition['params'];

        // get an array of stream objects
        $this->streams = $this->getInput()->enum();

        // resolve the output items, determining the appropriate connection type/eid for each item
        // in addition, a member will be populated called stream_idx, which will reference the
        // correct element of the $streams array;  this stream_idx allows wildcards with multiple
        // input streams with the same name

        $items = $this->resolveOutputItems($params);
        if ($items === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $this->getOutput()->merge($this->getInput());

        // current behavior is to only allow outputs in runtime;
        // so, if we're not in runtime, we're done
        if ($this->isRunMode() === false)
            return;

        // many variants of the output job specify a job-scope connection
        foreach ($items as $item)
        {
            $this->runExport($params, $item['connection_info'], $item['file_info']);
        }
    }


    // case-insensitive first-has-precendence name to stream object lookup function;
    // returns index of correct object in $this->streams; -1 if not found
    private $stream_map = null;
    private function getStreamIdxFromName($name)
    {
        if ($this->stream_map === null)
        {
            $this->stream_map = [];
            $idx = 0;
            foreach ($this->streams as $stream)
            {
                $name = mb_strtolower($stream->getName());
                if (!array_key_exists($name, $this->stream_map))
                {
                    $this->stream_map[$name] = $idx;
                }
                ++$idx;
            }
        }

        $name = strtolower($name);
        if (array_key_exists($name, $this->stream_map))
            return $this->stream_map[$name];
             else
            return -1;
    }

    private function resolveOutputItems(array $params)
    {
        // if the items parameter isn't an array, the format is invalid
        $items = $params['items'] ?? false;

        if (!isset($params['items']))
        {
            $items = [];
            $stream_idx = 0;
            foreach ($this->streams as $stream)
            {
                $items[] = [ "name" => $stream->getName(), "path" => $stream->getName(), "stream_idx" => $stream_idx ];
                ++$stream_idx;
            }
        }

        if (!is_array($items))
            return false;

        $resolved_items = array();
        foreach ($items as $item)
        {
            // get the connection info
            $connection_info = $this->getConnectionInfoFromItem($params, $item);

            // get the file paths from the items
            $file_info_list = $this->getMatchingStreamPaths($item);

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

    private function getConnectionInfoFromItem(array $params, array $item)
    {
        if (isset($item['path']))
        {
            $path = $item['path'];

            // does the item's path contain a url?
            if (substr($path,0,7) == 'http://' || substr($path,0,8) == 'https://')
                return array('connection_type' => \Model::CONNECTION_TYPE_HTTP);
        }

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

    private function getMatchingStreamPaths(array $item) : array
    {
        $expanded_items = array();

        if (isset($item['stream_idx']))
            return [ $item ]; // already know the stream idx, return the input

        // clean the item info from any connection info that may be specified
        if (!isset($item['name']))
            return $expanded_items;

        $pattern = $item['name'];
        $path = $item['path'] ?? null;

        $stream_idx = 0;
        foreach ($this->streams as $stream)
        {
            if (\Flexio\Base\Util::matchPath($stream->getName(), $pattern, false) !== false)
            {
                $expanded_items[] = array("name" => $stream->getName(),
                                          "path" => isset($path) ? $path : $stream->getName(),
                                          "stream_idx" => $stream_idx);
            }

            ++$stream_idx;
        }

        return $expanded_items;
    }

    private function runExport(array $params, array $connection_info, array $file_info)
    {
        // STEP 1: make sure we have a destination connection and path
        if (!isset($connection_info['connection_type']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        if (!isset($file_info['stream_idx']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $instream = $this->streams[ $file_info['stream_idx'] ];

        // load the service
        $connection_type = $connection_info['connection_type'] ?? false;
        $service = \Flexio\Services\Store::load($connection_info);
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        // make sure we have a good output filename
        $output_filename = $file_info['path'];
        if (strlen($output_filename) == 0)
        {
            $output_filename = "flexio_" . date('Ymd_His') . "_" . sprintf("%03d", ($file_info['stream_idx'] + 1));
        }

        // make fully qualified path, if necessary
        $full_path = $params['location'] ?? '';
        if (strlen($full_path) > 0)
            $full_path .= '/';

        $full_path .= $output_filename;
        while (strpos($full_path, '//') !== false)
            $full_path = str_replace('//','/', $full_path);

        // $file_info contains two elements, 'name' as the name of the source stream,
        // and 'path' which contains the destination name
        $output_info = array();
        $output_info['connection_eid'] = $connection_info['eid'];
        $output_info['name'] = $full_path;

        // route the request based on the connection type
        switch ($connection_type)
        {
            case \Model::CONNECTION_TYPE_DOWNLOAD:
                return $this->runDownloadExport($instream, $service, $output_info);

            case \Model::CONNECTION_TYPE_MYSQL:
            case \Model::CONNECTION_TYPE_POSTGRES:
                $output_info['name'] = str_replace('/','_', $output_info['name']);
                return $this->runDatabaseExport($instream, $service, $output_info);

            case \Model::CONNECTION_TYPE_FTP:
            case \Model::CONNECTION_TYPE_SFTP:
            case \Model::CONNECTION_TYPE_DROPBOX:
            case \Model::CONNECTION_TYPE_GOOGLEDRIVE:
            case \Model::CONNECTION_TYPE_AMAZONS3:
                return $this->runRemoteFileExport($instream, $service, $output_info);

            case \Model::CONNECTION_TYPE_GOOGLESHEETS:
                $output_info['name'] = str_replace('/','_', $output_info['name']);
                return $this->runGoogleSheetsExport($instream, $service, $output_info);

            case \Model::CONNECTION_TYPE_MAILJET:
                $output_info['name'] = str_replace('/','_', $output_info['name']);
                return $this->runMailJetExport($instream, $service, $output_info);
        }
    }

    private function runDownloadExport(\Flexio\Object\Stream $instream, $service, array $output_info) // TODO: add parameter type
    {
        // note: don't do anything; the output stream was already passed on
    }

    private function runDatabaseExport(\Flexio\Object\Stream$instream, $service, array $output_info) // TODO: add parameter type
    {
        // get ready to read the input
        $streamreader = \Flexio\Object\StreamReader::create($instream);

        // get field names from structure
        $structure = $instream->getStructure()->enum();
        $field_names = array_column($structure, 'name');
        $table_name = $output_info['name'];

        if (!$service->createTable($table_name, $structure))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $inserter = $service->bulkInsert($table_name);
        if (!$inserter)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $result = $inserter->startInsert($field_names);

        while (true)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $result = $inserter->insertRow($row);
            if ($result === false)
            {
                $inserter->finishInsert();
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
            }
        }

        $inserter->finishInsert();
    }

    private function runRemoteFileExport(\Flexio\Object\Stream $instream, $service, array $output_info) // TODO: add parameter type
    {
        // get ready to read the input
        $streamreader = \Flexio\Object\StreamReader::create($instream);

        $params = array();
        $params['path'] = $output_info['name'];
        $params['content_type'] =  $instream->getMimeType();

        $service->write($params, function($length) use (&$streamreader) {
            return $streamreader->read($length);  // returns false upon EOF
        });
    }

    private function runGoogleSheetsExport(\Flexio\Object\Stream $instream, $service, array $output_info) // TODO: add parameter type
    {
        // get ready to read the input
        $streamreader = \Flexio\Object\StreamReader::create($instream);

        // create the output
        $outstream = self::createOutputStream($instream, $service, $output_info);
        // note: don't add to the output stream since the input was passed on
        // $this->getOutput()->push($outstream);

        $service = $outstream->getService();
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        $foldername = $outstream->getPath();
        $filename = $outstream->getName();
        if (strlen($foldername) > 0)
            $filename = $foldername . "/" . $filename;

        $spreadsheet = $service->createFile($filename);
        if (!$spreadsheet)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        if (count($spreadsheet->worksheets) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $inserter = $spreadsheet->worksheets[0];
        $flds = $instream->getStructure()->getNames();

        if (!$inserter->startInsert($flds))
        {
            // could not initialize inserter
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        // transfer the data
        while (true)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $result = $inserter->insertRow($row);
        }

        $inserter->finishInsert();
        $outstream->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_NONE); // external table
    }

    private function runMailJetExport(\Flexio\Object\Stream $instream, $service, array $output_info) // TODO: add parameter type
    {
        // get ready to read the input
        $streamreader = \Flexio\Object\StreamReader::create($instream);

        // create the output
        $outstream = self::createOutputStream($instream, $output_info);
        // note: don't add to the output stream since the input was passed on
        // $this->getOutput()->push($outstream);

        $service = $outstream->getService();
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        $input_structure = $instream->getStructure();

// TODO: make the inserter work more like inserting into a database

        // create the table
        $outputpath = $outstream->getPath();

        // transfer the data
        while (true)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            $result = $service->insertRow($outputpath, $row);
        }

        $service->close();
    }

    private function createOutputStream(\Flexio\Object\Stream $instream, array $output_info) : \Flexio\Object\Stream
    {
        // copy the input properties to the output, overwriting the
        // connection_eid and path properties with the connection item info
        $properties = $instream->get();
        $properties = array_merge($properties, $output_info);
        $outstream = \Flexio\Object\Stream::create($properties);
        return $outstream;
    }

    // job definition info
    const MIME_TYPE = 'flexio.output';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.output",
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
                "enum": ["flexio.output"]
            },
            "params": {
                "type": "object",
                "required": ["connection", "path"],
                "properties": {
                    "connection": {
                        "type": "object"
                    },
                    "path": {
                        "type": "string"
                    }
                }
            }
        }
    }
EOD;
}
