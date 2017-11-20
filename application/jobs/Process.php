<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-10
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Process
{
    private static $manifest = array(
        'flexio.calc'      => '\Flexio\Jobs\CalcField',
        'flexio.comment'   => '\Flexio\Jobs\Comment',
        'flexio.convert'   => '\Flexio\Jobs\Convert',
        'flexio.create'    => '\Flexio\Jobs\Create',
        'flexio.distinct'  => '\Flexio\Jobs\Distinct',
        'flexio.duplicate' => '\Flexio\Jobs\Duplicate',
        'flexio.each'      => '\Flexio\Jobs\Each',
        'flexio.echo'      => '\Flexio\Jobs\Echo1',
        'flexio.email'     => '\Flexio\Jobs\Email',
        'flexio.request'   => '\Flexio\Jobs\Request',
        'flexio.output'    => '\Flexio\Jobs\Output',
        'flexio.filter'    => '\Flexio\Jobs\Filter',
        'flexio.replace'   => '\Flexio\Jobs\Replace',
        'flexio.grep'      => '\Flexio\Jobs\Grep',
        'flexio.group'     => '\Flexio\Jobs\Group',
        'flexio.input'     => '\Flexio\Jobs\Input',
        'flexio.limit'     => '\Flexio\Jobs\Limit',
        'flexio.merge'     => '\Flexio\Jobs\Merge',
        'flexio.nop'       => '\Flexio\Jobs\Nop',
        'flexio.fail'      => '\Flexio\Jobs\Fail',
        'flexio.execute'   => '\Flexio\Jobs\Execute',
        'flexio.exit'      => '\Flexio\Jobs\Exit1',
        'flexio.rename'    => '\Flexio\Jobs\Rename',
        'flexio.render'    => '\Flexio\Jobs\Render',
        'flexio.search'    => '\Flexio\Jobs\Search',
        'flexio.select'    => '\Flexio\Jobs\Select',
        'flexio.settype'   => '\Flexio\Jobs\SetType',
        'flexio.sleep'     => '\Flexio\Jobs\Sleep',
        'flexio.sort'      => '\Flexio\Jobs\Sort',
        'flexio.transform' => '\Flexio\Jobs\Transform',
        'flexio.list'      => '\Flexio\Jobs\List1'
    );

    private $tasks = array();
    private $input = array();
    private $output = array();

    public static function create() : \Flexio\Jobs\Process
    {
        $object = new static();
        return $object;
    }

    public function setTasks(array $tasks)
    {
        $this->tasks = $tasks;
    }

    public function getTasks() : array
    {
        return $this->tasks;
    }

    public function setInput(array $input)
    {
        $this->input = $input;
    }

    public function getInput() : array
    {
        return $this->input;
    }

    public function setOutput(array $output)
    {
        $this->output = $output;
    }

    public function getOutput() : array
    {
        return $this->output;
    }

    public function execute(callable $start, callable $finish)
    {
        // TODO: set appropriate status for failures

        // set initial job status
        $process_params = array();
        $process_params['started'] = self::getProcessTimestamp();
        $process_params['process_status'] = \Model::PROCESS_STATUS_RUNNING;
        $start($process_params);


        // STEP 1: get the process properties
        $process_tasks = $this->getTasks();


        // STEP 3: create the initial process context; make sure stdin/stdout are initialized
        // and initialize the context variables
        $process_input = $current_process_properties['input'];
        $context = \Flexio\Object\Context::fromString($process_input);

        $stdin = $context->getStdin();
        if (!isset($stdin))
        {
            $stdin = \Flexio\Object\StreamMemory::create();
            $stdin->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
            $context->setStdin($stdin);
        }

        $stdout = $context->getStdout();
        if (!isset($stdout))
        {
            $stdout = \Flexio\Object\StreamMemory::create();
            $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
            $context->setStdout($stdout);
        }

        $environment_variables = $this->getEnvironmentParams();
        $user_variables = $context->getParams();
        $variables = array_merge($user_variables, $environment_variables);
        $context->setParams($variables);

        // STEP 4: iterate through the tasks and run each one
        $first_task = true;
        foreach ($process_tasks as $task)
        {
            if ($first_task === false)
            {
                // set the stdin for the next job step to be the output from the stdout
                // of the step just executed and create a new stdout
                $context->setStdin($context->getStdout());
                $stdout = \Flexio\Object\StreamMemory::create();
                $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
                $context->setStdout($stdout);
            }

            // execute the step
            $log_eid = $this->startLog($task, $context);  // TODO: only log if in debug mode?
            $this->executeTask($task, $context);
            $this->finishLog($log_eid, $task, $context); // TODO: only log if in debug mode?
            $first_task = false;

            // if the step failed, stop the job
            if ($this->hasError())
                break;

            // if the step exited, stop the job
            $response_code = $context->getExitCode();
            if ($response_code !== null)
            {
                $this->response_code = $response_code;
                break;
            }
        }

        // save final job output and status
        $process_params = array();
        $process_params['output'] = \Flexio\Object\Context::toString($context);
        $process_params['finished'] = self::getProcessTimestamp();
        $process_params['process_status'] = $this->hasError() ? \Model::PROCESS_STATUS_FAILED : \Model::PROCESS_STATUS_COMPLETED;
        $process_params['cache_used'] = 'N';
        $finish($process_params);
    }

    private function executeTask(array $task, \Flexio\Object\Context &$context)
    {
        if (!IS_PROCESSTRYCATCH())
        {
            // during debugging, sometimes try/catch needs to be turned
            // of completely; this switch is implemented here and in Api.php
            $job = \Flexio\Jobs\Factory::create($task);
            $job->run($context);
            return;
        }

        try
        {
            // create the job with the task; set the job input, run the job, and get the output
            $job = \Flexio\Jobs\Factory::create($task);
            $job->run($context);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $info = $e->getMessage(); // exception info is packaged up in message
            $info = json_decode($info,true);
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            $code = $info['code'];
            $message = $info['message'];
            $type = 'flexio exception';
            $this->fail($code, $message, $file, $line, $type, $trace);
        }
        catch (\Exception $e)
        {
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            $type = 'php exception';
            $this->fail(\Flexio\Base\Error::GENERAL, '', $file, $line, $type, $trace);
        }
        catch (\Error $e)
        {
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            $type = 'php error';
            $this->fail(\Flexio\Base\Error::GENERAL, '', $file, $line, $type, $trace);
        }
    }

    private static function createTask(array $task) : \Flexio\Jobs\IJob
    {
        if (!isset($task['type']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $job_type = $task['type'];

        // make sure the job is registered; note: this isn't strictly necessary,
        // but gives us a convenient way of limiting what jobs are available for
        // processing
        $job_class_name = self::$manifest[$job_type] ?? false;
        if ($job_class_name === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // try to find the job file
        $class_name_parts = explode("\\", $job_class_name);
        if (!isset($class_name_parts[3]))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $job_class_file = \Flexio\System\System::getApplicationDirectory() . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $class_name_parts[3] . '.php';
        if (!@file_exists($job_class_file))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // load the job's php file and instantiate the job object
        include_once $job_class_file;
        $job = $job_class_name::create($task);

        if ($job === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        return $job;
    }
}
