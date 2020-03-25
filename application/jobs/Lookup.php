<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-08-08
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "lookup",     // string, required
    "path": "",         // string, required
    "lookup_keys: [],   // array of strings
    "return_columns: [] // array of strings
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'             => array('required' => true,  'enum' => ['lookup']),
        'path'           => array('required' => true,  'type' => 'string'),
        'lookup_keys'    => array('required' => true,  'type' => 'array'),
        'return_columns' => array('required' => true,  'type' => 'array')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Lookup implements \Flexio\IFace\IJob
{
    private $properties = array();

    public static function validate(array $task) : array
    {
        $errors = array();
        return $errors;
    }

    public static function run(\Flexio\IFace\IProcess $process, array $task) : void
    {
        unset($task['op']);
        \Flexio\Jobs\Util::replaceParameterTokens($process, $task);

        $object = new static();
        $object->properties = $task;
        $object->run_internal($process);
    }

    private function run_internal(\Flexio\IFace\IProcess $process) : void
    {
        $job_params = $this->getJobParameters();
        $path = $job_params['path'] ?? null;
        $lookup_keys = $job_params['lookup_keys'] ?? null;
        $return_columns = $job_params['return_columns'] ?? null;

        // basic input validation; TODO: check individual array values to make sure they're strings
        if (!is_string($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid parameter 'path'");
        if (!is_array($lookup_keys))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid parameter 'lookup_keys'");
        if (!is_array($return_columns))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid parameter 'return columns'");

        // get the input/output
        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        // make sure the lookup keys and return columns are both lowercase
        $lookup_keys = array_map('strtolower', $lookup_keys);
        $return_columns = array_map('strtolower', $return_columns);

        // get the lookup values from stdin; should be array of array values
        // e.g. ["apple", "red"]
        $lookup_values = $instream->getReader()->read();
        $lookup_values = @json_decode($lookup_values, true);
        if (!is_array($lookup_values))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid lookup values");

        // get the lookup table from the path
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "read",
                "path" => $path
            ],
            [
                "op" => "convert",
                "input" => [
                    //"format" => "delimited",
                    //"header" => true
                ],
                "output" => [
                    "format" => "table"
                ]
            ]
        ]);

        $inner_process = \Flexio\Jobs\Process::create();
        $inner_process->setOwner($process->getOwner());
        $inner_process->execute($task);
        $inner_process_output = $inner_process->getStdout();

        // create a lookup index from the table
        $lookup_index = array();
        $rows = \Flexio\Base\StreamUtil::getStreamContents($inner_process_output);

        foreach ($rows as $r)
        {
            $lookup_row = array_change_key_case($r, CASE_LOWER);
            $key = array();
            foreach ($lookup_keys as $k)
            {
                if (!array_key_exists($k, $lookup_row))
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid lookup key");

                $key[] = $lookup_row[$k];
            }

            $lookup_index[json_encode($key)] = $lookup_row;
        }

        // TODO: array version that allows multiple lookups from one input param
        // // get the lookup values for the input
        //$lookup_values = lookup_values[0];

        // $lookup_result = array();
        // $default_values = array_fill(0, count($return_columns), null);

        // foreach ($lookup_values as $l)
        // {
        //     $l = json_encode($l);
        //     if (!array_key_exists($l, $lookup_index))
        //         $lookup_result[] = $default_values;
        //      else
        //         $lookup_result[] = array_values(\Flexio\Base\Util::filterArray($lookup_index[$l], $return_columns));
        // }

        // single result lookup version
        $lookup_values = json_encode($lookup_values);
        $default_values = array_fill(0, count($return_columns), null);
        if (!array_key_exists($lookup_values, $lookup_index))
            $lookup_result[] = $default_values;
            else
            $lookup_result[] = array_values(\Flexio\Base\Util::filterArray($lookup_index[$lookup_values], $return_columns));

        $streamwriter = $outstream->getWriter();
        $streamwriter->write(json_encode($lookup_result));
        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
    }

    private function getStreamFromFile(string $owner, string $path) : \Flexio\Base\Stream
    {
        // TODO: connection mounts in connection object contain a similar caching
        // mechanism; should factor

        // get the connection identifier and remote path from the given path
        $connection_identifier = '';
        $remote_path = '';
        $vfs = new \Flexio\Services\Vfs($owner);
        $service = $vfs->getServiceFromPath($path, $connection_identifier, $remote_path);

        // get the file info
        $connection_eid = \Flexio\Object\Connection::getEidFromName($owner, $connection_identifier);
        $file_info = $service->getFileInfo($remote_path);

        // generate a handle for the content signature that will uniquely identify it;
        // use the owner plus a hash of some identifiers that constitute unique content
        $content_handle = '';
        $content_handle .= $owner; // include owner in the identifier so that even if the connection owner changes (later?), the cache will only exist for this owner
        $content_handle .= $file_info['hash']; // not always populated, so also add on info from the file
        $content_handle .= md5(
            $remote_path .
            strval($file_info['size']) .
            $file_info['modified']
        );

        // get the cached content; if it doesn't exist, create the cache
        $stored_stream = \Flexio\Object\Factory::getStreamContentCache($connection_eid, $content_handle);
        if (!isset($stored_stream))
            $stored_stream = \Flexio\Object\Factory::createStreamContentCache($connection_eid, $remote_path, $content_handle);

        // copy the stream contents to a memory stream
        $memory_stream = \Flexio\Base\Stream::create();

        $streamreader = $stored_stream->getReader();
        $streamwriter = $memory_stream->getWriter();

        while (($data = $streamreader->read(32768)) !== false)
            $streamwriter->write($data);

        return $memory_stream;
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}
