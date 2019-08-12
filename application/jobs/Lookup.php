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

class Lookup extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
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
        $lookup_values = $instream->getReader()->read();
        $lookup_values = @json_decode($lookup_values, true);
        if (!is_array($lookup_values))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid lookup values");

        // right now, assume the input comes from the stdin of a spreadsheet function,
        // so that the first element of the array contains the lookup values
        $lookup_values = $lookup_values[0];

        // read the input file and convert it into a table
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "read",
                "path" => $path
            ],
            [
                "op" => "convert",
                "input" => [
                    "format" => "delimited",
                    "header" => true
                ],
                "output" => [
                    "format" => "table"
                ]
            ]
        ]);

        $local_process = \Flexio\Jobs\Process::create();
        $local_process->setOwner($process->getOwner());
        $local_process->setStdin($instream);
        $local_process->execute($task);

        // create a lookup index from the input table
        $lookup_index = array();

        $local_stdout = $local_process->getStdout();
        $rows = \Flexio\Base\Util::getStreamContents($local_stdout);
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

        // get the lookup values for the input
        $lookup_result = array();
        $default_values = array_fill(0, count($return_columns), null);

        foreach ($lookup_values as $l)
        {
            $l = json_encode($l);

            if (!array_key_exists($l, $lookup_index))
                $lookup_result[] = $default_values;
             else
                $lookup_result[] = array_values(\Flexio\Base\Util::filterArray($lookup_index[$l], $return_columns));
        }

        $streamwriter = $outstream->getWriter();
        $streamwriter->write(json_encode($lookup_result));
        return;







        // convert the table to json, but do so manually so we can
        // handle large tables
        $streamwriter = $outstream->getWriter();

        // start the output
        $streamwriter->write("[");

        // write out the column names
        $column_names = $local_stdout->getStructure()->getNames();
        $streamwriter->write(json_encode($column_names));

        // write out each row
        $rows = \Flexio\Base\Util::getStreamContents($local_stdout);
        foreach ($rows as $r)
        {
            $row_values = array_values($r);

            $streamwriter->write(',');
            $streamwriter->write(json_encode($row_values));
        }

        // end the output
        $streamwriter->write(']');

        // set the content type
        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
    }
}
