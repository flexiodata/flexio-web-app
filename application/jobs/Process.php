<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
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

    public const MODE_UNDEFINED  = '';
    public const MODE_BUILD      = 'B';
    public const MODE_RUN        = 'R';

    public const STATUS_UNDEFINED = '';
    public const STATUS_PENDING   = 'S'; // 'S' for 'Starting'
    public const STATUS_WAITING   = 'W';
    public const STATUS_RUNNING   = 'R';
    public const STATUS_CANCELLED = 'X';
    public const STATUS_PAUSED    = 'P';
    public const STATUS_FAILED    = 'F';
    public const STATUS_COMPLETED = 'C';

    public const RESPONSE_NONE = 0;
    public const RESPONSE_NORMAL = 200;

    // events are passed in a callback function along with info
    // to track info about the process
    public const EVENT_STARTING       = 'process.starting';
    public const EVENT_STARTING_TASK  = 'process.starting.task';
    public const EVENT_FINISHED       = 'process.finished';
    public const EVENT_FINISHED_TASK  = 'process.finished.task';

    private static $manifest = array(
        'archive'   => '\Flexio\Jobs\Archive',
        'calc'      => '\Flexio\Jobs\CalcField',
        'connect'   => '\Flexio\Jobs\Connect',
        'convert'   => '\Flexio\Jobs\Convert',
        'copy'      => '\Flexio\Jobs\Copy',
        'create'    => '\Flexio\Jobs\Create',
        'delete'    => '\Flexio\Jobs\Delete',
        'dump'      => '\Flexio\Jobs\Dump',
        'echo'      => '\Flexio\Jobs\Echo1',
        'email'     => '\Flexio\Jobs\Email',
        'execute'   => '\Flexio\Jobs\Execute',
        'exit'      => '\Flexio\Jobs\Exit1',
        'fail'      => '\Flexio\Jobs\Fail',
        'filter'    => '\Flexio\Jobs\Filter',
        'for'       => '\Flexio\Jobs\Foreach1',
        'foreach'   => '\Flexio\Jobs\Foreach1',
        'grep'      => '\Flexio\Jobs\Grep',
        'insert'    => '\Flexio\Jobs\Insert',
        'limit'     => '\Flexio\Jobs\Limit',
        'list'      => '\Flexio\Jobs\List1',
        'merge'     => '\Flexio\Jobs\Merge',
        'mkdir'     => '\Flexio\Jobs\Mkdir',
        'read'      => '\Flexio\Jobs\Read',
        'rename'    => '\Flexio\Jobs\Rename',
        'render'    => '\Flexio\Jobs\Render',
        'replace'   => '\Flexio\Jobs\Replace',
        'report'    => '\Flexio\Jobs\Report',
        'request'   => '\Flexio\Jobs\Request',
        'select'    => '\Flexio\Jobs\Select',
        'sequence'  => '\Flexio\Jobs\Sequence',
        'set'       => '\Flexio\Jobs\Set',
        'settype'   => '\Flexio\Jobs\SetType',
        'sleep'     => '\Flexio\Jobs\Sleep',
        'transform' => '\Flexio\Jobs\Transform',
        'unarchive' => '\Flexio\Jobs\Unarchive',
        'write'     => '\Flexio\Jobs\Write'
    );

    private $owner_eid;     // user eid of the owner of the process
    private $params;        // variables that are used in the processing (array of \Flexio\Base\Stream objects)
    private $stdin;
    private $stdout;
    private $response_code;
    private $error;
    private $stop;
    private $handlers;     // array of callbacks invoked for each event
    private $files;        // array of streams of files (similar to php's $_FILES)
    private $first_execute;
    private $local_connections = [];   // map from connection identifier to connection_properties...e.g. [ 'connection_type' => ''. 'connection_properties' => [...]]
    private $local_files = [];         // map from file number to Stream object

    public function __construct()
    {
        $this->owner_eid = '';
        $this->params = array();
        $this->stdin = self::createStream();
        $this->stdout =  self::createStream();

        $this->response_code = self::RESPONSE_NONE;
        $this->error = array();
        $this->handlers = array();
        $this->files = array();
        $this->stop = false;
        $this->first_execute = true;
    }

    public static function create() : \Flexio\Jobs\Process
    {
        $object = new static();
        return $object;
    }

    public static function createTask(array $task) : \Flexio\IFace\IJob
    {
        if (!isset($task['op']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _('Missing operation \'op\' task parameter'));

        $operation = $task['op'];

        if (!is_string($operation))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _('Invalid operation \'op\' task parameter'));

        // make sure the job is registered; note: this isn't strictly necessary,
        // but gives us a convenient way of limiting what jobs are available for
        // processing
        $job_class_name = self::$manifest[$operation] ?? false;
        if ($job_class_name === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _('Invalid operation \'op\' task parameter'));

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

    public function addEventHandler($handler) : \Flexio\Jobs\Process
    {
        $this->handlers[] = $handler;
        return $this;
    }

    public function setOwner(string $owner_eid) : \Flexio\Jobs\Process
    {
        $this->owner_eid = $owner_eid;
        return $this;
    }

    public function getOwner() : string
    {
        return $this->owner_eid;
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

    // for local files (using file number handle)
    public function setLocalFile(int $fileno, \Flexio\IFace\IStream $stream)
    {
        $this->local_files[$fileno] = $stream;
        return $this;
    }

    public function getLocalFile(int $fileno)
    {
        return $this->local_files[$fileno] ?? null;
    }

    public function getLocalFiles()
    {
        return $this->local_files;
    }

    public function setLocalFiles($files)
    {
        $this->local_files = $files;
    }

    // for post files
    public function addFile(string $name, \Flexio\IFace\IStream $stream) : \Flexio\Jobs\Process
    {
        $this->files[$name] = $stream;
        return $this;
    }

    public function getFiles() : array
    {
        return $this->files;
    }


    public function addLocalConnection(string $identifier, array $connection_properties) : void
    {
        $this->local_connections[$identifier] = $connection_properties;
    }

    public function getLocalConnection(string $identifier) : ?array
    {
        return ($this->local_connections[$identifier] ?? null);
    }

    public function getLocalConnections() : array
    {
        return $this->local_connections;
    }

    public function getConnection(string $identifier) : ?array
    {
        $local = $this->getLocalConnection($identifier);
        if ($local !== null)
            return $local;

        $owner_user_eid = $this->getOwner();

        try
        {
            $connection = \Flexio\Object\Connection::load($identifier);
        }
        catch (\Exception $e)
        {
            $eid_from_identifier = \Flexio\Object\Connection::getEidFromName($owner_user_eid, $identifier);
            if ($eid_from_identifier === false)
                throw $e;

            $connection = \Flexio\Object\Connection::load($eid_from_identifier);
        }

        // check the rights on the connection
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($connection->allows($owner_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return $connection->get();
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

    public function setError(string $code = '', string $message = null, string $module = null, int $line = null, string $type = null, string $trace = null)  : \Flexio\Jobs\Process
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

    public function validate(array $task) : array
    {
        try
        {
            $job = self::createTask($task);
            return $job->validate();
        }
        catch (\Error $e)
        {
            $errors = array();
            $errors[] = array('code' => \Flexio\Base\Error::INVALID_SYNTAX, 'message' => 'Missing or invalid task operation or parameter.');
            return $errors;
        }
    }

    public function execute(array $task) : \Flexio\Jobs\Process
    {
        // if the process was cancelled, stop the process
        if ($this->isStopped() === true)
            return $this;

        // if the process was exited intentionally, stop the process
        $response_code = $this->getResponseCode();
        if ($response_code !== self::RESPONSE_NONE)
            return $this;

        // if there's an error, stop the process
        if ($this->hasError())
            return $this;

        // signal the start of the task
        $this->signal(self::EVENT_STARTING_TASK, $this->getProcessState($task));

        // if a task on the process has already been executed, move the previous stdout
        // to the current stdin; this allows chaining of execute() on the process with
        // a separate task in each execute:  $process->execute($task1)->execute($task2);
        if ($this->first_execute === false)
        {
            // copy the stdout of the last task to the stdin; make a new stdout
            $this->stdin = $this->stdout;
            $this->stdout = self::createStream();
        }

        // execute the task
        $this->executeTask($task);
        $this->first_execute = false;

        // signal the end of the task
        $this->signal(self::EVENT_FINISHED_TASK, $this->getProcessState($task));

        return $this;
    }

    public function stop() : \Flexio\Jobs\Process
    {
        $this->stop = true;
        return $this;
    }

    public function isStopped() : bool
    {
        return $this->stop;
    }

    public function signal(string $event, array $properties) : \Flexio\Jobs\Process
    {
        foreach ($this->handlers as $handler)
        {
            call_user_func($handler, $event, $properties);
        }

        return $this;
    }

    private function getProcessState(array $task = null) : array
    {
        $state = array();
        $state['stdin'] = $this->getStdin();
        $state['stdout'] = $this->getStdout();
        $state['task'] = $task ?? array();
        return $state;
    }

    private function executeTask(array $task) : void
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
            $trace = $debug ? $e->getTraceAsString() : null;
            $code = $info['code'];
            $message = $info['message'];
            $type = 'flexio exception';

            if ($debug)
                $this->setError($code, $message, $module, $line, $type, $trace);
                 else
                $this->setError($code, $message);
        }
        catch (\Exception $e)
        {
            $debug = IS_DEBUG();
            $module = $debug ? $e->getFile() : \Flexio\Base\Util::safePrintCodeFilename($e->getFile());
            $line = $e->getLine();
            $trace = $debug ? $e->getTraceAsString() : null;
            $message = $e->getMessage();
            $type = 'system exception';

            if ($debug)
                $this->setError(\Flexio\Base\Error::GENERAL, $message, $module, $line, $type, $trace);
                 else
                $this->setError(\Flexio\Base\Error::GENERAL); // don't patch through sensitive info in non-debug
        }
        catch (\Error $e)
        {
            $debug = IS_DEBUG();
            $module = $debug ? $e->getFile() : \Flexio\Base\Util::safePrintCodeFilename($e->getFile());
            $line = $e->getLine();
            $trace = $debug ? $e->getTraceAsString() : null;
            $message = $e->getMessage();
            $type = 'system error';

            if ($debug)
                $this->setError(\Flexio\Base\Error::GENERAL, $message, $module, $line, $type, $trace);
                 else
                $this->setError(\Flexio\Base\Error::GENERAL); // don't patch through sensitive info in non-debug
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

        $encoded_task = json_encode($task);
        $hash = md5(
            $implementation_version .
            $encoded_task
        );

        return $hash;
    }
}
