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
    "op": "extract",  // string, required
    "path": ""        // string, required
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['extract']),
        'path'       => array('required' => true,  'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Extract implements \Flexio\IFace\IJob
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
        // TODO: this function is very similar to the vfs info job; factor?

        $job_params = $this->getJobParameters();
        $path = $job_params['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing parameter 'path'");

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        // read the file to get the info; TODO: use cache?
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->setOwner($process->getOwner());
        $process_engine->queue('\Flexio\Jobs\Read::run', array('path' => $path));
        $process_engine->queue('\Flexio\Jobs\ProcessHandler::chain', array());
        $process_engine->queue('\Flexio\Jobs\Convert::run', array('input' => array(), 'output' => array('format' => 'ndjson')));
        $process_engine->run();

        if ($process_engine->hasError())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $local_stdout = $process_engine->getStdout();

        // convert the table to json, but do so manually so we can
        // handle large tables
        $streamwriter = $outstream->getWriter();

        // start the output
        $streamwriter->write("[");

        // write out the column names
        $column_names = $local_stdout->getStructure()->getNames();
        $streamwriter->write(json_encode($column_names));

        // write out each row
        $idx = 0;
        $limit = PHP_INT_MAX; // placeholder for limit if desired
        $reader = $local_stdout->getReader();
        $column_names = array_flip($column_names);
        while (true)
        {
            if ($idx >= $limit)
                break;

            $item = $reader->readRow();
            if ($item === false)
                break;

            // get the key/value info for the rows
            $item = json_decode($item, true);

            // get the values corresponding to the headers
            $item_values = array_values(\Flexio\Base\Util::mapArray($column_names, $item));
            $streamwriter->write(',');
            $streamwriter->write(json_encode($item_values));
            $idx++;
        }

        // end the output
        $streamwriter->write(']');

        // set the content type
        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}




