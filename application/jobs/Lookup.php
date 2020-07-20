<?php
/**
 *
 * Copyright (c) 2019, Flex Research LLC. All rights reserved.
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

        // read the file to get the info; TODO: use cache?
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->setOwner($process->getOwner());
        $process_engine->queue('\Flexio\Jobs\Read::run', array('path' => $path));
        $process_engine->queue('\Flexio\Jobs\ProcessHandler::chain', array());
        $process_engine->queue('\Flexio\Jobs\Convert::run', array('input' => array(), 'output' => array('format' => 'ndjson')));
        $process_engine->run();

        if ($process_engine->hasError())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // create a lookup index from the table
        // TODO: lookups used to allow a range of lookup values, in which case creating
        // a lookup table made sense; however, lookup syntax with multiple lookup was
        // deemed complicated, so function was converted to a single lookup; with single
        // lookup, building a lookup table is inefficient since all values need to be
        // read through first; should simply scan table and return first corresponding
        // value
        $lookup_index = array();

        $idx = 0;
        $limit = PHP_INT_MAX; // placeholder for limit if desired

        $reader = $process_engine->getStdout()->getReader();
        while (true)
        {
            if ($idx >= $limit)
                break;

            $item = $reader->readline();
            if ($item === false)
                break;

            // get the key/value info for the rows
            $lookup_row = json_decode($item, true);
            $lookup_row = array_change_key_case($lookup_row, CASE_LOWER);

            $key = array();
            foreach ($lookup_keys as $k)
            {
                if (!array_key_exists($k, $lookup_row))
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid lookup key");

                $key[] = $lookup_row[$k];
            }

            $lookup_index[json_encode($key)] = $lookup_row;
        }

        // return the value from the lookup table
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

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}
