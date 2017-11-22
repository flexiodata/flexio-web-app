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


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Process implements \Flexio\Jobs\IProcess
{
    // events are passed in a callback function along with info
    // to track info about the process
    const EVENT_PROCESS_STARTING       = 'process.starting';
    const EVENT_PROCESS_STARTING_TASK  = 'process.starting.task';
    const EVENT_PROCESS_FINISHED       = 'process.finished';
    const EVENT_PROCESS_FINISHED_TASK  = 'process.finished.task';

    const PROCESS_RESPONSE_NONE = 0;
    const PROCESS_RESPONSE_NORMAL = 200;

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

    private $metadata;      // array of optional metadata info that can be used for passing info (such as info from the calling context) across callbacks
    private $tasks;         // array of tasks to process; tasks are popped off the list; when there are no tasks left, the process is done
    private $params;        // variables that are used in the processing
    private $streams;       // additional streams that will be process by jobs
    private $stdin;
    private $stdout;
    private $response_code;
    private $error;
    private $status_info;

    public function __construct()
    {
        $this->metadata = array();
        $this->tasks = array();
        $this->params = array();
        $this->streams = array();

        $this->stdin = self::createStreamMemory();
        $this->stdout =  self::createStreamMemory();

        $this->response_code = self::PROCESS_RESPONSE_NONE;
        $this->error = array();
        $this->status_info = array();
    }

    public static function create() : \Flexio\Jobs\Process
    {
        $object = new static();
        return $object;
    }

    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getMetadata() : array
    {
        return $this->metadata;
    }

    public function addTasks(array $tasks)
    {
        $this->tasks = array_merge($this->tasks, $tasks);
    }

    public function getTasks() : array
    {
        return $this->tasks;
    }

    public function setParams(array $arr)
    {
        $this->params = $arr;
    }

    public function getParams() : array
    {
        return $this->params;
    }

    public function getStdin() : \Flexio\Base\IStream
    {
        return $this->stdin;
    }

    public function getStdout() : \Flexio\Base\IStream
    {
        return $this->stdout;
    }

    public function setResponseCode(int $code)
    {
        $this->response_code = $code;
    }

    public function getResponseCode() : int
    {
        return $this->response_code;
    }

    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null)
    {
        if (!isset($message))
        $message = \Flexio\Base\Error::getDefaultMessage($code);

        $this->error = array('code' => $code, 'message' => $message, 'file' => $file, 'line' => $line, 'type' => $type, 'trace' => $trace);
    }

    public function getError() : array
    {
        return $this->error;
    }

    public function hasError() : bool
    {
        if (empty($this->error))
            return false;

        return true;
    }

    public function getStatusInfo() : array
    {
        return $this->status_info;
    }

    public function execute($func = null)
    {
        // fire the starting event
        $this->signal(self::EVENT_PROCESS_STARTING, $func);

        // if we don't have any tasks, simply move the stdin to the stdout;
        // otherwise, process the tasks
        if (count($this->tasks) === 0)
            $this->stdout = $this->stdin;
             else
            $this->executeAllTasks($func);

        // fire the finish event
        $this->signal(self::EVENT_PROCESS_FINISHED, $func);
    }

    private function executeAllTasks($func = null)
    {
        $first = true;
        while (true)
        {
            // reset the status info
            $this->status_info = array();

            // get the next task to process
            $current_task = array_shift($this->tasks);
            if (!isset($current_task))
                break;

            // set the current status info
            $this->status_info['current_task'] = $current_task;

            // signal the start of the task
            $this->signal(self::EVENT_PROCESS_STARTING_TASK, $func);

            if ($first === false)
            {
                // copy the stdout of the last task to the stdin; make a new stdout
                $this->stdin = $this->stdout;
                $this->stdout = self::createStreamMemory();
            }

            // execute the task
            $this->executeTask($current_task);
            $first = false;

            // signal the end of the task
            $this->signal(self::EVENT_PROCESS_FINISHED_TASK, $func);

            // if there's an error, stop the process
            if ($this->hasError())
                break;

            // if the process was exited intentionally, stop the process
            $response_code = $this->getResponseCode();
            if ($response_code !== self::PROCESS_RESPONSE_NONE)
                break;
        }
    }

    private function executeTask(array $task)
    {
        if (!IS_PROCESSTRYCATCH())
        {
            // during debugging, sometimes try/catch needs to be turned
            // of completely; this switch is implemented here and in Api.php
            $job = self::createTask($task);
            $job->run($this);
            return;
        }

        try
        {
            // create the job with the task; set the job input, run the job, and get the output
            $job = self::createTask($task);
            $job->run($this);
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
            $this->setError($code, $message, $file, $line, $type, $trace);
        }
        catch (\Exception $e)
        {
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            $type = 'php exception';
            $this->setError(\Flexio\Base\Error::GENERAL, '', $file, $line, $type, $trace);
        }
        catch (\Error $e)
        {
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTrace();
            $type = 'php error';
            $this->setError(\Flexio\Base\Error::GENERAL, '', $file, $line, $type, $trace);
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

    private function signal(string $event, $func = null)
    {
        if (!isset($func))
            return;

        call_user_func($func, $event, $this);
    }

    private static function createStreamMemory() : \Flexio\Base\IStream
    {
        $stream = \Flexio\Base\StreamMemory::create();
        $stream->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
        return $stream;
    }

    private static function generateTaskHash(string $implementation_version, array $task) : string
    {
        // note: currently, this isn't used, but it's here for reference in case we
        // need a way of referencing looking up tasks from a string

        // task hash should uniquely identify the result; use
        // a hash of:
        //     1. implementation version (git version)
        //     2. task type
        //     3. task parameters
        // leave out the job name or other identifiers, such as the
        // the task eid

        // if we dont' have an implementation version or an invalid implementation
        // version (git revision), don't cache anything
        if (strlen($implementation_version) < 40)
            return '';

        // make sure have a valid task
        $task_type = $task['type'] ?? false;
        $task_parameters = $task['params'] ?? false;

        // make sure we have the params we need
        if (is_string($task_type) === false || is_array($task_parameters) === false)
            return '';

        $encoded_task_parameters = json_encode($task_parameters);
        $hash = md5(
            $implementation_version .
            $task_type .
            $encoded_task_parameters
        );

        return $hash;
    }
}
