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
    "lang": "",       // string, required, enum: python|javascript
    "code": "",       // string (base64 encoded string of code to run); either "code" or "path" is required
    "path": "",       // string (url to remote code to execute); either "code" or "path" is required
    "integrity": ""   // string (integrity check; sha256, sha384, sha512 allowed with format: <sha-type>:<integrity-check>
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['execute']),
        'lang'       => array('required' => true,  'enum' => ['python','javascript']),
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

    public function run() : void
    {
        // generate a key which will be used as a kind of password
        $access_key = \Flexio\Base\Util::generateRandomString(20);

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

        // start a zeromq server -- let OS choose port

        $host_socket_path = "/dev/shm/ipc-exec-$access_key";
        $container_socket_path = "/tmp/ipc-endpoint";
        $container_ipc_address = "ipc://$container_socket_path";

        $server = new \ZMQSocket(new \ZMQContext(), \ZMQ::SOCKET_REP);
        $server->bind("ipc://$host_socket_path");

        if (file_exists($host_socket_path))
        {
            register_shutdown_function('unlink', $host_socket_path);
        }


        // recv() should time out every 250 ms
        $server->setSockOpt(\ZMQ::SOCKOPT_RCVTIMEO, 250);
        //$server->setSockOpt(\ZMQ::SOCKOPT_SNDTIMEO, 250);


        // run the container command

        $dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        //$cmd = "./update-docker-images && $dockerbin run --rm -e FLEXIO_RUNTIME_KEY=$access_key -e FLEXIO_RUNTIME_SERVER=tcp://$address:$port -i fxruntime timeout 3600s python3 /fxpython/fxstart.py";
        //echo $cmd;
        //ob_end_flush();
        //flush();

        $engine = $this->engine;

        $cmd = "$dockerbin run --rm -v $host_socket_path:$container_socket_path -e FLEXIO_RUNTIME_KEY=$access_key -e FLEXIO_RUNTIME_SERVER=$container_ipc_address -e FLEXIO_EXECUTE_ENGINE=$engine -i fxruntime timeout 3600s python3 /fxpython/fxstart.py";

        //echo "./update-docker-images && $cmd";
        //ob_end_flush();
        //flush();

        exec("$cmd  > /dev/null  &");

        $start_time = microtime(true);
        $connection_established = false;
        $call_count = 0;

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

            if ($call_count == 0 && (microtime(true) - $start_time) > 30)
            {
                // if we haven't yet received our first call after 30 seconds, something is wrong;
                // terminate the execute job with an exception

                throw new \Flexio\Base\Exception(\Flexio\Base\Error::GENERAL, "Execute proxy: IPC timeout");
            }

            /*
            if ((microtime(true) - $start_time) > 16)
            {
                die("TIMED OUT");
            }
            */

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

        $moniker = "~$id/bin.b64:";
        $moniker_len = strlen($moniker);

        array_walk_recursive($args, function(&$v, $k) use ($moniker, $moniker_len) {
            if (is_string($v) && substr($v, 0, $moniker_len) == $moniker)
                $v = new BinaryData(base64_decode(substr($v, $moniker_len)));
        });

        // make function call
        if (!is_array($args))
            $args = [ $args ];
        $retval = call_user_func_array([ $this->callbacks, 'func_'.$method ], $args);


        if (is_array($retval))
        {
            array_walk_recursive($retval, function(&$v, $k) use ($moniker, $moniker_len) {
                if ($v instanceof BinaryData)
                    $v = $moniker . base64_encode($v->data);
            });
        }
         else
        {
            if ($retval instanceof BinaryData)
                $retval = $moniker . base64_encode($retval->data);
        }


        //  Send reply back to client
        $response = [ 'result' => $retval, 'id' => $msg['id'] ];
        $server->send(json_encode($response));
    }
}

class ScriptHost
{
    protected $process = null;

    private $input_map = [];
    private $input_streams = [];
    private $input_readers = [];
    private $output_map = [];
    private $output_streams = [];
    private $output_writers = [];
    private $context_files = null;


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
    }

    //public function getProcess() : \Flexio\IFace\Process
    public function getProcess() // TODO: add return type
    {
        return $this->process;
    }

    private function getInputReader(int $idx) // TODO: add return type
    {
        if (count($this->input_readers) != count($this->input_streams))
            $this->input_readers = array_pad($this->input_readers, count($this->input_streams), null);

        if ($idx < 0 || $idx >= count($this->input_readers))
            return null;

        $ret = $this->input_readers[$idx];
        if (is_null($ret))
        {
            $input_stream = $this->input_streams[$idx];
            $ret = $input_stream->getReader();
            $this->input_readers[$idx] = $ret;
        }

        return $ret;
    }

    private function getOutputWriter(int $idx, bool $reset = false) // TODO: add return type
    {
        if (count($this->output_writers) != count($this->output_streams))
            $this->output_writers = array_pad($this->output_writers, count($this->output_streams), null);

        if ($idx < 0 || $idx >= count($this->output_writers))
            return null;

        $ret = $this->output_writers[$idx];
        if (is_null($ret) || $reset === true)
        {
            $output_stream = $this->output_streams[$idx];
            $ret = $output_stream->getWriter();
            $this->output_writers[$idx] = $ret;
        }

        return $ret;
    }

    public function func_hello(string $message) : string
    {
        return "Parameter = $message";
    }

    public function func_exit($response_code) : void
    {
        $this->process->setExitCode((int)$response_code);
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

    public function func_getFilesParameters() // TODO: add return type
    {
        if ($this->context_files)
        {
            return $this->context_files;
        }

        $files = $this->process->getFiles();
        foreach ($files as $k => $stream)
        {
            $files[$k] = [ 'name' => $stream->getName(),
                           'size' => $stream->getSize(),
                           'mime_type' => $stream->getMimeType() ];
        }

        $this->context_files = (object)$files;
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

    public function func_debug($message) : void
    {
        if ($message instanceof BinaryData) {
            var_dump("BINARY DATA");
        }
        var_dump($message);
        die();
        //die($message);
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

    private function __getInputStreamInfo(string $name) : ?array
    {
        if ($name === '_fxstdin_')
        {
            $stdin = $this->process->getStdin();
            $this->input_streams[] = $stdin;
            return array('handle' => count($this->input_streams)-1,
                         'name' => '',
                         'size' => 0,
                         'content_type' => (isset($stdin) ? $stdin->getMimeType() : 'application/octet-stream'));
        }
        else
        {
            $files = $this->process->getFiles();
            if (isset($files[$name]))
            {
                $stream = $files[$name];
                $this->input_streams[] = $stream;
                return array('handle' => count($this->input_streams)-1,
                             'name' => $name,
                             'size' => $stream->getSize(),
                             'content_type' => $stream->getMimeType());
            }
             else
            {
                return null;
            }
        }
    }

    public function func_getInputStreamInfo(string $name) : ?array
    {
        if (isset($this->input_map[$name]))
        {
            return $this->input_map[$name];
        }

        $res = $this->__getInputStreamInfo($name);
        if ($res === null)
            return null;

        $this->input_map[$name] = $res;
        return $res;
    }

    public function func_getVfsStreamHandle(string $path) : int
    {
        if (isset($this->input_map[$path]))
        {
            return $this->input_map[$path];
        }

        $stream = \Flexio\Base\Stream::create();
        $streamwriter = $stream->getWriter();


        $vfs = new \Flexio\Services\Vfs($this->process->getOwner());
        $vfs->setProcess($this->process);

        $files = $vfs->read($path, function($data) use (&$streamwriter) {
            $streamwriter->write($data);
        });


        $this->input_streams[] = $stream;
        $info = array('handle' => count($this->input_streams)-1,
                      'name' => $path,
                      'size' => $stream->getSize(),
                      'content_type' => 'application/octet-stream');

        $this->input_map[$path] = $info;
        return $info['handle'];
    }

    public function func_fs_exists(string $path) : bool
    {
        $vfs = new \Flexio\Services\Vfs($this->process->getOwner());
        $vfs->setProcess($this->process);
        return $vfs->exists($path);
    }

    private function __getOutputStreamInfo(string $name) : ?array
    {
        if ($name === '_fxstdout_')
        {
            $stdout = $this->process->getStdout();
            $this->output_streams[] = $stdout;
            return array('handle' => count($this->output_streams)-1,
                         'name' => '',
                         'size' => 0,
                         'content_type' => $stdout->getMimeType());
        }
        else
        {
            return null;
        }
    }

    public function func_getOutputStreamInfo(string $name) : ?array
    {
        if (isset($this->output_map[$name]))
        {
            return $this->output_map[$name];
        }

        $res = $this->__getOutputStreamInfo($name);
        if ($res === null)
            return null;

        $this->output_map[$name] = $res;
        return $res;
    }

    public function func_setOutputStreamInfo($idx, $properties) : bool
    {
        if ($idx < 0 || $idx >= count($this->output_streams))
            return false;

        if (!is_array($properties))
            $properties = (array)$properties;

        if (isset($properties['content_type']))
            $properties['mime_type'] = $properties['content_type'];

        $properties = \Flexio\Base\Util::filterArray($properties, ["name","mime_type"]);
        $this->output_streams[$idx]->set($properties);

        return true;
    }

    public function func_createOutputStream($properties) : array
    {
        // we've removed the "looped stream" handling in the logic, so
        // we no longer support arbitrary creation and subsequent processing
        // an array of streams; we'll probably want to bring back this notion,
        // but using parameters and explicit operations on those parameters
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::DEPRECATED);


        $name = $properties['name'] ?? '';
        $content_type = $properties['content_type'] ?? 'text/plain';
        $structure = $properties['structure'] ?? null;

        $properties = array(
            'name' => $name,
            'mime_type' => $content_type
        );

        if (!is_null($structure))
        {
            // specifying a structure automatically sets content type to table
            $properties['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
            $properties['structure'] = $structure;
        }

        $stream = \Flexio\Base\Stream::create($properties);
        $this->process->addStream($stream);
        $this->output_streams[] = $stream;

        return array('idx' => count($this->output_streams)-1,
                     'name' => $stream->getName(),
                     'size' => $stream->getSize(),
                     'content_type' => $stream->getMimeType());
    }

    public function func_managedCreate(int $stream_idx, $properties) : bool
    {
        if ($stream_idx < 0 || $stream_idx >= count($this->output_streams))
            return false;

        if (is_object($properties))
            $properties = (array)$properties;

        $stream = $this->output_streams[$stream_idx];

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
        if ($stream_idx < 0 || $stream_idx >= count($this->input_streams))
            return false;

        $stream = $this->input_streams[$stream_idx];

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

        if ($data instanceof BinaryData)
        {
            $writer->write($data->getData());
        }
        else if (is_object($data))
        {
            if (isset($this->output_streams[$handle]) && $this->output_streams[$handle]->isTable())
                $writer->write((array)$data);
                 else
                $writer->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        }
        else if (is_array($data))
        {
            if (isset($this->output_streams[$handle]) && $this->output_streams[$handle]->isTable())
                $writer->write((array)$data);
                 else
                $writer->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        }
        else
        {
            $writer->write((string)$data);
        }
    }

    public function func_createVfsStreamHandle(string $path) : int
    {
        $key = \Flexio\Base\Util::generateRandomString(20);

        $stream = \Flexio\Base\Stream::create();
        $stream->setName($path);

        $this->output_streams[] = $stream;
        $info = array('handle' => count($this->output_streams)-1,
                      'name' => $key,
                      'size' => 0,
                      'content_type' => 'application/octet-stream');

        $this->output_map[$key] = $info;

        return $info['handle'];
    }

    public function func_commitVfsStreamHandle(int $stream_idx /* handle */)
    {
        if ($stream_idx < 0 || $stream_idx >= count($this->output_streams))
            return false;

        $writer = $this->getOutputWriter($stream_idx);
        if (is_null($writer))
            return false;

        $this->output_writers[$stream_idx] = null;
        $writer = null;


        $stream = $this->output_streams[$stream_idx];
        $reader = $stream->getReader();

        $vfs = new \Flexio\Services\Vfs($this->process->getOwner());
        $vfs->setProcess($this->process);

        $files = $vfs->write($stream->getName(), function($length) use (&$reader) {
            return $reader->read($length);
        });
    }

    public function func_read(int $stream_idx, $length) // TODO: add return type
    {
        $reader = $this->getInputReader($stream_idx);
        if (is_null($reader))
            return false;

        $res = $reader->read($length);

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
        $this->lang = $job_params['lang'];

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

        if ($this->lang == 'python' || $this->lang == 'javascript')
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
        else if ($this->lang == 'javascript')
        {
            // if a flexio_hander is specified, call it, otherwise let the script handle everything
            if (strpos($this->code, "flexio_handler") !== false)
            {
                $this->code_base64 = base64_encode($this->code);
            }

            $dockerbin = \Flexio\System\System::getBinaryPath('docker');
            if (is_null($dockerbin))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxruntime sh -c '(echo ".$this->code_base64." | base64 -d > /fxnodejs/script.js && timeout 3600s nodejs /fxnodejs/run.js unmanaged /fxnodejs/script.js)'";

            $script_host = new ScriptHost();
            $script_host->setProcess($process);

            $ep = new ExecuteProxy;
            $ep->initialize($cmd, $script_host);
            $ep->run();

            $err = $ep->getStdError();
            if (isset($err))
            {
                //die("<pre>".$err);
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
         else if ($lang == 'javascript')
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
