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
// EXAMPLE:
{
    "op": "execute",
    "params": {
        "lang": "python",
        "code": "<base64 encoded>",
        "path": "",
        "integrity": ""
    }
}
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
    const MESSAGE_SIGNATURE = '--MSGqQp8mf~';
    public $pipes = [null,null,null];
    public $process = null;
    public $check_sig = '';
    public $callbacks = null;

    public function initialize($cmd, $callbacks)
    {
        $this->callbacks = $callbacks;

        $descriptor_spec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $this->process = proc_open($cmd, $descriptor_spec, $this->pipes);
        if (!is_resource($this->process))
        {
            $this->process = null;
            return false;
        }

        stream_set_blocking($this->pipes[0], true);  // write to here
        stream_set_blocking($this->pipes[1], false); // read from here
    }

    public function run()
    {
        while (!feof($this->pipes[1]))
        {
            $read_streams = array( $this->pipes[1] );
            $write_streams = NULL;
            $except_streams = NULL;
            $res = stream_select($read_streams, $write_streams, $except_streams, 1);
            if ($res === false)
                break;

            if ($res > 0)
            {
                $ch = fread($this->pipes[1], 1);

                $this->check_sig = substr($this->check_sig . $ch, 0, 12);

                if ($this->check_sig == self::MESSAGE_SIGNATURE)
                {
                    $length = '';
                    $this->check_sig = '';

                    for ($i = 0; $i < 12; ++$i)
                    {
                        $ch = fread($this->pipes[1], 1);
                        if ($ch < '0' || $ch > '9')
                            break;
                        $length .= $ch;
                    }

                    if ($ch == ',')
                    {
                        $length = (int)$length;

                        $payload = '';
                        $remaining = $length;
                        while ($remaining > 0)
                        {
                            $want = min(8192, $remaining);
                            $chunk = fread($this->pipes[1], $want);
                            $remaining -= strlen($chunk);
                            $payload .= $chunk;
                        }

                        if (strlen($payload) == $length)
                        {
                            $this->onMessage($payload);
                        }
                         else
                        {
                            //echo "Only got " . strlen($payload) . " wanted $length";
                        }
                    }

                }
            }

            //var_dump($res);
        }

/*
        echo "Stderr from child process was:\n";
        $str = fread($this->pipes[2], 8192);
        echo $str;
        die($str);
*/
    }

    public function getStdError()
    {
        $res = fread($this->pipes[2], 8192);
        if (strlen(trim($res)) == 0)
            return null;
        return $res;
    }

    public static function encodepart($val)
    {
        if (is_null($val))
        {
            return 'N0,';
        }
        else if ($val instanceof BinaryData)
        {
            $type = 'B';
            $val = $val->data;
        }
        else if (is_int($val))
        {
            $type = 'i';
            $val = (string)$val;
        }
        else if (is_bool($val))
        {
            $type = 'b';
            $val = $val ? '1':'0';
        }
        else if (is_array($val))
        {
            if (count($val) == 0 || array_keys($val) === range(0, count($val) - 1))
            {
                // sequential array
                $type = 'a';
                $payload = '';
                foreach ($val as $v)
                {
                    $payload .= self::encodepart($v);
                }
                $val = $payload;
            }
             else
            {
                // assoc array
                $type = 'o';
                $payload = '';
                foreach ($val as $k => $v)
                {
                    $payload .= self::encodepart($k);
                    $payload .= self::encodepart($v);
                }
                $val = $payload;
            }
        }
        else if (is_object($val))
        {
            // assoc array passed as object
            $type = 'o';
            $payload = '';
            foreach ((array)$val as $k => $v)
            {
                $payload .= self::encodepart($k);
                $payload .= self::encodepart($v);
            }
            $val = $payload;
        }
        else
        {
            $type = 's';
        }

        return $type . strlen($val) . ',' . $val;
    }

    public function parseCallString($s)
    {
        $offset = 0;
        $strlen = strlen($s);

        $result = [];

        while ($offset < $strlen)
        {
            // get type -- and make sure it's supported
            $type = substr($s, $offset, 1);
            $offset++;

            if (strpos("sibfaoBN", $type) === false)
                return false; // unsupported type

            // find comma
            $comma_offset = false;
            for ($i = $offset; $i < min($strlen,$offset+11); ++$i)
            {
                if ($s[$i] == ',')
                {
                    $comma_offset = $i;
                    break;
                }
                if (strpos("01234567889", $s[$i]) === false)
                    return false; // length must be integer
            }
            if ($comma_offset === false || $comma_offset == $offset)
                return false;

            // get length
            $length = intval(substr($s, $offset, $comma_offset - $offset));

            // get content
            $offset = $comma_offset+1;
            $content = substr($s, $offset, $length);
            $offset += $length;

            if ($type == 'B')
                $content = new BinaryData($content);
            if ($type == 'i')
                $content = (int)$content;
            if ($type == 'f')
                $content = floatval($content);
            else if ($type == 'N')
                $content = null;
            else if ($type == 'b')
                $content = ($content == '0' ? false : true);
            else if ($type == 'a')
                $content = $this->parseCallString($content);
            else if ($type == 'o')
            {
                // objects are stored in <key><value><key><value>... format
                $arr = $this->parseCallString($content);
                $size = count($arr);

                if (($size % 2) != 0)
                    return false;

                $content = [];
                for ($idx = 0; $idx < $size; $idx += 2)
                {
                    $content[ $arr[$idx] ] = $arr[$idx+1];
                }

                $content = (object)$content;
            }

            $result[] = $content;
        }

        return $result;
    }


    public function sendMessage($msg)
    {
        fwrite($this->pipes[0], self::MESSAGE_SIGNATURE . strlen($msg) . ',' . $msg);
        fflush($this->pipes[0]);
    }

    public function onMessage($msg)
    {
        $arr = $this->parseCallString($msg);
        //var_dump($arr);

        if ($arr === false || count($arr) == 0)
        {
            // bad parse -- send exception
            $this->sendMessage("E19,Request parse error");
            /*
            $msg = "Request parse error" . var_export($msg,true);
            $msg = "E" . strlen($msg) . ',' . $msg;
            $this->sendMessage($msg);
            */
            return;
        }

        $func = array_shift($arr);

        // check if function exists
        if (!method_exists($this->callbacks, 'func_' . $func))
        {
            // bad parse -- send exception
            $this->sendMessage("E14,Unknown method");
            return;
        }

        // make function call
        $retval = call_user_func_array([ $this->callbacks, 'func_'.$func ], $arr);

        //  Send reply back to client
        $this->sendMessage(self::encodepart($retval));
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
    public function setProcess($process)
    {
        $this->process = $process;
    }

    //public function getProcess() : \Flexio\IFace\Process
    public function getProcess()
    {
        return $this->process;
    }

    private function getInputReader($idx)
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

    private function getOutputWriter($idx)
    {
        if (count($this->output_writers) != count($this->output_streams))
            $this->output_writers = array_pad($this->output_writers, count($this->output_streams), null);

        if ($idx < 0 || $idx >= count($this->output_writers))
            return null;

        $ret = $this->output_writers[$idx];
        if (is_null($ret))
        {
            $output_stream = $this->output_streams[$idx];
            $ret = $output_stream->getWriter();
            $this->output_writers[$idx] = $ret;
        }

        return $ret;
    }

    public function func_hello($message)
    {
        return "Parameter = $message";
    }

    public function func_exit($response_code)
    {
        $this->process->setExitCode((int)$response_code);
    }

    public function func_setenv($key,$value)
    {
        $env = $this->process->getParams();
        $env[$key] = $value;
        $this->process->setParams($env);
    }


    public function func_getQueryParameters()
    {
        return (object)$_GET;
    }

    public function func_getFormParameters()
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

    public function func_getFilesParameters()
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

    public function func_runJob($json)
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


    public function func_debug($message)
    {
        die($message);
    }

    public function func_getEnv()
    {
        $res = [];
        $params = $this->process->getParams();
        foreach ($params as $k => $v)
        {
            $res[$k] = $v->getReader()->read();
        }
        return (object)$res;
    }

    public function func_setEnvValue($key, $value)
    {
        $env = $this->process->getParams();
        $env[(string)$key] = (string)$value;
        $this->process->setParams($env);
        return true;
    }


    public $stream_cache = array();

    private function __getInputStreamInfo($name)
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

    public function func_getInputStreamInfo($name)
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

    private function __getOutputStreamInfo($name)
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

    public function func_getOutputStreamInfo($name)
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


    public function func_setOutputStreamInfo($idx, $properties)
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

    public function func_createOutputStream($properties)
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

    public function func_managedCreate($stream_idx, $properties)
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

    public function func_getInputStreamStructure($stream_idx)
    {
        if ($stream_idx < 0 || $stream_idx >= count($this->input_streams))
            return false;

        $stream = $this->input_streams[$stream_idx];

        return $stream->getStructure()->enum();
    }

    public function func_insertRow($stream_idx, $row)
    {
        $writer = $this->getOutputWriter($stream_idx);
        if (is_null($writer))
            return null;

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

    public function func_insertRows($stream_idx, $row)
    {
        $writer = $this->getOutputWriter($stream_idx);
        if (is_null($writer))
            return null;

        foreach ($rows as $row)
        {
            if (is_object($row))
                $row = (array)$row;
            $writer->write($row);
        }
    }

    public function func_write($handle, $data)
    {
        $writer = $this->getOutputWriter($handle);
        if (is_null($writer))
            return null;

        if ($data instanceof BinaryData)
        {
            $writer->write($data->getData());
        }
        else if (is_object($data))
        {
            if (isset($this->output_streams[$handle]) && $this->output_streams[$handle]->isTable())
                $writer->write((array)$data);
                 else
                $writer->write(json_encode($data));
        }
        else
        {
            $writer->write((string)$data);
        }
    }


    public function func_read($stream_idx, $length)
    {
        $reader = $this->getInputReader($stream_idx);
        if (is_null($reader))
            return false;

        $res = $reader->read($length);

        if ($res === false)
            return false;
        return new BinaryData($res);
    }

    public function func_readline($stream_idx, $associative)
    {
        $reader = $this->getInputReader($stream_idx);
        if (is_null($reader))
            return false;

        $res = $reader->readRow();
        if ($res === false)
            return false;
        return $associative ? $res : array_values($res);
    }
}



class Execute extends \Flexio\Jobs\Base
{
    private $code_base64 = '';
    private $code = '';
    private $process = null;

    private static function getFileContents(string $url) : string
    {
        // load the service
        $connection_info = array(
            'connection_type' => \Flexio\Services\Factory::TYPE_HTTP
        );

        $service = \Flexio\Services\Factory::create($connection_info);
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        // get the contents
        $contents = '';
        $service->read(array('path'=>$url), function($data) use (&$contents) {
            $contents .= $data;
        });

        return $contents;
    }

    public function validate() : array
    {
        $errors = array();
        $properties = $this->getProperties();

        if (!isset($properties['op']))
            $errors[] = array('code' => \Flexio\Base\Error::INVALID_PARAMETER, 'message' => '');

        if (!isset($properties['params']))
            $errors[] = array('code' => \Flexio\Base\Error::INVALID_PARAMETER, 'message' => '');

        if (count($errors) > 0)
            return $errors;

        try
        {
            $job_params = $properties['params'];

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

    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $this->process = $process;

        // properties
        $job_definition = $this->getProperties();

        // get the language
        $this->lang = $job_definition['params']['lang'];

        // if code is specified, get the contents from the supplied code
        $code = $job_definition['params']['code'] ?? false;
        if ($code !== false)
        {
            $this->code_base64 = $job_definition['params']['code'];
            $this->code = base64_decode($this->code_base64);
        }
         else
        {
            // if the code isn't specified in a file location, see if it's specified
            // in a remote location
            $file = $job_definition['params']['path'] ?? false;
            if ($file === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

            $this->code = self::getFileContents($file);
            $this->code_base64 = base64_encode($this->code);
        }

        $integrity = $job_definition['params']['integrity'] ?? false;
        if ($integrity !== false)
        {
            // an integrity parameter is specified; use a subresource integrity check
            // (https://www.w3.org/TR/SRI/) to verify the code (e.g. "sha384:<base64-encoded-hash-value>");
            // if a hash of the code using the provided algorithm doesn't match the value in
            // the integrity check, throw an exception
            if (!is_string($integrity))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            // use ":" as format/code separator
            $parts = explode(':', $integrity);
            if (count($parts) !== 2)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $integrity_hashformat = strtolower($parts[0]);
            $integrity_hashvalue = strtolower($parts[1]);

            $code_hashvalue = false;
            switch ($integrity_hashformat)
            {
                // make sure the integrity is a supported format; currently, the required formats
                // in the standard are sha256, sha384, and sha512, and weak formats, such as md5
                // and sha1 should be refused
                default:
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

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

        if ($this->lang == 'python')
        {
            // if a flexio_hander is specified, call it, otherwise let the script handle everything
            if (strpos($this->code, "flexio_handler") !== false)
            {
                // add code that invokes the main handler -- this is the preferred
                // way of coding python scripts
                if (false !== strpos($this->code, "\r\n"))
                    $endl = "\r\n";
                    else
                    $endl = "\n";

                $this->code .= $endl . "import flexio as flexioext";
                $this->code .= $endl . "flexioext.run(flexio_handler)";
                $this->code .= $endl;

                $this->code_base64 = base64_encode($this->code);
            }

            $dockerbin = \Flexio\System\System::getBinaryPath('docker');
            if (is_null($dockerbin))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxruntime sh -c '(echo ".$this->code_base64." | base64 -d > /fxpython/script.py && timeout 30s python3 /fxpython/script.py)'";

            $script_host = new ScriptHost();
            $script_host->setProcess($process);

            $ep = new ExecuteProxy;
            $ep->initialize($cmd, $script_host);
            $ep->run();

            $err = $ep->getStdError();

            if (isset($err))
            {
                $err = trim(str_replace('read unix @->/var/run/docker.sock: read: connection reset by peer', '', $err));
                if (strlen($err) == 0)
                    $err = null;
            }

            if (isset($err))
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, $err);
            }

            return true;
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
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxruntime sh -c '(echo ".$this->code_base64." | base64 -d > /fxnodejs/script.js && timeout 30s nodejs /fxnodejs/run.js unmanaged /fxnodejs/script.js)'";

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

            return true;
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

                $json = json_encode($rows);

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

            return true;
        }

    }

    // checks a script for compile errors;  If script compiles cleanly, returns true,
    // otherwise returns the error as a textual string

    public static function checkScript(string $lang, string $code)
    {
        // only python supported for now
        if ($lang == 'python')
        {
            $code = base64_encode($code);

            $dockerbin = \Flexio\System\System::getBinaryPath('docker');
            if (is_null($dockerbin))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }
    }
}
