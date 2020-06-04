<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
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

    public $owner_eid;
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

    public static function debugstep($fn, $s)
    {
        //return;
        file_put_contents("/tmp/execute-$fn.txt", date("Y-m-d H:i:s") . ' - ' . $s . "\n", FILE_APPEND);
    }

    public function initialize($owner_eid, $engine, $code, $callbacks) : bool
    {
        $this->owner_eid = $owner_eid;
        $this->engine = $engine;
        $this->callbacks = $callbacks;
        $this->code = $code;
        return true;
    }


    public function run() : void
    {
        $c1 = microtime(true);

        //self::debugstep($c1, "Started at $c1");

        // generate a key which will be used as a kind of password
        $access_key = \Flexio\Base\Util::generateRandomString(20);

        $container_name = 'fxexec-' . $this->owner_eid;
        $max_execution_time = 3600;
        $exception = null;
        $exception_msg = '';

        //self::debugstep($c1, "Container name $container_name");

        // get the docker binary location
        global $g_dockerbin;
        $g_dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($g_dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        //self::debugstep($c1, "Docker command '$g_dockerbin'");

        // there is a directory hierarchy that is written on the host and mounted in the container
        // For example, if two processes were running in one container, it would look like this

        // /dev/shm/owner-eid/controller
        // /dev/shm/owner-eid/fxproc-111111/process
        // /dev/shm/owner-eid/fxproc-111111/code/ (script files)
        // /dev/shm/owner-eid/fxproc-222222/process
        // /dev/shm/owner-eid/fxproc-222222/code/ (script files)
        //
        // Inside the container, /dev/shm/owner-eid is mounted to /fxruntime/base

        $host_top_dir = "/dev/shm/fxexec";
        $host_base_dir = $host_top_dir . '/' . $this->owner_eid;
        $host_process_dir = $host_base_dir . '/fxproc-' . $access_key;
        $host_src_dir = $host_process_dir . '/src';
        $host_container_zmq = "$host_base_dir/controller";
        $host_process_zmq = "$host_process_dir/process";

        $cont_base_dir = '/fxruntime/base';
        $cont_src_dir = $cont_base_dir . '/fxproc-' . $access_key . '/src';
        $cont_ipc_address = "ipc://$cont_base_dir/fxproc-$access_key/process";

        $zmq_context = new \ZMQContext();
        $zmqsock_client = null;


        //self::debugstep($c1, "ZMQ Context created");

        // write out the execute job's script

        if (false === @mkdir($host_src_dir, 0700, true))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED, "Cannot create source directory");
        }

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


        //self::debugstep($c1, "Script files written");


        // at script shutdown, clean up progress directory
        register_shutdown_function(function() use ($host_process_dir) {
            \Flexio\Base\Util::rmtree($host_process_dir);
        });


        //echo("Time c2 " . (microtime(true)-$c1) . ";");


        // find out if we need to start a container
        //self::debugstep($c1, "Looking for IPC file $host_container_zmq");

        if (file_exists($host_container_zmq))
        {
            //self::debugstep($c1, "IPC file $host_container_zmq exists");

            $zmqsock_client = new \ZMQSocket($zmq_context, \ZMQ::SOCKET_REQ);
            $zmqsock_client->connect("ipc://$host_container_zmq");
            $zmqsock_client->setSockOpt(\ZMQ::SOCKOPT_SNDTIMEO, 1000);
            $zmqsock_client->setSockOpt(\ZMQ::SOCKOPT_RCVTIMEO, 1000);
            $zmqsock_client->_is_bound = true;

            try
            {
                //echo "Sending message to ipc://$host_container_zmq <br/>";
                //self::debugstep($c1, "Sending 'hello' to ZMQ");

                $zmqsock_client->send('{"cmd":"hello"}');

                $message = $zmqsock_client->recv();
                if ($message === false)
                {
                    //self::debugstep($c1, "zmq_client->recv() returned false");
                    $zmqsock_client = null;
                }
                 else
                {
                    $message = @json_decode($message, true);
                    if (($message['response'] ?? '') == 'hello')
                    {
                        //self::debugstep($c1, "Received 'hello' response");
                    }
                    else
                    {
                        //self::debugstep($c1, "hello message returned something other than hello: " . json_encode($message));
                        $zmqsock_client = null;
                    }
                }
            }
            catch(\Exception $e)
            {
                //echo $e->getMessage();
                // error occurred, start a new server
                //echo "No controller found";
                //self::debugstep($c1, "exception during hello command");

                $zmqsock_client = null;
            }
        }
         else
        {
            //echo "No controller file found ($host_container_zmq)";
        }

        //echo("Time c3 " . (microtime(true)-$c1) . ";");

        if (is_null($zmqsock_client))
        {
            //self::debugstep($c1, "ZMQ client not yet initialized - starting docker container");

            $uid = posix_getuid();
            $cmd = "$g_dockerbin run -d --net=host --rm --name $container_name -v $host_base_dir:$cont_base_dir  -i fxruntime /fxruntime/fxcontroller $uid";

            //echo "./update-docker-images && " . str_replace(" -d ", " ", str_replace(" -i", " -it", $cmd));
            //ob_end_flush();
            //flush();

            //self::debugstep($c1, "Executing $cmd 2>&1");

            exec("$cmd 2>&1", $output_lines, $exit_code);

            //self::debugstep($c1, "Ok...now waiting for host_container_zmq file to show up: Path is $host_container_zmq");


            $count = 0;
            while (!file_exists($host_container_zmq))
            {
                usleep(50000); // sleep 50ms
                if (++$count == 600)
                {
                    // 30 seconds expired; stop waiting

                    //self::debugstep($c1, "File didn't show up after waiting 30 seconds; throwing IPC timeout exception");

                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Execute proxy: IPC timeout");
                }
            }

            //self::debugstep($c1, "Setting up ZMQSocket client after docker initialization");

            $zmqsock_client = new \ZMQSocket($zmq_context, \ZMQ::SOCKET_REQ);
            $zmqsock_client->connect("ipc://$host_container_zmq");
            $zmqsock_client->setSockOpt(\ZMQ::SOCKOPT_SNDTIMEO, 1000);
            $zmqsock_client->setSockOpt(\ZMQ::SOCKOPT_RCVTIMEO, 1000);
            $zmqsock_client->_is_bound = true;

            //self::debugstep($c1, "...done");

            //$zmqsock_client->send('{"cmd":"hello"}');
            //$message = $zmqsock_client->recv();
        }


        //echo("Time c4 " . (microtime(true)-$c1) . ";");


        // start a zeromq server with a domain socket
        //self::debugstep($c1, "Setting up ZMQSocket server for host process");

        $container_socket_path = "/fxruntime/base/fxproc-$access_key/process";

        $dsn = "ipc://$host_process_zmq";
        $zmqsock_server = new \ZMQSocket($zmq_context, \ZMQ::SOCKET_REP);
        $zmqsock_server->bind($dsn);

        // recv() should time out every 1000 ms so we can loop below
        $zmqsock_server->setSockOpt(\ZMQ::SOCKOPT_RCVTIMEO, 1000);
        $zmqsock_server->_is_bound = true;

        $engine = $this->engine;


        $command =
        [
            'cmd' => 'run',
            'cmdline' => [ '/fxruntime/fxstart' ],
            'setenv' => [ 'FLEXIO_RUNTIME_KEY' => $access_key,
                          'FLEXIO_RUNTIME_SERVER' => $cont_ipc_address,
                          'FLEXIO_EXECUTE_ENGINE' => $engine,
                          'FLEXIO_EXECUTE_HOME' => $cont_src_dir,
                          'PYTHONDONTWRITEBYTECODE' => '1' ]
        ];

        //self::debugstep($c1, "Sending command to container: " . json_encode($command));

        $zmqsock_client->send(json_encode($command));
        $message = $zmqsock_client->recv();

        //echo("Time c5 " . (microtime(true)-$c1) . ";");


        register_shutdown_function(function() use (&$zmqsock_server, $dsn) {
            if ($zmqsock_server !== null && $zmqsock_server->_is_bound)
            {
                $zmqsock_server->unbind($dsn);
                $zmqsock_server->_is_bound = false;
            }
        });



        //echo("Time c6 " . (microtime(true)-$c1) . ";");


        $start_time = microtime(true);
        $actual_start_time = null;    // actual_start_time is initially set when the get_script hook is called

        $connection_established = false;
        $call_count = 0;

        $last_check = 0;

        while (true)
        {
            $message = $zmqsock_server->recv();

            if ($message !== false)
            {
                //self::debugstep($c1, "Received message from container: $message");

                $message = @json_decode($message);

                if ($message === null)
                {
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Execute proxy: decoding fault");
                }
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
                        $zmqsock_server->send(json_encode($response));
                    }
                    else if ($method == 'compile_error')
                    {
                        $retval = call_user_func_array([ $this, 'func_'.$method ], $message['params'] ?? ['']);
                        $response = [ 'result' => true, 'id' => $message['id'] ];
                        $zmqsock_server->send(json_encode($response));
                    }
                    else if ($method == 'exit_loop')
                    {
                        $response = [ 'result' => true, 'id' => $message['id'] ];
                        $zmqsock_server->send(json_encode($response));
                        break;
                    }
                    else
                    {
                        $this->onMessage($zmqsock_server, $message, $c1);
                    }

                    if ($call_count == 0)
                    {
                        //echo("Time c7 " . (microtime(true)-$c1) . ";");
                    }

                    ++$call_count;
                }
                 else
                {
                    $response = [ 'result' => false, 'id' => 'Invalid credentials' ];
                    $zmqsock_server->send(json_encode($response));
                }
            }

            if (!is_null($actual_start_time))
            {
                $execution_seconds = (int)floor(microtime(true) - $actual_start_time);
                if ($execution_seconds > $max_execution_time)
                {
                    $exception = \Flexio\Base\Error::GENERAL;
                    $exception_msg = "Maximum execution time exceeded";
                    break;
                }
            }

            if ($call_count == 0)
            {
                $seconds = (int)floor(microtime(true) - $start_time);
                if ($seconds - $last_check > 3000) /*300*/
                {
                    $last_check = $seconds;
                    $full_state = get_docker_full_state($container_name);
                    $is_running = (strpos($full_state, 'running') !== false || (strpos($full_state, 'created') !== false));

                    // if the container says it's running -- give it another chance (check in 30 seconds)

                    // but if the final threshold (300 seconds/5 minutes) has expired, something has gone wrong;
                    // terminate the execute job with an exception (but break first and clean up the socket etc)

                    if ($seconds >= 300 || !$is_running)
                    {
                        $exception = \Flexio\Base\Error::GENERAL;
                        $exception_msg = "Execute proxy: IPC timeout";
                        //$exception_msg = "Execute proxy: IPC timeout Container=$container_name Exit Code=$exit_code Output=$docker_exec_output State=$full_state";
                        break;
                    }
                }
            }
        }


        //echo("Time c8 " . (microtime(true)-$c1) . ";");


        if ($zmqsock_server->_is_bound)
        {
            $zmqsock_server->unbind($dsn);
            $zmqsock_server->_is_bound = false;
            $zmqsock_server = null;
        }

        //@unlink($host_socket_path);

        // docker container stopped normally, so it does not need to be terminated; remove from the container GC list
        gc_container_remove($container_name);

        if (!is_null($exception))
        {
            throw new \Flexio\Base\Exception($exception, $exception_msg);
        }

        //echo("Time c9 " . (microtime(true)-$c1) . ";");
        //self::debugstep($c1, "Total runtime " . '' . (microtime(true)-$c1));
    }

    public function func_compile_error($error)
    {
        $pos = strpos($error, "bin.b64:");
        if ($pos !== false)
        {
            $this->compile_error .= base64_decode(substr($error, $pos+8));
        }
        else
        {
            $this->compile_error .= $error;
        }
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



    public function onMessage(\ZMQSocket $zmqsock_server, array $msg, $logfile) : void
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

        //self::debugstep($logfile, "Sending response to container: " . json_encode($response));

        $zmqsock_server->send(json_encode($response));
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
                $form[substr($k,5)] = $v->getReader()->read(PHP_INT_MAX); // get all the contents; read() takes a default parameter that limits read size
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
                               'content_type' => $file->stream->getMimeType());
        }

        $this->context_files = $results;
        return $this->context_files;
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
            if ($c->allows($owner_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_READ) === false)
                continue;

            $properties = $c->get();
            $results[] = [ 'eid' => $properties['eid'], 'name' => $properties['name'], 'description' => $properties['description'] ];
        }

        return $results;
    }

    public function func_getLocalConnections() : array
    {
        $results = array();

        $local_connections = $this->getProcess()->getLocalConnections();
        foreach ($local_connections as $key => $value)
        {
            $results[] = [ 'eid' => $key, 'name' => $key, 'description' => '' ];
        }

        return $results;
    }

    public function func_getConnectionAccessToken(string $identifier) // TODO: add return type
    {
        // first, check the process's local connections for a hit
        $local_connection_properties = $this->getProcess()->getLocalConnection($identifier);
        if ($local_connection_properties)
        {
            $connection_type = $local_connection_properties['connection_type'];
            $connection_info = $local_connection_properties['connection_info'];
            $service = \Flexio\Services\Factory::create($connection_type, $connection_info);
            if (!($service instanceof \Flexio\IFace\IOAuthConnection))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

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

            $service = $connection->getService();
            if (!($service instanceof \Flexio\IFace\IOAuthConnection))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $tokens = $service->getTokens();
            return $tokens['access_token'];
        }
    }

    public function func_getConnectionCredentials(string $identifier) // TODO: add return type
    {
        // first, check the process's local connections for a hit
        $local_connection_properties = $this->getProcess()->getLocalConnection($identifier);
        if ($local_connection_properties)
        {
            return (object)($local_connection_properties['connection_info']??[]);
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

            return (object)($connection->get()['connection_info']??[]);
        }
    }

    public function func_getEnv() // TODO: add return type
    {
        $res = [];
        $params = $this->process->getParams();
        foreach ($params as $k => $v)
        {
            $value = $v->getReader()->read(PHP_INT_MAX); // get all the contents; read() takes a default parameter that limits read size

            // these variables may include mount parameters; these can be normal
            // string key/values, or in one case, a key with a json-encoded value
            // that contains connection info; these parameters have a special mime
            // type, and we want to decode the json so it gets passed on to the script
            // as a dictionary object; see \Flexio\Jobs\ProcessHandler::addMountParams()
            $mime_type = $v->getMimeType();
            if ($mime_type === \Flexio\Base\ContentType::FLEXIO_CONNECTION_INFO)
                $value = @json_decode($value, true);

            $res[$k] = $value;
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
                         'content_type' => $file->stream->getMimeType());
        }


        $vfs = new \Flexio\Services\Vfs($this->process->getOwner());
        $vfs->setProcess($this->process);

        $info = $vfs->getFileInfo($path);

        $properties = [ 'mime_type' => ($info['content_type'] ?? 'application/octet-stream') ];
        if (isset($info['structure']))
            $properties['structure'] = $info['structure'];

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

        if ($data instanceof BinaryData)
        {
            $writer->write($data->getData());
        }
        else if (is_object($data))
        {
            $writer->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        }
        else if (is_array($data))
        {
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


    public function func_kvGet(string $key)
    {
        global $g_config;

        $session_redis_host = $g_config->session_redis_host ?? '';
        $session_redis_port = $g_config->session_redis_port ?? '';
        if (strlen($session_redis_host) == 0)
            return false;

        $owner_user_eid = $this->getProcess()->getOwner();
        $store_key = "kv.$owner_user_eid.$key";

        $redis = new \Redis();
        $redis->connect($session_redis_host, $session_redis_port);
        return $redis->get($store_key);
    }

    public function func_kvSet(string $key, $value)
    {
        global $g_config;

        $session_redis_host = $g_config->session_redis_host ?? '';
        $session_redis_port = $g_config->session_redis_port ?? '';
        if (strlen($session_redis_host) == 0)
            return false;

        $owner_user_eid = $this->getProcess()->getOwner();
        $store_key = "kv.$owner_user_eid.$key";

        $redis = new \Redis();
        $redis->connect($session_redis_host, $session_redis_port);
        $redis->set($store_key, $value);
    }

    public function func_kvIncr(string $key, $value)
    {
        global $g_config;

        $session_redis_host = $g_config->session_redis_host ?? '';
        $session_redis_port = $g_config->session_redis_port ?? '';
        if (strlen($session_redis_host) == 0)
            return false;

        $owner_user_eid = $this->getProcess()->getOwner();
        $store_key = "kv.$owner_user_eid.$key";

        $redis = new \Redis();
        $redis->connect($session_redis_host, $session_redis_port);
        if ($value == 1)
            $redis->incr($store_key);
        else
            $redis->incrBy($store_key, $value);
    }

    public function func_kvDecr(string $key, $value)
    {
        global $g_config;

        $session_redis_host = $g_config->session_redis_host ?? '';
        $session_redis_port = $g_config->session_redis_port ?? '';
        if (strlen($session_redis_host) == 0)
            return false;

        $owner_user_eid = $this->getProcess()->getOwner();
        $store_key = "kv.$owner_user_eid.$key";

        $redis = new \Redis();
        $redis->connect($session_redis_host, $session_redis_port);
        if ($value == 1)
            $redis->decr($store_key);
        else
            $redis->decrBy($store_key, $value);
    }
}



class Execute implements \Flexio\IFace\IJob
{
    private $properties = array();
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

    public static function validate(array $task) : array
    {
        $errors = array();
        $job_params = $task;

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

    public static function run(\Flexio\IFace\IProcess $process, array $task) : void
    {
        unset($task['op']);

        // don't replace parameters to save a little bit of work;
        // logic is in script so no replacement necessary
        //\Flexio\Jobs\Util::replaceParameterTokens($process, $task);

        $object = new static();
        $object->properties = $task;
        $object->run_internal($process);
    }

    private function run_internal(\Flexio\IFace\IProcess $process) : void
    {
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
            $ep->initialize($process->getOwner(), $this->lang, $this->code, $script_host);
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
                'mime_type' => \Flexio\Base\ContentType::FLEXIO_HTML
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

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}
