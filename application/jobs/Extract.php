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
    "op": "extract",  // string, required
    "path": ""       // string, required
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['extract'])
        'path'       => array('required' => true,  'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Extract extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        $job_params = $this->getJobParameters();
        $path = $job_params['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing parameter 'path'");

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

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

        $local_stdout = $local_process->getStdout();

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




