<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
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

    public const STATUS_UNDEFINED = '';
    public const STATUS_PENDING   = 'S'; // 'S' for 'Starting' ('P' used to be used for STATUS_PAUSED, which is no longer used)
    public const STATUS_WAITING   = 'W';
    public const STATUS_RUNNING   = 'R';
    public const STATUS_CANCELLED = 'X';
    public const STATUS_FAILED    = 'F';
    public const STATUS_COMPLETED = 'C';

    public const RESPONSE_NONE = 0;
    public const RESPONSE_NORMAL = 200;

    private $handlers = [];     // array of callbacks to run

    private $owner_eid;                 // user eid of the owner of the process
    private $mount_eid;                 // mount/connection eid of the pipe that created the process, if any
    private $response_code;
    private $process_status;
    private $error;
    private $params;                    // variables that are used in the processing (array of \Flexio\Base\Stream objects)
    private $files;                     // array of streams of files (similar to php's $_FILES)
    private $local_connections = [];    // map from connection identifier to connection_properties...e.g. [ 'connection_type' => ''. 'connection_properties' => [...]]
    private $local_files = [];          // map from file number to Stream object
    private $stdin;
    private $stdout;

    public function __construct()
    {
        $this->owner_eid = '';
        $this->mount_eid = '';
        $this->response_code = self::RESPONSE_NONE;
        $this->process_status = self::STATUS_UNDEFINED;
        $this->error = array();
        $this->params = array();
        $this->files = array();
        $this->local_connections = array();
        $this->local_files = array();
        $this->stdin = self::createStream();
        $this->stdout =  self::createStream();
    }

    public static function create() : \Flexio\Jobs\Process
    {
        $object = new static();
        return $object;
    }

    ////////////////////////////////////////////////////////////
    // IProcess interface
    ////////////////////////////////////////////////////////////

    public function setOwner(string $owner_eid) : \Flexio\Jobs\Process
    {
        $this->owner_eid = $owner_eid;
        return $this;
    }

    public function getOwner() : string
    {
        return $this->owner_eid;
    }

    public function setMount(string $mount_eid) : \Flexio\Jobs\Process
    {
        $this->mount_eid = $mount_eid;
        return $this;
    }

    public function getMount() : string
    {
        return $this->mount_eid;
    }

    public function setParams(array $arr) : \Flexio\Jobs\Process
    {
        $this->params = $arr;
        return $this;
    }

    public function getParams() : array
    {
        return $this->params;
    }

    public function addFile(string $name, \Flexio\IFace\IStream $stream) : \Flexio\Jobs\Process
    {
        // for post files
        $this->files[$name] = $stream;
        return $this;
    }

    public function getFiles() : array
    {
        return $this->files;
    }

    public function setLocalFile(int $fileno, \Flexio\IFace\IStream $stream)
    {
        // for local files (using file number handle)
        $this->local_files[$fileno] = $stream;
        return $this;
    }

    public function getLocalFile(int $fileno)
    {
        return $this->local_files[$fileno] ?? null;
    }

    public function setLocalFiles($files)
    {
        $this->local_files = $files;
    }

    public function getLocalFiles()
    {
        return $this->local_files;
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
            if (!$eid_from_identifier)
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
            $job = \Flexio\Jobs\Factory::getJobClass($task);
            return $job::validate($task);
        }
        catch (\Error $e)
        {
            $errors = array();
            $errors[] = array('code' => \Flexio\Base\Error::INVALID_SYNTAX, 'message' => 'Missing or invalid task operation or parameter.');
            return $errors;
        }
    }

    public function queue(string $handler, array $task = array()) : \Flexio\Jobs\Process
    {
        $this->handlers[] = array(
            'func' => $handler,
            'task' => $task
        );

        return $this;
    }

    public function run() : \Flexio\Jobs\Process
    {
        if (!IS_PROCESSTRYCATCH())
        {
            // during debugging, sometimes try/catch needs to be turned
            // of completely; this switch is implemented here and in Api.php
            // call the event handlers for the given event
            foreach ($this->handlers as $h)
            {
                $func = $h['func'];
                $task = $h['task'];
                call_user_func($func, $this, $task);
            }
        }
         else
        {
            try
            {
                // call the event handlers for the given event
                foreach ($this->handlers as $h)
                {
                    $func = $h['func'];
                    $task = $h['task'];
                    call_user_func($func, $this, $task);
                }
            }
            catch (\Flexio\Base\Exception | \Exception | \Error $e)
            {
                $error_info = \Flexio\Base\Error::getInfo($e);
                $code = $error_info['code'] ?? '';
                $message = $error_info['message'] ?? '';
                $module = $error_info['module'] ?? null;
                $line = $error_info['line'] ?? null;
                $type = $error_info['type'] ?? null;
                $trace = $error_info['trace'] ?? null;
                $this->setError($code, $message, $module, $line, $type, $trace);
            }
        }

        return $this;
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function execute(array $task) : \Flexio\Jobs\Process
    {
        if (!IS_PROCESSTRYCATCH())
        {
            // during debugging, sometimes try/catch needs to be turned
            // of completely; this switch is implemented here and in Api.php
            // call the event handlers for the given event
            \Flexio\Jobs\Task::run($this, $task);
        }
         else
        {
            try
            {
                \Flexio\Jobs\Task::run($this, $task);
            }
            catch (\Flexio\Base\Exception | \Exception | \Error $e)
            {
                $error_info = \Flexio\Base\Error::getInfo($e);
                $code = $error_info['code'] ?? '';
                $message = $error_info['message'] ?? '';
                $module = $error_info['module'] ?? null;
                $line = $error_info['line'] ?? null;
                $type = $error_info['type'] ?? null;
                $trace = $error_info['trace'] ?? null;
                $this->setError($code, $message, $module, $line, $type, $trace);
            }
        }

        return $this;
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
