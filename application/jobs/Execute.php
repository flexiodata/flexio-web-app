<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-11-20
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "execute",  // string, required
    "lang": "",       // string, required, enum: python|nodejs|javascript
    "code": "",       // string (base64 encoded string of code to run); either "code" or "path" is required
    "path": "",       // string (url to remote code to execute); either "code" or "path" is required
    "integrity": ""   // string (integrity check; sha256, sha384, sha512 allowed with format: <sha-type>:<integrity-check>
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['execute']),
        'lang'       => array('required' => true,  'enum' => ['python','nodejs','javascript']),
        'code'       => array('required' => false, 'type' => 'string'),
        'path'       => array('required' => false, 'type' => 'string'),
        'integrity'  => array('required' => false, 'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class BinaryData
{
    public $data = '';
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function __toString()
    {
        return $this->data;
    }
    public function getData()
    {
        return $this->data;
    }
}

$g_dockerbin = '';
$gc_container_list = [];
function gc_container_cleanup()
{
    global $gc_container_list;
    global $g_dockerbin;

    foreach ($gc_container_list as $container_name => $v)
    {
        $cmd = "$g_dockerbin kill $container_name";
        @exec("$cmd > /dev/null &");
    }
}

function gc_container_add($container_name)
{
    global $gc_container_list;
    $gc_container_list[$container_name] = true;
}

function gc_container_remove($container_name)
{
    global $gc_container_list;
    unset($gc_container_list[$container_name]);
}



function get_docker_status($container_name)
{
    // this isn't working on Aaron's machine, so we'll use get_docker_full_state instead

    @exec("docker inspect -f {{.State.Status}} $container_name", $output_lines);
    //@exec("docker inspect -f {{.State}} $container_name", $output_lines);
    //@exec("docker inspect $container_name", $output_lines);

    $res = '';
    foreach ($output_lines as $line)
        $res .= $line;
    return $res;
}


function get_docker_full_state($container_name)
{
    //@exec("docker inspect -f {{.State.Status}} $container_name", $output_lines);
    @exec("docker inspect -f {{.State}} $container_name", $output_lines);
    //@exec("docker inspect $container_name", $output_lines);

    $res = '';
    foreach ($output_lines as $line)
        $res .= $line;
    return $res;
}


class ExecuteProxy
{
    private const MESSAGE_SIGNATURE = '--MSGqQp8mf~';

    public $code = '';
    public $pipes = [null,null,null];
    public $process = null;
    public $check_sig = '';
    public $callbacks = null;
    public $compile_error = '';

    private static function getLocalIpAddress() : string
    {
        if (isset($_SERVER['SERVER_ADDR']))
            $result =  $_SERVER['SERVER_ADDR'];
             else
            $result = trim(shell_exec("hostname -I | cut -d' ' -f1"));

        // check IP address for valid format
        if (!filter_var($result, FILTER_VALIDATE_IP))
            return '127.0.0.1';

        return $result;
    }

    public function initialize($engine, $code, $callbacks) : bool
    {
        $this->engine = $engine;
        $this->callbacks = $callbacks;
        $this->code = $code;
        return true;
    }


        /*
        // start a zeromq server -- let OS choose port
        $address = self::getLocalIpAddress();
        $server = new \ZMQSocket(new \ZMQContext(), \ZMQ::SOCKET_REP);
        $server->bind("tcp://$address:*");

        // figure out what port OS chose
        $port = $server->getSockOpt(\ZMQ::SOCKOPT_LAST_ENDPOINT);
        $colon = strrpos($port, ':');
        $port = ($colon !== false ? substr($port, $colon+1) : '');
        if (strlen($port) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Execute proxy: could not determine bind port");
        $port = (int)$port;

        $address = self::getLocalIpAddress();
        $ipc_address = "tcp://$address:$port";
        */


    public function run() : void
    {
        $max_execution_time = 3600;
        $exception = null;
        $exception_msg = '';

        // generate a key which will be used as a kind of password
        $access_key = \Flexio\Base\Util::generateRandomString(20);
        $container_name = 'fxexec-' . $access_key;

        // get the docker binary location
        global $g_dockerbin;
        $g_dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($g_dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $host_src_dir = "/dev/shm/fxsrc-$access_key";
        $container_src_dir = '/fxruntime/src';

        mkdir($host_src_dir, 0700);

        if ($this->engine == 'python')
        {
            file_put_contents("$host_src_dir/script.py", $this->code);
        }
        else if ($this->engine == 'nodejs')
        {
            file_put_contents("$host_src_dir/script.js", $this->code);
        }
        else
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED, "Unknown execution engine");
        }


        // start a zeromq server -- let OS choose port

        $host_socket_path = "/dev/shm/ipc-exec-$access_key";
        $container_socket_path = "/tmp/ipc-endpoint";
        $container_ipc_address = "ipc://$container_socket_path";

        $dsn = "ipc://$host_socket_path";
        $server = new \ZMQSocket(new \ZMQContext(), \ZMQ::SOCKET_REP);
        $server->bind($dsn);

        // recv() should time out every 1000 ms
        $server->setSockOpt(\ZMQ::SOCKOPT_RCVTIMEO, 1000);
        $server->_is_bound = true;


        //$server->setSockOpt(\ZMQ::SOCKOPT_SNDTIMEO, 250);

        //$cmd = "./update-docker-images && $g_dockerbin run --rm -e FLEXIO_RUNTIME_KEY=$access_key -e FLEXIO_RUNTIME_SERVER=tcp://$address:$port -i fxruntime timeout 3600s python3 /fxpython/fxstart.py";
        //echo $cmd;
        //ob_end_flush();
        //flush();

        $engine = $this->engine;
        //$cmd = "$g_dockerbin run --rm --name $container_name -v $host_socket_path:$container_socket_path -e FLEXIO_RUNTIME_KEY=$access_key -e FLEXIO_RUNTIME_SERVER=$container_ipc_address -e FLEXIO_EXECUTE_ENGINE=$engine -i fxruntime python3 /fxpython/fxstart.py";

        // OLD
        //$cmd = "$g_dockerbin run -d --net=host --rm --name $container_name -v $host_socket_path:$container_socket_path -e FLEXIO_RUNTIME_KEY=$access_key -e FLEXIO_RUNTIME_SERVER=$container_ipc_address -e FLEXIO_EXECUTE_ENGINE=$engine -i fxruntime python3 /fxpython/fxstart.py";

        // NEW
        $cmd = "$g_dockerbin run -d --net=host --rm --name $container_name -v $host_src_dir:$container_src_dir:ro -v $host_socket_path:$container_socket_path -e FLEXIO_RUNTIME_KEY=$access_key -e FLEXIO_RUNTIME_SERVER=$container_ipc_address -e FLEXIO_EXECUTE_ENGINE=$engine -i fxruntime /fxruntime/fxstart";

        //echo "./update-docker-images && " . str_replace(" -d ", " ", $cmd);
        //ob_end_flush();
        //flush();

        exec("$cmd 2>&1", $output_lines, $exit_code);

        //$docker_exec_output = '';
        //foreach ($output_lines as $line)
        //    $docker_exec_output .= $line;

        
        // should the script host crash for any reason, the container could be left running;
        // the following function adds this container to a list that of containers that
        // will be terminated if the execute job abnormally exits; If the execute job exits
        // normally, no GC will be done
        gc_container_add($container_name);


        register_shutdown_function(function() use (&$server, $dsn, $host_socket_path, $host_src_dir) {
            \Flexio\Jobs\gc_container_cleanup();
            @unlink($host_socket_path);
            if ($server !== null && $server->_is_bound)
            {
                $server->unbind($dsn);
                $server->_is_bound = false;
            }

            \Flexio\Base\Util::rmtree($host_src_dir);
        });

        $start_time = microtime(true);
        $actual_start_time = null;    // actual_start_time is initially set when the get_script hook is called

        $connection_established = false;
        $call_count = 0;

        $last_check = 0;

        while (true)
        {
            $message = $server->recv();

            if ($message !== false)
            {
                $message = @json_decode($message);

                if ($message === null)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Execute proxy: decoding fault");
                $message = (array)$message;

                if (isset($message['access_key']) && $message['access_key'] == $access_key)
                {
                    $method = ($message['method'] ?? '');

                    if ($actual_start_time === null)
                    {
                        $actual_start_time = microtime(true);
                    }
                    
                    if ($method == 'get_script')
                    {
                        $response = [ 'result' => $this->code, 'id' => $message['id'] ];
                        $server->send(json_encode($response));
                    }
                    else if ($method == 'compile_error')
                    {
                        $retval = call_user_func_array([ $this, 'func_'.$method ], $message['params'] ?? ['']);
                        $response = [ 'result' => true, 'id' => $message['id'] ];
                        $server->send(json_encode($response));
                    }
                    else if ($method == 'exit_loop')
                    {
                        $response = [ 'result' => true, 'id' => $message['id'] ];
                        $server->send(json_encode($response));
                        break;
                    }
                    else
                    {
                        $this->onMessage($server, $message);
                    }

                    ++$call_count;
                }
            }

            if (!is_null($actual_start_time))
            {
                $execution_seconds = (int)floor(microtime(true) - $actual_start_time);
                if ($execution_seconds > $max_execution_time)
                {
                    // first, make sure container is 'dead'
                    $cmd = "$g_dockerbin kill $container_name";
                    @exec("$cmd > /dev/null &");

                    $exception = \Flexio\Base\Error::GENERAL;
                    $exception_msg = "Maximum execution time exceeded";
                    break;
                }
            }

            if ($call_count == 0)
            {
                $seconds = (int)floor(microtime(true) - $start_time);
                if ($seconds - $last_check > 30)
                {
                    $last_check = $seconds;
                    $full_state = get_docker_full_state($container_name);
                    $is_running = (strpos($full_state, 'running') !== false || (strpos($full_state, 'created') !== false));

                    // if the container says it's running -- give it another chance (check in 30 seconds)

                    // but if the final threshold (300 seconds/5 minutes) has expired, something has gone wrong;
                    // terminate the execute job with an exception (but break first and clean up the socket etc)

                    if ($seconds >= 300 || !$is_running)
                    {
                        // first, make sure container is 'dead'
                        $cmd = "$g_dockerbin kill $container_name";
                        @exec("$cmd > /dev/null &");

                        $exception = \Flexio\Base\Error::GENERAL;
                        $exception_msg = "Execute proxy: IPC timeout";
                        //$exception_msg = "Execute proxy: IPC timeout Container=$container_name Exit Code=$exit_code Output=$docker_exec_output State=$full_state";

                        break;
                    }
                }
            }
        }

        if ($server->_is_bound)
        {
            $server->unbind($dsn);
            $server->_is_bound = false;
            $server = null;
        }

        @unlink($host_socket_path);

        // docker container stopped normally, so it does not need to be terminated; remove from the container GC list
        gc_container_remove($container_name);

        if (!is_null($exception))
        {
            throw new \Flexio\Base\Exception($exception, $exception_msg);
        }
    }

    public function func_compile_error($error)
    {
        $this->compile_error .= $error;
    }

    public function getStdError() : ?string
    {
        return $this->compile_error;

        $res = fread($this->pipes[2], 8192);
        if ($res === false)
            return null;
        if (strlen(trim($res)) == 0)
            return null;
        return $res;
    }


    public static function walk_recursive(&$v, $callback)
    {
        if (is_object($v))
        {
            foreach ($v as $prop => &$value)
            {
                $callback($value);
                self::walk_recursive($value, $callback);
            }
        }
        else if (is_array($v))
        {
            foreach ($v as $key => &$value)
            {
                $callback($value);
                self::walk_recursive($value, $callback);
            }
        }
    }



    public function onMessage(\ZMQSocket $server, array $msg) : void
    {
        if (!isset($msg['id']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Execute proxy: missing id");

        if (!isset($msg['method']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Execute proxy: missing method");

        $id = $msg['id'];
        $method = $msg['method'];
        $args = [];
        if (isset($msg['params']))
            $args = $msg['params'];

        // check if function exists
        if (!method_exists($this->callbacks, 'func_' . $method))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Execute proxy: unknown method '$method' invoked");
        }

        $moniker = "~$id/";
        $moniker_len = strlen($moniker);

        self::walk_recursive($args, function(&$v) use ($moniker, $moniker_len) {
            if (is_string($v) && substr($v, 0, $moniker_len) == $moniker)
            {
                if (substr($v, $moniker_len, 8) == 'bin.b64:')
                    $v = new BinaryData(base64_decode(substr($v, $moniker_len+8)));
                else if (substr($v, $moniker_len, 7) == 'stream:')
                    $v = "context://fileno/" . substr($v, $moniker_len+7);
            }
        });


        // make function call
        if (!is_array($args))
            $args = [ $args ];
        $retval = call_user_func_array([ $this->callbacks, 'func_'.$method ], $args);


        if (is_array($retval))
        {
            array_walk_recursive($retval, function(&$v, $k) use ($moniker, $moniker_len) {
                if ($v instanceof BinaryData)
                    $v = $moniker . 'bin.b64:' . base64_encode($v->data);
            });
        }
         else
        {
            if ($retval instanceof BinaryData)
                $retval = $moniker . 'bin.b64:' . base64_encode($retval->data);
        }


        //  Send reply back to client
        $response = [ 'result' => $retval, 'id' => $msg['id'] ];
        $server->send(json_encode($response));
    }
}


class ScriptHostFile
{
    public $stream = null;
    public $reader = null;
    public $writer = null;
}

class ScriptHost
{
    protected $process = null;

    private $files = [];        // array of ScriptHostFile

    // I'd like to add these type checks back, but I was getting an error running this pipe:
    // Flexio.pipe()
    //   .javascript(function(ctx) {
    //        ctx.pipe.create("/google-sheets-bwilliams/BensNewSheet222")
    //        ctx.pipe.insert("/google-sheets-bwilliams/BensNewSheet222", [["a","b","c"]])
    //    })


    //public function setProcess(\Flexio\IFace\Process $process)
    public function setProcess($process) : void
    {
        $this->process = $process;

        // stdin is always stream handle 0
        $file = new ScriptHostFile();
        $file->stream = $this->process->getStdin();
        $this->files[] = $file;
        $handle = count($this->files)-1;
        $this->process->setLocalFile($handle, $file->stream);

        // stdout is always stream handle 1
        $file = new ScriptHostFile();
        $file->stream = $this->process->getStdout();
        $this->files[] = $file;
        $handle = count($this->files)-1;
        $this->process->setLocalFile($handle, $file->stream);

        // reserve a spot for stderr (future)
        $this->files[] = new ScriptHostFile();
    }

    //public function getProcess() : \Flexio\IFace\Process
    public function getProcess() // TODO: add return type
    {
        return $this->process;
    }

    private function getInputReader(int $idx) // TODO: add return type
    {
        if ($idx < 0 || $idx >= count($this->files))
            return null;

        $script_host_file = $this->files[$idx];
        if ($script_host_file->reader === null)
            $script_host_file->reader = $script_host_file->stream->getReader();

        return $script_host_file->reader;
    }

    private function getOutputWriter(int $idx) // TODO: add return type
    {
        if ($idx < 0 || $idx >= count($this->files))
            return null;

        $file = $this->files[$idx];
        if ($file->writer === null)
            $file->writer = $file->stream->getWriter();

        return $file->writer;
    }

    public function func_hello() : string
    {
        return "hello";
    }

    public function func_exit($response_code) : void
    {
        $this->process->setResponseCode((int)$response_code);
    }

    public function func_status($response_code) : void
    {
        $this->process->setResponseCode((int)$response_code);
    }

    public function func_setenv($key,$value) : void
    {
        $env = $this->process->getParams();
        $env[$key] = $value;
        $this->process->setParams($env);
    }

    public function func_getQueryParameters() // TODO: add return type
    {
        return (object)$_GET;
    }

    public function func_getFormParameters() // TODO: add return type
    {
        $form = [];
        $params = $this->process->getParams();
        foreach ($params as $k => $v)
        {
            if (substr($k, 0, 5) == 'form.')
            {
                $form[substr($k,5)] = $v->getReader()->read();
            }
        }
        return (object)$form;
    }

    private $context_files = null;
    public function func_getFilesParameters() // TODO: add return type
    {
        if ($this->context_files)
        {
            return $this->context_files;
        }

        $results = [];

        $files = $this->process->getFiles();
        foreach ($files as $k => $stream)
        {
            $file = new ScriptHostFile();
            $file->stream = $stream;
            $this->files[] = $file;
            $handle = count($this->files)-1;
            $this->process->setLocalFile($handle, $file->stream);

            $results[] = array('handle' => $handle,
                               'tag' => $k,
                               'name' => $file->stream->getName(),
                               'size' => $file->stream->getSize(),
                               'is_table' => $file->stream->isTable(),
                               'content_type' => $file->stream->getMimeType());
        }

        $this->context_files = $results;
        return $this->context_files;
    }

    private $runjob_stdin = null;
    public function func_runJob($json) : void
    {
        $task = @json_decode($json,true);
        if ($task === null)
            return;

        if ($this->runjob_stdin === null)
            $this->runjob_stdin = $this->process->getStdin();

        $process = \Flexio\Jobs\Process::create();
        $process->setOwner($this->getProcess()->getOwner());
        $process->getStdin()->copyFrom($this->runjob_stdin);
        $process->execute($task);

        // stdin of the next invocation of runjob is the stdout of the job that just ran
        $this->runjob_stdin = $process->getStdout();
        $this->process->getStdout()->copyFrom($this->runjob_stdin);
    }

    public function func_upper($str)
    {
        // tester function -- makes string uppercase

        if ($str instanceof BinaryData) {
            $str->data = strtoupper($str->data);
            return [ 'a' => [ $str ] ];
        }

        return strtoupper($str);
    }

    public function func_invoke_service($service, $params)
    {
        if ($service == 'email.send')
        {
            // recursively convert objects to arrays
            $params = json_decode(json_encode($params), true);
            $task = array_merge([ 'op' => 'email' ], $params);

            $process = \Flexio\Jobs\Process::create();
            $process->setOwner($this->getProcess()->getOwner());
            $process->setLocalFiles($this->getProcess()->getLocalFiles()); // allow job process to access files via context://fileno/X
            $process->execute($task);
        }
    }

    public function func_debug($message) : void
    {
        if ($message instanceof BinaryData) {
            var_dump("BINARY DATA");
        }
        var_dump($message);
        die();
        //die($message);
    }

    public function func_crash()
    {
        $obj = null;
        $obj->crash();
    }

    public function func_getConnections() : array
    {
        $owner_user_eid = $this->getProcess()->getOwner();

        // make sure the owner exists
        $owner_user = \Flexio\Object\User::load($owner_user_eid);
        if ($owner_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $results = array();

        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_AVAILABLE);
        $connections = \Flexio\Object\Connection::list($filter);

        foreach ($connections as $c)
        {
            if ($c->allows($owner_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $properties = $c->get();
            $results[] = [ 'eid' => $properties['eid'], 'alias' => $properties['alias'], 'name' => $properties['name'], 'description' => $properties['description']  ];
        }

        return $results;
    }

    public function func_getLocalConnections() : array
    {
        $results = array();

        $local_connections = $this->getProcess()->getLocalConnections();
        foreach ($local_connections as $key => $value)
        {
            $results[] = [ 'eid' => $key, 'alias' => $key, 'name' => $key, 'description' => '' ];
        }

        return $results;
    }

    public function func_getConnectionAccessToken(string $identifier) // TODO: add return type
    {
        // first, check the process's local connections for a hit
        $local_connection_properties = $this->getProcess()->getLocalConnection($identifier);
        if ($local_connection_properties)
        {
            $service = \Flexio\Services\Factory::create($local_connection_properties);
            if (!$service)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE, "Process-local service not found");

            $tokens = $service->getTokens();
            return $tokens['access_token'];
        }
         else
        {
            $owner_user_eid = $this->getProcess()->getOwner();

            // make sure the owner exists
            $owner_user = \Flexio\Object\User::load($owner_user_eid);
            if ($owner_user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // load the object; make sure the eid is associated with the owner
            // as an additional check
            $connection = \Flexio\Object\Connection::load($identifier);
            if ($owner_user_eid !== $connection->getOwner())
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            return $connection->getAccessToken();
        }
    }

    public function func_getEnv() // TODO: add return type
    {
        $res = [];
        $params = $this->process->getParams();
        foreach ($params as $k => $v)
        {
            $res[$k] = $v->getReader()->read();
        }
        return (object)$res;
    }

    public function func_setEnvValue($key, $value) : bool
    {
        $env = $this->process->getParams();
        $env[(string)$key] = (string)$value;
        $this->process->setParams($env);
        return true;
    }

    public $stream_cache = array();

    public function func_getStreamInfo($handle) : ?array
    {
        if ($handle < 0 || $handle >= count($this->files))
            return null;

        $file = $this->files[$handle];

        return array('handle' => $handle,
                     'name' => $file->stream->getName(),
                     'size' => $file->stream->getSize(),
                     'is_table' => $file->stream->isTable(),
                     'content_type' => $file->stream->getMimeType());
    }

    public function func_setStreamInfo($handle, $properties) : bool
    {
        if ($handle < 0 || $handle >= count($this->files))
            return false;

        if (!is_array($properties))
            $properties = (array)$properties;

        if (isset($properties['content_type']))
            $properties['mime_type'] = $properties['content_type'];

        $properties = \Flexio\Base\Util::filterArray($properties, ["name","mime_type"]);
        $this->files[$handle]->stream->set($properties);

        return true;
    }

    public function func_fsCreate(string $path, string $connection) : ?array
    {
        $stream = \Flexio\Base\Stream::create();
        $stream->setName($path);

        $file = new ScriptHostFile();
        $file->stream = $stream;
        $file->writer = $stream->getWriter();

        $this->files[] = $file;
        $handle = count($this->files)-1;
        $this->process->setLocalFile($handle, $file->stream);

        return array('handle' => $handle,
                     'name' => '',
                     'size' => $file->stream->getSize(),
                     'is_table' => $file->stream->isTable(),
                     'content_type' => $file->stream->getMimeType());
    }

    public function func_fsCreateTempFile() : ?array
    {
        $stream = \Flexio\Base\Stream::create();
        $stream->setName(''); // temporary

        $file = new ScriptHostFile();
        $file->stream = $stream;
        $file->writer = $stream->getWriter();

        $this->files[] = $file;
        $handle = count($this->files)-1;
        $this->process->setLocalFile($handle, $file->stream);

        return array('handle' => $handle,
                     'name' => '',
                     'size' => $file->stream->getSize(),
                     'is_table' => $file->stream->isTable(),
                     'content_type' => $file->stream->getMimeType());
    }

    public function func_fsOpen(string $mode, string $path, string $connection) : ?array
    {
        if ($mode == 'w' || $mode == 'w+')
        {
            $key = \Flexio\Base\Util::generateRandomString(20);

            $stream = \Flexio\Base\Stream::create();
            $stream->setName($path);

            $file = new ScriptHostFile();
            $file->stream = $stream;
            $this->files[] = $file;
            $handle = count($this->files)-1;
            $this->process->setLocalFile($handle, $file->stream);

            return array('handle' => $handle,
                         'name' => '',
                         'size' => $file->stream->getSize(),
                         'is_table' => $file->stream->isTable(),
                         'content_type' => $file->stream->getMimeType());
        }


        $vfs = new \Flexio\Services\Vfs($this->process->getOwner());
        $vfs->setProcess($this->process);

        $info = $vfs->getFileInfo($path);



        $is_table = false;
        $properties = [ 'mime_type' => ($info['content_type'] ?? 'application/octet-stream') ];
        if (isset($info['structure']))
        {
            $is_table = true;
            $properties['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
            $properties['structure'] = $info['structure'];
        }

        $properties['name'] = $path;


        $stream = \Flexio\Base\Stream::create();
        $stream->set($properties);
        $streamwriter = $stream->getWriter();


        $files = $vfs->read($path, function($data) use (&$streamwriter) {
            $streamwriter->write($data);
        });


        $file = new ScriptHostFile();
        $file->stream = $stream;
        $this->files[] = $file;
        $handle = count($this->files)-1;
        $this->process->setLocalFile($handle, $file->stream);

        if ($mode == 'a' || $mode == 'a+')
        {
            $file->writer = $streamwriter;
        }

        $streamwriter = null;

        return array('handle' => $handle,
                     'name' => '',
                     'size' => $file->stream->getSize(),
                     'is_table' => $file->stream->isTable(),
                     'content_type' => $file->stream->getMimeType());
    }


    public function func_fsCommit(int $stream_idx /* handle */)
    {
        if ($stream_idx < 0 || $stream_idx >= count($this->files))
            return false;

        $file = $this->files[$stream_idx];
        $file->reader = null;
        $file->writer = null;

        $stream = $file->stream;

        $target_path = $stream->getName();
        if (strlen($target_path) == 0)
            return true; // temporary file

        $reader = $stream->getReader();

        $vfs = new \Flexio\Services\Vfs($this->process->getOwner());
        $vfs->setProcess($this->process);

        $files = $vfs->write($target_path, function($length) use (&$reader) {
            return $reader->read($length);
        });

        return true;
    }

    public function func_fsExists(string $path) : bool
    {
        $vfs = new \Flexio\Services\Vfs($this->process->getOwner());
        $vfs->setProcess($this->process);
        return $vfs->exists($path);
    }

    public function func_fsRemove(string $path) : bool
    {
        $vfs = new \Flexio\Services\Vfs($this->process->getOwner());
        $vfs->setProcess($this->process);
        return $vfs->unlink($path);
    }

    public function func_fsList(string $path, string $connection)
    {
        $params = [ 'path' => $path ];
        if (strlen($connection) > 0)
            $params['connection'] = $connection;

        return \Flexio\Jobs\List1::doList($this->process, $params);

        /*
        $vfs = new \Flexio\Services\Vfs($this->process->getOwner());
        $vfs->setProcess($this->process);
        return $vfs->list($path);
        */
    }

    public function func_managedCreate(int $stream_idx, $properties) : bool
    {
        if ($stream_idx < 0 || $stream_idx >= count($this->files))
            return false;

        if (is_object($properties))
            $properties = (array)$properties;

        $stream = $this->files[$stream_idx]->stream;

        $set = array();

        if (isset($properties['name']))
            $set['name'] = $properties['name'];
        if (isset($properties['content_type']))
            $set['mime_type'] = $properties['content_type'];
        if (isset($properties['structure']))
        {
            for ($i = 0; $i < count($properties['structure']); ++$i)
            {
                if (is_object($properties['structure'][$i]))
                    $properties['structure'][$i] = (array)$properties['structure'][$i];
            }

            $set['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
            $set['structure'] = $properties['structure'];
        }

        $stream->set($set);

        // ensure the table/file is actually created
        $writer = $this->getOutputWriter($stream_idx);

        return true;
    }

    public function func_getInputStreamStructure(int $stream_idx) : array
    {
        if ($stream_idx < 0 || $stream_idx >= count($this->files))
            return false;

        $stream = $this->files[$stream_idx]->stream;

        return $stream->getStructure()->enum();
    }

    public function func_insertRow(int $stream_idx, $row) : void
    {
        $writer = $this->getOutputWriter($stream_idx);
        if (is_null($writer))
            return;

        if ($row instanceof BinaryData)
        {
            $writer->write($row->getData());
        }
         else
        {
            if (is_object($row))
                $row = (array)$row;
            $writer->write($row);
        }
    }

    public function func_insertRows(int $stream_idx, $row) : void
    {
        $writer = $this->getOutputWriter($stream_idx);
        if (is_null($writer))
            return;

        foreach ($rows as $row)
        {
            if (is_object($row))
                $row = (array)$row;
            $writer->write($row);
        }
    }

    public function func_write($handle, $data) : void
    {
        $writer = $this->getOutputWriter($handle);
        if (is_null($writer))
            return;

        $stream = $this->files[$handle]->stream;
        $is_table = ($stream && $stream->isTable());

        if ($data instanceof BinaryData)
        {
            $writer->write($data->getData());
        }
        else if (is_object($data))
        {
            if ($is_table)
                $writer->write((array)$data);
                 else
                $writer->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        }
        else if (is_array($data))
        {
            if ($is_table)
                $writer->write((array)$data);
                 else
                $writer->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        }
        else
        {
            $writer->write((string)$data);
        }
    }

    public function func_read(int $stream_idx, $length) // TODO: add return type
    {
        $reader = $this->getInputReader($stream_idx);
        if (is_null($reader))
            return false;

        if ($length === -1 || $length === null)
        {
            $res = '';
            while (($chunk = $reader->read(4096)) !== false)
                $res .= $chunk;
        }
         else
        {
            $res = $reader->read($length);
        }

        if ($res === false)
            return false;
        return new BinaryData($res);
    }

    public function func_readline(int $stream_idx, bool $associative) // TODO: add return type
    {
        $reader = $this->getInputReader($stream_idx);
        if (is_null($reader))
            return false;

        $res = $reader->readRow();
        if ($res === false)
            return false;
        if (is_string($res))
            return $res;
        return $associative ? $res : array_values($res);
    }
}



class Execute extends \Flexio\Jobs\Base
{
    private $code_base64 = '';
    private $code = '';
    private $process = null;

    private static function getFileContents(\Flexio\IFace\IProcess $process, string $path) : string
    {
        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);
        $info = $vfs->getFileInfo($path);

        $contents = '';
        $vfs->read($path, function($data) use (&$contents) {
            $contents .= $data;
        });

        return $contents;
    }

    public function validate() : array
    {
        $errors = array();
        $job_params = $this->getJobParameters();

        if (!isset($job_params['op']))
            $errors[] = array('code' => \Flexio\Base\Error::INVALID_SYNTAX, 'message' => '');

        if (count($errors) > 0)
            return $errors;

        try
        {
            $lang = $job_params['lang'] ?? '';
            $code = base64_decode($job_params['code'] ?? '');
            $code = is_null($code) ? '' : $code;

            $err = self::checkScript($lang, $code);
            if ($err !== true)
            {
                $errors[] = array(
                    'code' => 'compile_error',
                    'message' => $err
                );
            }
        }
        catch (\Flexio\Base\Exception $e)
        {
            $errors[] = array(
                'code' => 'unknown_language',
                'message' => 'The scripting language specified is unknown'
            );
        }

        return $errors;
    }

    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        $this->process = $process;

        // properties
        $job_params = $this->getJobParameters();

        // get the language
        $this->lang = $job_params['lang'] ?? false;

        // allow 'javascript' as an alternate for nodejs for backward compatability
        // TODO: remove after migrating all old references
        if ($this->lang == 'javascript')
            $this->lang = 'nodejs';

        // if code is specified, get the contents from the supplied code
        $code = $job_params['code'] ?? false;
        if ($code !== false)
        {
            $this->code_base64 = $job_params['code'];
            $this->code = base64_decode($this->code_base64);
        }
         else
        {
            // if the code isn't specified in a file location, see if it's specified
            // in a remote location
            $file = $job_params['path'] ?? false;
            if ($file === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $this->code = self::getFileContents($process, $file);
            $this->code_base64 = base64_encode($this->code);
        }

        $integrity = $job_params['integrity'] ?? false;
        if ($integrity !== false)
        {
            // an integrity parameter is specified; use a subresource integrity check
            // (https://www.w3.org/TR/SRI/) to verify the code (e.g. "sha384:<base64-encoded-hash-value>");
            // if a hash of the code using the provided algorithm doesn't match the value in
            // the integrity check, throw an exception
            if (!is_string($integrity))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            // use ":" as format/code separator
            $parts = explode(':', $integrity);
            if (count($parts) !== 2)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $integrity_hashformat = strtolower($parts[0]);
            $integrity_hashvalue = strtolower($parts[1]);

            $code_hashvalue = false;
            switch ($integrity_hashformat)
            {
                // make sure the integrity is a supported format; currently, the required formats
                // in the standard are sha256, sha384, and sha512, and weak formats, such as md5
                // and sha1 should be refused
                default:
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

                case 'sha256':
                    $code_hashvalue = hash('sha256', $this->code, false); // false = use lowercase base64 encoded output
                    break;
                case 'sha384':
                    $code_hashvalue = hash('sha384', $this->code, false); // false = use lowercase base64 encoded output
                    break;
                case 'sha512':
                    $code_hashvalue = hash('sha512', $this->code, false); // false = use lowercase base64 encoded output
                    break;
            }

            if ($code_hashvalue !== $integrity_hashvalue)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INTEGRITY_FAILED);
        }

        if ($this->lang == 'python' || $this->lang == 'nodejs')
        {
            $dockerbin = \Flexio\System\System::getBinaryPath('docker');
            if (is_null($dockerbin))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $script_host = new ScriptHost();
            $script_host->setProcess($process);

            $ep = new ExecuteProxy;
            $ep->initialize($this->lang, $this->code, $script_host);
            $ep->run();

            $err = $ep->getStdError();

            if (strlen($err) > 0)
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, $err);
            }
        }
        else if ($this->lang == 'html')
        {
            $outstream = $process->getStdout();
            if (strpos($this->code, 'flexio.input.json_assoc()') !== false)
            {
                $streamreader = $instream->getReader();
                $rows = [];
                while (true)
                {
                    $row = $streamreader->readRow();
                    if ($row === false)
                        break;
                    $rows[] = $row;
                }

                $json = json_encode($rows, JSON_UNESCAPED_SLASHES);

                $code = str_replace('flexio.input.json_assoc()', $json, $code);
            }

            // create the output stream
            $outstream_properties = array(
                'name' => $instream->getName() . '.html',
                'mime_type' => \Flexio\Base\ContentType::FLEXIO_HTML,
                'size' => strlen($code)
            );

            $outstream->set($outstream_properties);
            $streamwriter = $outstream->getWriter();
            $streamwriter->write($code);
        }
        else
        {
            // unknown language
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        }
    }

    // checks a script for compile errors;  If script compiles cleanly, returns true,
    // otherwise returns the error as a textual string

    public static function checkScript(string $lang, string $code) : bool
    {
        // only python supported for now
        if ($lang == 'python')
        {
            $code = base64_encode($code);

            $dockerbin = \Flexio\System\System::getBinaryPath('docker');
            if (is_null($dockerbin))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $cmd = "$dockerbin run --net none --rm -i fxruntime python3 -c 'import py_compile; import base64; code=base64.b64decode(\"$code\"); f=open(\"script.py\",\"wb\"); f.write(code); f.close(); py_compile.compile(\"script.py\");'";

            $f = popen($cmd . ' 2>&1' /*grab stderr*/, 'r');
            $str = fread($f, 8192);
            pclose($f);

            if ($str === false || strlen(trim($str)) == 0)
                return true;

            return $str;
        }
         else if ($lang == 'nodejs')
        {
            return true;
        }
         else
        {
            // unknown language
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        }
    }
}
