<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-28
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Process extends \Flexio\Object\Base
{
    // variables and errors
    private $response_code;
    private $error;

    public function __construct()
    {
        $this->setType(\Model::TYPE_PROCESS);
        $this->response_code = \Flexio\Jobs\Process::PROCESS_RESPONSE_NORMAL;
        $this->error = array();
    }

    public static function create(array $properties = null) : \Flexio\Object\Process
    {
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = \Flexio\Object\Task::create($properties['task'])->get();

        // task is stored as a json string, so it needs to be encoded
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = json_encode($properties['task']);

        // if not process mode is specified, run everything
        if (!isset($properties['process_mode']))
            $properties['process_mode'] = \Model::PROCESS_MODE_RUN;

        $object = new static();
        $model = $object->getModel();
        $local_eid = $model->create($object->getType(), $properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public static function addProcessInputFromStream($php_stream_handle, string $post_content_type, \Flexio\Object\Process $process) // TODO: add type checking for handle
    {
        $stream = false;
        $streamwriter = false;
        $form_params = array();
        $post_streams = array();

        // first fetch query string parameters
        foreach ($_GET as $key => $value)
        {
            $form_params["query.$key"] = $value;
        }

        // \Flexio\Base\MultipartParser can handle application/x-www-form-urlencoded and /multipart/form-data
        // for all other types, we will take the post body and make it into the stdin

        $mime_type = $post_content_type;
        $semicolon_pos = strpos($mime_type, ';');
        if ($semicolon_pos !== false)
            $mime_type = substr($mime_type, 0, $semicolon_pos);
        if ($mime_type != 'application/x-www-form-urlencoded' && $mime_type != 'multipart/form-data')
        {
            $stream = \Flexio\Base\StreamMemory::create();

            // post body is a data stream, post it as our pipe's stdin
            $first = true;
            $done = false;
            $streamwriter = null;
            while (!$done)
            {
                $data = fread($php_stream_handle, 32768);

                if ($data === false || strlen($data) != 32768)
                    $done = true;

                if ($first && $data !== false && strlen($data) > 0)
                {
                    $stream_info = array();
                    $stream_info['mime_type'] = $mime_type;
                    $stream->set($stream_info);
                    $streamwriter = $stream->getWriter();
                }

                if ($streamwriter)
                    $streamwriter->write($data);
            }

            $process->setParams($form_params);
            $process->setStdin($stream);

            return;
        }


        $parser = \Flexio\Base\MultipartParser::create();
        $parser->parse($php_stream_handle, $post_content_type, function ($type, $name, $data, $filename, $content_type) use (&$stream, &$streamwriter, &$process, &$form_params, &$post_streams) {
            if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_BEGIN)
            {
                $stream = \Flexio\Object\Stream::create();

                if ($content_type === false)
                    $content_type = \Flexio\Base\ContentType::getMimeType($filename, '');

                // stream name will be the post variable name, not the multipart filename
                // TODO: should we be using filename in the path and form name in the name?
                $stream_info = array();
                $stream_info['name'] = $name;
                $stream_info['mime_type'] = $content_type;

                $stream->set($stream_info);
                $streamwriter = $stream->getWriter();
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_DATA)
            {
                if ($streamwriter !== false)
                {
                    // write out the data
                    $streamwriter->write($data);
                }
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_END)
            {
                $post_streams[] = $stream;
                $streamwriter = false;
                $stream = false;
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_KEY_VALUE)
            {
                $form_params[$name] = $data;
            }
        });
        fclose($php_stream_handle);

        $process->setParams($form_params);
        foreach ($post_streams as $s)
        {
            // TODO: right now, we only have stdin; so we can only take the first stream
            $process->setStdin($s);
            break;
        }
    }

    public function set(array $properties) : \Flexio\Object\Process
    {
        // TODO: add properties check

        // TODO: some properties shouldn't be able to be changed once
        // a process is running; e.g. the task

        // if a task parameter is set, we need to assign a client id to each element
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = \Flexio\Object\Task::create($properties['task'])->get();

        // task and schedule are stored as a json string, so these need to be encoded
        if (isset($properties) && isset($properties['task']))
            $properties['task'] = json_encode($properties['task']);

        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_model->set($this->getEid(), $properties);
        return $this;
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    public function run(bool $background = true) : \Flexio\Object\Process
    {
        // STEP 1: check the status; don't run the job in certain circumstances
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_status = $process_model->getProcessStatus($this->getEid());

        switch ($process_status)
        {
            // run job; fall through
            default:
            case \Model::PROCESS_STATUS_UNDEFINED:
            case \Model::PROCESS_STATUS_PENDING:
                break;

            // job is already running or has been run, so don't do anything
            case \Model::PROCESS_STATUS_WAITING:
            case \Model::PROCESS_STATUS_RUNNING:
            case \Model::PROCESS_STATUS_CANCELLED:
            case \Model::PROCESS_STATUS_FAILED:
            case \Model::PROCESS_STATUS_COMPLETED:
                return $this;

            // job is paused, so resume it
            case \Model::PROCESS_STATUS_PAUSED:
                $process_model->setProcessStatus($this->getEid(), \Model::PROCESS_STATUS_RUNNING);
                return $this;
        }

        // STEP 2: set the status
        $process_model->setProcessStatus($this->getEid(), \Model::PROCESS_STATUS_RUNNING);

        // STEP 3: run the job
        if ($background !== true)
        {
            $this->execute();
            return $this;
        }

        $process_eid = $this->getEid();
        \Flexio\System\Program::runInBackground("\Flexio\Object\Process::run_internal('$process_eid')");
        return $this;
    }

    public static function run_internal(string $eid) : bool
    {
        // this is a non-blocking internal static run function called that's
        // run in the background by \Flexio\Objects\Process::run($background) when $background
        // is set to true

        // load the process and run in non-blocking mode
        $object = self::load($eid);

        // run the job
        $object->execute();
        return true;
    }

    public function pause() : \Flexio\Object\Process
    {
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_status = $process_model->getProcessStatus($this->getEid());

        switch ($process_status)
        {
            // only allow jobs that are running to be paused
            case \Model::PROCESS_STATUS_RUNNING:
                $process_model->setProcessStatus($this->getEid(), \Model::PROCESS_STATUS_PAUSED);
                break;
        }

        return $this;
    }

    public function cancel() : \Flexio\Object\Process
    {
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_status = $process_model->getProcessStatus($this->getEid());

        switch ($process_status)
        {
            // if a job is already completed, don't allow it to be cancelled
            case \Model::PROCESS_STATUS_CANCELLED:
            case \Model::PROCESS_STATUS_FAILED:
            case \Model::PROCESS_STATUS_COMPLETED:
                return $this;
        }

        $process_model->setProcessStatus($this->getEid(), \Model::PROCESS_STATUS_CANCELLED);
        return $this;
    }

    public function setProcessStatus(string $status) : \Flexio\Object\Process
    {
        if (self::isValidProcessStatus($status) === false)
            return $this;

        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_model->setProcessStatus($this->getEid(), $status);
        return $this;
    }

    public function getProcessStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_status'];
    }

    public function setProcessInfo(array $info) : \Flexio\Object\Process
    {
        // pack the process info
        $params = array();
        $params['process_info'] = json_encode($info);

        // set the info
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_model->set($this->getEid(), $params);

        return $this;
    }

    public function getProcessInfo() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_info'];
    }

    public function getEnvironmentParams() : array
    {
        // return a list of environment parameters;
        // TODO: determine list; for now, include current user information and time
        // TODO: do we want to "namespace" the variables? right now, variables are
        // limited to alphanumeric, but maybe we want to do something like:
        // "flexio.user_firstname", "flexio.user_lastname", etc
        $environment_params = array();

        $environment_params['process.user.firstname'] = \Flexio\System\System::getCurrentUserFirstName();
        $environment_params['process.user.lastname'] = \Flexio\System\System::getCurrentUserLastName();
        $environment_params['process.user.email'] = \Flexio\System\System::getCurrentUserEmail();
        $environment_params['process.time.started'] = \Flexio\System\System::getTimestamp();

        return $environment_params;
    }

    public function setTask(array $task) : \Flexio\Object\Process
    {
        $properties = array();
        $properties['task'] = $task;
        return $this->set($properties);
    }

    public function getTask() : array
    {
        $properties = $this->get();
        return $properties['task'];
    }

    public function setParams(array $params) : \Flexio\Object\Process
    {
        $process_properties = $this->getModel()->process->get($this->getEid());
        $input = json_decode($process_properties['input'], true);
        $input['params'] = $params;
        $input = json_encode($input);
        $this->getModel()->process->set($this->getEid(), array('input' => $input));
        return $this;
    }

    public function getParams() : array
    {
        $process_properties = $this->getModel()->process->get($this->getEid());
        $input = json_decode($process_properties['input'], true);
        return $input['params'] ?? array();
    }

    public function setStdin(\Flexio\Base\IStream $stream)
    {
        $storable_stream = self::createStorableStream($stream);
        $process_properties = $this->getModel()->process->get($this->getEid());
        $input = json_decode($process_properties['input'], true);
        $input['stream'] = $storable_stream->getEid();
        $input = json_encode($input);
        $this->getModel()->process->set($this->getEid(), array('input' => $input));
        return $this;
    }

    public function getStdin() : \Flexio\Base\IStream
    {
        $memory_stream = \Flexio\Base\StreamMemory::create();

        $process_properties = $this->getModel()->process->get($this->getEid());
        $input = json_decode($process_properties['input'], true);

        if ($input === false)
            return $memory_stream;
        if (!isset($input['stream']))
            return $memory_stream;

        $storable_stream = \Flexio\Object\Stream::load($input['stream']);
        if ($storable_stream === false)
            return $memory_stream;

        $memory_stream = self::createMemoryStream($storable_stream);
        return $memory_stream;
    }

    public function setStdout(\Flexio\Base\IStream $stream)
    {
        $storable_stream = self::createStorableStream($stream);
        $process_properties = $this->getModel()->process->get($this->getEid());
        $output = json_decode($process_properties['output'], true);
        $output['stream'] = $storable_stream->getEid();
        $output = json_encode($output);
        $this->getModel()->process->set($this->getEid(), array('output' => $output));
        return $this;
    }

    public function getStdout() : \Flexio\Base\IStream
    {
        $memory_stream = \Flexio\Base\StreamMemory::create();

        $process_properties = $this->getModel()->process->get($this->getEid());
        $output = json_decode($process_properties['output'], true);

        if ($output === false)
            return $memory_stream;
        if (!isset($output['stream']))
            return $memory_stream;

        $storable_stream = \Flexio\Object\Stream::load($output['stream']);
        if ($storable_stream === false)
            return $memory_stream;

        $memory_stream = self::createMemoryStream($storable_stream);
        return $memory_stream;
    }

    public function isBuildMode() : bool
    {
        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->properties['process_mode'] === \Model::PROCESS_MODE_BUILD)
            return true;

        return false;
    }

    public function isRunMode() : bool
    {
        if ($this->isCached() === false)
            $this->populateCache();

        if ($this->properties['process_mode'] === \Model::PROCESS_MODE_RUN)
            return true;

        return false;
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

    public function writeLog(string $event, \Flexio\Jobs\IProcess $process_engine)
    {
        switch ($event)
        {
            // don't do anything if it's an event we don't care about
            default:
            case \Flexio\Jobs\Process::EVENT_PROCESS_STARTING:
            case \Flexio\Jobs\Process::EVENT_PROCESS_FINISHED:
                return;

            case \Flexio\Jobs\Process::EVENT_PROCESS_STARTING_TASK:
                $this->startLog($process_engine);
                break;

            case \Flexio\Jobs\Process::EVENT_PROCESS_FINISHED_TASK:
                $this->finishLog($process_engine);
                break;
        }
    }

    private function fail(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null)
    {
        // only save the first error we come to
        if ($this->hasError())
            return;

        if (!isset($message))
            $message = \Flexio\Base\Error::getDefaultMessage($code);

        $this->error = array('code' => $code, 'message' => $message, 'file' => $file, 'line' => $line, 'type' => $type, 'trace' => $trace);
    }

    private function execute()
    {
        // TODO: need to handle process cancellation

        // track what version of the task implementation we're using
        // (more granular than task version, which may or may not be updated
        // with small logic changes)
        //$implementation_revision = \Flexio\System\System::getGitRevision();

        // set initial job status
        $process_params = array();
        $process_params['started'] = self::getProcessTimestamp();
        $process_params['process_status'] = \Model::PROCESS_STATUS_RUNNING;
        //$process_params['impl_revision'] = $implementation_revision;
        $this->getModel()->process->set($this->getEid(), $process_params);


        // STEP 1: get the process properties
        $current_process_properties = $this->getModel()->process->get($this->getEid());

        // STEP 2: get the process tasks
        $process_tasks = $current_process_properties['task'];
        $process_tasks = json_decode($process_tasks, true);
        if ($process_tasks === false)
        {
            // TODO: set process error, and we're done
        }

        // STEP 3: get the parameters and add the environment variables in
        $environment_variables = $this->getEnvironmentParams();
        $user_variables = $this->getParams();
        $variables = array_merge($user_variables, $environment_variables);

        // STEP 4: create the process engine and configure it
        $process_engine = \Flexio\Jobs\Process::create();
        $process_engine->addTasks($process_tasks);
        $process_engine->setParams($variables);
        $process_engine->getStdin()->copy($this->getStdin());

        // STEP 5: execute the process; TODO: add the logging callbacks
        if ($this->isBuildMode() === true)
            $process_engine->execute([$this, 'writeLog']); // if we're in build mode, log info during execution
             else
            $process_engine->execute(); // if we're not in build mode (e.g. run mode), don't log anything

        // STEP 6: patch through the response code and any error
        $this->response_code = $process_engine->getResponseCode();
        if ($process_engine->hasError())
            $this->error = $process_engine->getError();

        // STEP 7: save final job output and status
        $process_params = array();
        $process_params['finished'] = self::getProcessTimestamp();
        $process_params['process_status'] = $this->hasError() ? \Model::PROCESS_STATUS_FAILED : \Model::PROCESS_STATUS_COMPLETED;
        $process_params['cache_used'] = 'N';
        $this->getModel()->process->set($this->getEid(), $process_params);

        // STEP 8: save the process output; TODO: saving stdout also writes to the database; could we consolidate with other writes for efficiency?
        $this->setStdout($process_engine->getStdout());

        // clear the process object cache
        $this->clearCache();
    }

    public function getResponseCode() : int
    {
        return $this->response_code;
    }

    public function getLog() : array
    {
        $process_model = $this->getModel()->process;
        $log_entries = $process_model->getProcessLogEntries($this->getEid());
        if ($log_entries == false)
            return array();

        $result = array();
        foreach ($log_entries as $entry)
        {
            // unpack the task
            $task = @json_decode($entry['task'],true);
            if ($task !== false)
                $entry['task'] = $task;

            // unpack the input
            $input = @json_decode($entry['input'],true);
            if ($input !== false)
                $entry['input'] = $input;

            // unpack the output
            $output = @json_decode($entry['output'],true);
            if ($output !== false)
                $entry['output'] = $output;

            $result[] = $entry;
        }

        return $result;
    }

    private function isCached() : bool
    {
        // a process may be run in the background and update values
        // in the model; never cache process data so an object always
        // represents the latest state of a process
        return false;

        // note: following is normal cache behavior
        // if ($this->properties === false)
        //    return false;
        //
        // return true;
    }

    private function clearCache() : bool
    {
        $this->eid_status = false;
        $this->properties = false;
        return true;
    }

    private function populateCache() : bool
    {
        // get the properties
        $local_properties = $this->getProperties();
        $this->properties = $local_properties;
        $this->eid_status = $local_properties['eid_status'];
        return true;
    }

    private function getProperties() : array
    {
        $query = '
        {
            "eid" : null,
            "eid_type" : "'.\Model::TYPE_PROCESS.'",
            "eid_status" : null,
            "parent='.\Model::EDGE_PROCESS_OF.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_PIPE.'",
                "name" : null,
                "description" : null
            },
            "owned_by='.\Model::EDGE_OWNED_BY.'" : {
                "eid" : null,
                "eid_type" : "'.\Model::TYPE_USER.'",
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "process_mode": null,
            "task" : null,
            "started_by" : null,
            "started" : null,
            "finished" : null,
            "duration" : null,
            "process_info" : null,
            "process_status" : null,
            "cache_used" : null,
            "created" : null,
            "updated" : null
        }
        ';

        // get the primary process info
        $query = json_decode($query);
        $properties = \Flexio\Object\Query::exec($this->getEid(), $query);
        if (!$properties)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // unpack the primary process task json
        if (isset($properties['task']))
        {
            $task = @json_decode($properties['task'],true);
            if ($task !== false)
                $properties['task'] = $task;
        }

        // unpack the primary process process info json
        if (isset($properties['process_info']))
        {
            $process_info = @json_decode($properties['process_info'],true);
            if ($process_info !== false)
                $properties['process_info'] = $process_info;
        }

        return $properties;
    }

    private function startLog(\Flexio\Jobs\IProcess $process_engine)
    {
        $storable_stream_info = self::getStreamLogInfo($process_engine);
        $process_info = $process_engine->getStatusInfo();
        $task = $process_info['current_task'] ?? array();

        // create a log record
        $params = array();
        $params['task_type'] = $task['type'] ?? '';
        $params['task'] = json_encode($task);
        $params['started'] = self::getProcessTimestamp();
        $params['input'] = json_encode($storable_stream_info);
        $params['log_type'] = \Model::PROCESS_LOG_TYPE_SYSTEM;
        $params['message'] = '';

        $log_eid = $this->getModel()->process->log(null, $this->getEid(), $params);

        // pass on the log entry for the log finish function
        $process_engine->setMetadata(array('log_eid' => $log_eid));
    }

    private function finishLog(\Flexio\Jobs\IProcess $process_engine)
    {
        // make sure we have a log eid record to complete
        $process_engine_metadata = $process_engine->getMetadata();
        if (!isset($process_engine_metadata['log_eid']))
            return;

        $log_eid = $process_engine_metadata['log_eid'];
        $storable_stream_info = self::getStreamLogInfo($process_engine);
        $process_info = $process_engine->getStatusInfo();
        $task = $process_info['current_task'] ?? array();

        // update the log record
        $params = array();
        $params['task_type'] = $task['type'] ?? '';
        $params['task'] = json_encode($task);
        $params['finished'] = self::getProcessTimestamp();
        $params['output'] = json_encode($storable_stream_info);
        $params['log_type'] = \Model::PROCESS_LOG_TYPE_SYSTEM;
        $params['message'] = '';

        $this->getModel()->process->log($log_eid, $this->getEid(), $params);
    }

    private static function getStreamLogInfo(\Flexio\Jobs\IProcess $process_engine) : array
    {
        $stdin = $process_engine->getStdin();
        $stdout = $process_engine->getStdout();

        $storable_stdin = self::createStorableStream($stdin);
        $storable_stdout = self::createStorableStream($stdout);

        $info = array();
        $info['stdin'] = array('eid' => $storable_stdin->getEid());
        $info['stdout'] = array('eid' => $storable_stdout->getEid());
        return $info;
    }

    private static function createStorableStream(\Flexio\Base\IStream $stream) : \Flexio\Object\Stream
    {
        $properties['path'] = \Flexio\Base\Util::generateHandle();
        $properties = array_merge($stream->get(), $properties);
        $storable_stream = \Flexio\Object\Stream::create($properties);

        // copy from the input stream to the storable stream
        $streamreader = $stream->getReader();
        $streamwriter = $storable_stream->getWriter();

        while (true)
        {
            $row = false;
            if ($stream->getMimeType() === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
                $row = $streamreader->readRow();
                 else
                $row = $streamreader->read();

            if ($row === false)
                break;

            $streamwriter->write($row);
        }

        return $storable_stream;
    }

    private static function createMemoryStream(\Flexio\Base\IStream $stream) : \Flexio\Base\StreamMemory
    {
        $properties = $stream->get();
        unset($properties['path']);
        unset($properties['connection_eid']);
        $stream_memory = \Flexio\Base\StreamMemory::create($properties);

        // copy from the input stream to the memory stream
        $streamreader = $stream->getReader();
        $streamwriter = $stream_memory->getWriter();

        while (true)
        {
            $row = false;
            if ($stream->getMimeType() === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
                $row = $streamreader->readRow();
                 else
                $row = $streamreader->read();

            if ($row === false)
                break;

            $streamwriter->write($row);
        }

        return $stream_memory;
    }

    private static function isValidProcessStatus(string $status) : bool
    {
        switch ($status)
        {
            default:
                return false;

            case \Model::PROCESS_STATUS_UNDEFINED:
            case \Model::PROCESS_STATUS_PENDING:
            case \Model::PROCESS_STATUS_WAITING:
            case \Model::PROCESS_STATUS_RUNNING:
            case \Model::PROCESS_STATUS_CANCELLED:
            case \Model::PROCESS_STATUS_PAUSED:
            case \Model::PROCESS_STATUS_FAILED:
            case \Model::PROCESS_STATUS_COMPLETED:
                return true;
        }
    }

    private static function getProcessTimestamp() : string
    {
        // return the timestamp as accurately as we can determine
        $time_exact = microtime(true);
        $time_rounded = floor($time_exact);
        $time_micropart = sprintf("%06d", ($time_exact - $time_rounded) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $time_micropart, (int)$time_rounded));
        return ($date->format("Y-m-d H:i:s.u"));
    }

    private static function logExceptionIfConfigured($exception, $task)
    {
        if (isset($GLOBALS['g_config']->debug_error_log))
        {
            $message = $exception->getMessage();
            $json = json_encode($task);
            file_put_contents($GLOBALS['g_config']->debug_error_log, "Job exception caught '$message'; json was $json\n\n", FILE_APPEND);
        }
    }
}
