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


class Process implements \Flexio\IFace\IProcess
{
    // note: the following constants may be stored in the database;
    // if they are changed, the database values need to be migrated

    const MODE_UNDEFINED  = '';
    const MODE_BUILD      = 'B';
    const MODE_RUN        = 'R';

    const STATUS_UNDEFINED = '';
    const STATUS_PENDING   = 'S'; // 'S' for 'Starting'
    const STATUS_WAITING   = 'W';
    const STATUS_RUNNING   = 'R';
    const STATUS_CANCELLED = 'X';
    const STATUS_PAUSED    = 'P';
    const STATUS_FAILED    = 'F';
    const STATUS_COMPLETED = 'C';

    const RESPONSE_NONE = 0;
    const RESPONSE_NORMAL = 200;

    const LOG_TYPE_UNDEFINED = '';
    const LOG_TYPE_SYSTEM    = 'P'; // 'P' for process
    const LOG_TYPE_USER      = 'U'; // 'U' for user

    // events are passed in a callback function along with info
    // to track info about the process
    const EVENT_STARTING       = 'process.starting';
    const EVENT_STARTING_TASK  = 'process.starting.task';
    const EVENT_FINISHED       = 'process.finished';
    const EVENT_FINISHED_TASK  = 'process.finished.task';

    private static $manifest = array(
        'calc'      => '\Flexio\Jobs\CalcField',
        'comment'   => '\Flexio\Jobs\Comment',
        'convert'   => '\Flexio\Jobs\Convert',
        'create'    => '\Flexio\Jobs\Create',
        'distinct'  => '\Flexio\Jobs\Distinct',
        'duplicate' => '\Flexio\Jobs\Duplicate',
        'each'      => '\Flexio\Jobs\Each',
        'echo'      => '\Flexio\Jobs\Echo1',
        'email'     => '\Flexio\Jobs\Email',
        'request'   => '\Flexio\Jobs\Request',
        'output'    => '\Flexio\Jobs\Output',
        'filter'    => '\Flexio\Jobs\Filter',
        'read'      => '\Flexio\Jobs\Read',
        'replace'   => '\Flexio\Jobs\Replace',
        'grep'      => '\Flexio\Jobs\Grep',
        'group'     => '\Flexio\Jobs\Group',
        'input'     => '\Flexio\Jobs\Input',
        'insert'    => '\Flexio\Jobs\Insert',
        'limit'     => '\Flexio\Jobs\Limit',
        'merge'     => '\Flexio\Jobs\Merge',
        'nop'       => '\Flexio\Jobs\Nop',
        'fail'      => '\Flexio\Jobs\Fail',
        'execute'   => '\Flexio\Jobs\Execute',
        'exit'      => '\Flexio\Jobs\Exit1',
        'rename'    => '\Flexio\Jobs\Rename',
        'render'    => '\Flexio\Jobs\Render',
        'search'    => '\Flexio\Jobs\Search',
        'select'    => '\Flexio\Jobs\Select',
        'settype'   => '\Flexio\Jobs\SetType',
        'sleep'     => '\Flexio\Jobs\Sleep',
        'sort'      => '\Flexio\Jobs\Sort',
        'task'      => '\Flexio\Jobs\Task',       // general task whose parameters are another task; for internal use only
        'transform' => '\Flexio\Jobs\Transform',
        'list'      => '\Flexio\Jobs\List1',
        'write'     => '\Flexio\Jobs\Write',
        'report'    => '\Flexio\Jobs\Report',
        'set'       => '\Flexio\Jobs\Set'
    );

    private $metadata;      // array of optional metadata info that can be used for passing info (such as info from the calling context) across callbacks
    private $tasks;         // array of tasks to process; tasks are popped off the list; when there are no tasks left, the process is done
    private $params;        // variables that are used in the processing (array of \Flexio\Base\Stream objects)
    private $stdin;
    private $stdout;
    private $response_code;
    private $error;
    private $status_info;
    private $handlers;     // array of callbacks invoked for each event
    private $files;        // array of streams of files (similar to php's $_FILES)

    public function __construct()
    {
        $this->metadata = array();
        $this->tasks = array();
        $this->params = array();

        $this->stdin = self::createStream();
        $this->stdout =  self::createStream();

        $this->response_code = self::RESPONSE_NONE;
        $this->error = array();
        $this->handlers = array();
        $this->files = array();
    }

    public static function create() : \Flexio\Jobs\Process
    {
        $object = new static();
        return $object;
    }

    public function addEventHandler($handler) : \Flexio\Jobs\Process
    {
        $this->handlers[] = $handler;
        return $this;
    }

    public function setMetadata(array $metadata) : \Flexio\Jobs\Process
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getMetadata() : array
    {
        return $this->metadata;
    }

    public function setTasks(array $tasks) : \Flexio\Jobs\Process
    {
        $this->tasks = $tasks;
        return $this;
    }

    public function getTasks() : array
    {
        return $this->tasks;
    }

    public function setParams(array $arr) : \Flexio\Jobs\Process
    {
        foreach ($arr as $k => &$v)
        {
            if (!($v instanceof \Flexio\Base\Stream))
            {
                $stream = \Flexio\Base\Stream::create();
                $stream->buffer = (string)$v;     // shortcut to speed it up -- can also use getWriter()->write((string)$v)
                $v = $stream;
            }
        }

        $this->params = $arr;
        return $this;
    }

    public function getParams() : array
    {
        return $this->params;
    }

    public function addFile(string $name, \Flexio\IFace\IStream $stream) : \Flexio\Jobs\Process
    {
        $this->files[$name] = $stream;
        return $this;
    }

    public function getFiles() : array
    {
        return $this->files;
    }

    public function setStdin(\Flexio\IFace\IStream $stream) : \Flexio\Jobs\Process
    {
        $this->stdin = $stream;
        return $this;
    }

    public function getStdin() : \Flexio\IFace\IStream
    {
        return $this->stdin;
    }

    public function setStdout(\Flexio\IFace\IStream $stream) : \Flexio\Jobs\Process
    {
        $this->stdout = $stream;
        return $this;
    }

    public function getStdout() : \Flexio\IFace\IStream
    {
        return $this->stdout;
    }

    public function setResponseCode(int $code) : \Flexio\Jobs\Process
    {
        $this->response_code = $code;
        return $this;
    }

    public function getResponseCode() : int
    {
        return $this->response_code;
    }

    public function setError(string $code = '', string $message = null, string $module = null, int $line = null, string $type = null, array $trace = null)  : \Flexio\Jobs\Process
    {
        if (!isset($message))
        $message = \Flexio\Base\Error::getDefaultMessage($code);

        $this->error = array('code' => $code, 'message' => $message);

        if ($module !== null)
        {
            $this->error['module'] = $module;
            if ($line !== null)
                $this->error['line'] = $line;
        }

        if ($type !== null)
            $this->error['type'] = $type;

        if ($trace !== null)
            $this->error['trace'] = $trace;

        return $this;
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

    public function execute() : \Flexio\IFace\IProcess
    {
        // fire the starting event
        $this->invokeEventHandlers(self::EVENT_STARTING, $this->getProcessState($current_task));

        // if we don't have any tasks, simply move the stdin to the stdout;
        // otherwise, process the tasks
        if (count($this->tasks) === 0)
            $this->stdout = $this->stdin;
             else
            $this->executeAllTasks();

        // fire the finish event
        $this->invokeEventHandlers(self::EVENT_FINISHED, $this->getProcessState($current_task));
        return $this;
    }

    private function executeAllTasks()
    {
        $first = true;
        foreach ($this->tasks as $current_task)
        {
            // if there's an error, stop the process
            if ($this->hasError())
                break;

            // if the process was exited intentionally, stop the process
            $response_code = $this->getResponseCode();
            if ($response_code !== self::RESPONSE_NONE)
                break;

            // signal the start of the task
            $this->invokeEventHandlers(self::EVENT_STARTING_TASK, $this->getProcessState($current_task));

            if ($first === false)
            {
                // copy the stdout of the last task to the stdin; make a new stdout
                $this->stdin = $this->stdout;
                $this->stdout = self::createStream();
            }

            // execute the task
            $this->executeTask($current_task);
            $first = false;

            // signal the end of the task
            $this->invokeEventHandlers(self::EVENT_FINISHED_TASK, $this->getProcessState($current_task));
        }
    }

    private function getProcessState(array $current_task) : array
    {
        $state = array();
        $state['stdin'] = $this->getStdin();
        $state['stdout'] = $this->getStdout();
        $state['current_task'] = $current_task;
        return $state;
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
            $debug = IS_DEBUG();
            $info = $e->getMessage(); // exception info is packaged up in message
            $info = json_decode($info,true);
            $module = $debug ? $e->getFile() : \Flexio\Base\Util::safePrintCodeFilename($e->getFile());
            $line = $e->getLine();
            $trace = $debug ? $e->getTrace() : null;
            $code = $info['code'];
            $message = $info['message'];
            $type = 'flexio exception';
            $this->setError($code, $message, $module, $line, $type, $trace);
        }
        catch (\Exception $e)
        {
            $debug = IS_DEBUG();
            $module = $debug ? $e->getFile() : \Flexio\Base\Util::safePrintCodeFilename($e->getFile());
            $line = $e->getLine();
            $trace = $debug ? $e->getTrace() : null;
            $type = 'system exception';
            $this->setError(\Flexio\Base\Error::GENERAL, '', $module, $line, $type, $trace);
        }
        catch (\Error $e)
        {
            $debug = IS_DEBUG();
            $module = $debug ? $e->getFile() : \Flexio\Base\Util::safePrintCodeFilename($e->getFile());
            $line = $e->getLine();
            $trace = $debug ? $e->getTrace() : null;
            $type = 'system error';
            $this->setError(\Flexio\Base\Error::GENERAL, '', $module, $line, $type, $trace);
        }
    }

    private static function createTask(array $task) : \Flexio\IFace\IJob
    {
        if (!isset($task['op']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $operation = $task['op'];

        // make sure the job is registered; note: this isn't strictly necessary,
        // but gives us a convenient way of limiting what jobs are available for
        // processing
        $job_class_name = self::$manifest[$operation] ?? false;
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

    private function invokeEventHandlers(string $event, array $process_info)
    {
        foreach ($this->handlers as $handler)
        {
            call_user_func($handler, $event, $process_info);
        }
    }

    private static function createStream() : \Flexio\IFace\IStream
    {
        $stream = \Flexio\Base\Stream::create();
        $stream->setMimeType(\Flexio\Base\ContentType::TEXT); // default mime type
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
        $task_operation = $task['op'] ?? false;
        $task_parameters = $task['params'] ?? false;

        // make sure we have the params we need
        if (is_string($task_operation) === false || is_array($task_parameters) === false)
            return '';

        $encoded_task_parameters = json_encode($task_parameters);
        $hash = md5(
            $implementation_version .
            $task_operation .
            $encoded_task_parameters
        );

        return $hash;
    }
}
