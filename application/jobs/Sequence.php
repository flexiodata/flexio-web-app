<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-12-27
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "sequence",
    "items": []
    // TODO: fill out
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['sequence'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Sequence implements \Flexio\IFace\IJob
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

        // don't do parameter replace on the task because it will do
        // this on the entire sequence before variables are set/evaluated
        //\Flexio\Jobs\Base::replaceParameterTokens($process, $task);

        $object = new static();
        $object->properties = $task;
        $object->run_internal($process);
    }

    private function run_internal(\Flexio\IFace\IProcess $process) : void
    {
        $job_params = $this->getJobParameters();
        $job_sequence_tasks = $job_params['items'] ?? false;

        if ($job_sequence_tasks === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $first_execute = true;
        foreach ($job_sequence_tasks as $task)
        {
            if ($first_execute === false)
            {
                // copy the stdout of the last task to the stdin; make a new stdout
                $new_stdout_stream = \Flexio\Base\Stream::create()->setMimeType(\Flexio\Base\ContentType::TEXT);
                $process->setStdin($process->getStdout());
                $process->setStdout($new_stdout_stream);
            }

            // execute the task
            $process->execute($task);
            $first_execute = false;
        }
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}
