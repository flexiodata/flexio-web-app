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

            if (strpos("sibaoBN", $type) === false)
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


class Execute extends \Flexio\Jobs\Base
{
    private $code_base64 = '';
    private $code = '';
    private $inputs = [];
    private $input_readers = [];
    private $outputs = [];
    private $output_writers = [];
    private $managed_stream_index = 0;   // "current stream" index running in managed mode

    public function run()
    {
        $this->getOutput()->setEnv($this->getInput()->getEnv()); // by default, pass on all params; however, execute script can change them
        $this->inputs = $this->getInput()->getStreams();


        // properties
        $job_definition = $this->getProperties();

        // get the code from the template
        // 'code' contains the base64-encoded program source
        $this->lang = $job_definition['params']['lang'];
        $this->code_base64 = $job_definition['params']['code'] ?? '';
        if (strlen($this->code_base64) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        $this->code = base64_decode($this->code_base64);

        if ($this->lang == 'python')
        {
            if (strpos($this->code, "flexio_file_handler") !== false)
            {
                // "MANAGED MODE" - script is called once for each stream


                // set up one output stream for each input stream
                foreach ($this->inputs as $instream)
                {
                    // input/output
                    $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());
                    $this->getOutput()->addStream($outstream);

                    // by default, set output content type to text
                    $outstream->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT);

                    $this->outputs[] = $outstream;
                }


                // add code that invokes the main handler -- this is the preferred
                // way of coding python scripts
                if (false !== strpos($this->code, "\r\n"))
                    $endl = "\r\n";
                    else
                    $endl = "\n";

                $this->code .= $endl . "import flexio as flexioext";
                $this->code .= $endl . "flexioext.run_stream(flexio_file_handler)";
                $this->code .= $endl;

                $this->code_base64 = base64_encode($this->code);


                $stream_count = count($this->inputs);
                for ($idx = 0; $idx < $stream_count; ++$idx)
                {
                    $this->managed_stream_index = $idx;
                    $this->doStream($this->inputs[$idx], $this->outputs[$idx]);
                }
                
                return true;
            }
             else
            {
                // "UNMANAGED MODE" - script is called once per job (once per all streams)

                // if a flexio_hander is specified, call it, otherwise let the script
                // handle everything
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

                $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxpython sh -c '(echo ".$this->code_base64." | base64 -d > /tmp/script.py && timeout 30s python3 /tmp/script.py)'";

                $ep = new ExecuteProxy;
                $ep->initialize($cmd, $this);
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
        }


        foreach ($inputs as $instream)
        {
            $mime_type = $instream->getMimeType();
            $this->doStream($instream);
/*
            switch ($mime_type)
            {
                // unhandled input
                default:
                    $this->getOutput()->addStream($instream->copy());
                    break;

                // stream/text/csv input
                case \Flexio\Base\ContentType::MIME_TYPE_STREAM:
                case \Flexio\Base\ContentType::MIME_TYPE_TXT:
                case \Flexio\Base\ContentType::MIME_TYPE_CSV:
                case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                    $this->createOutput($instream);
                    break;
            }
*/
        }
    }

    private function getInputReader($idx)
    {
        if (count($this->input_readers) != count($this->outputs))
            $this->input_readers = array_pad($this->input_readers, count($this->outputs), null);

        if ($idx < 0 || $idx >= count($this->input_readers))
            return null;

        $ret = $this->input_readers[$idx];
        if (is_null($ret))
        {
            $ret = \Flexio\Object\StreamReader::create($this->inputs[$idx]);
            $this->input_readers[$idx] = $ret;
        }

        return $ret;
    }

    private function getOutputWriter($idx)
    {
        if (count($this->output_writers) != count($this->outputs))
            $this->output_writers = array_pad($this->output_writers, count($this->outputs), null);

        if ($idx < 0 || $idx >= count($this->output_writers))
            return null;

        $ret = $this->output_writers[$idx];
        if (is_null($ret))
        {
            $ret = \Flexio\Object\StreamWriter::create($this->outputs[$idx]);
            $this->output_writers[$idx] = $ret;
        }

        return $ret;
    }


    private function doStream(\Flexio\Object\Stream $instream, \Flexio\Object\Stream $outstream)
    {

/*
        $is_input_table = false;
        $is_output_table = false;
        if ($instream->getMimeType() === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
            $is_input_table = true;
*/


        // determine what program to load
        if ($this->lang == 'python')
        {
            $dockerbin = \Flexio\System\System::getBinaryPath('docker');
            if (is_null($dockerbin))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxpython sh -c '(echo ".$this->code_base64." | base64 -d > /tmp/script.py && timeout 30s python3 /tmp/script.py)'";

            $ep = new ExecuteProxy;
            $ep->initialize($cmd, $this);
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
         else if ($this->lang == 'html')
        {
            if (strpos($this->code, 'flexio.input.json_assoc()') !== false)
            {
                $streamreader = \Flexio\Object\StreamReader::create($instream);
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
                'mime_type' => \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_HTML,
                'size' => strlen($code)
            );

            $outstream->set($outstream_properties);

            $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
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

            $cmd = "$dockerbin run --net none --rm -i fxpython python3 -c 'import py_compile; import base64; code=base64.b64decode(\"$code\"); f=open(\"script.py\",\"wb\"); f.write(code); f.close(); py_compile.compile(\"script.py\");'";

            $f = popen($cmd . ' 2>&1' /*grab stderr*/, 'r');
            $str = fread($f, 8192);
            pclose($f);

            if ($str === false || strlen(trim($str)) == 0)
                return true;

            return $str;
        }
         else
        {
            // unknown language
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }
    }






    private function func_hello1($name)
    {
        if (is_array($name))
           $name = var_export($name,true);
        return "Hello, $name";
    }

    private function func_hello2()
    {
        return "Hello2";
    }

    public function func_getInputStreamInfo()
    {
        $res = [];
        $idx = 0;
        foreach ($this->inputs as $stream)
        {
            $res[] = array('idx' => $idx++,
                           'name' => $stream->getName(),
                           'size' => $stream->getSize(),
                           'content_type' => $stream->getMimeType());
        }

        return $res;
    }

    public function func_getOutputStreamInfo()
    {
        $res = [];

        if ($this->outputs)
        {
            $idx = 0;
            foreach ($this->outputs as $stream)
            {
                $res[] = array('idx' => $idx++,
                               'name' => $stream->getName(),
                               'size' => $stream->getSize(),
                               'content_type' => $stream->getMimeType());
            }
        }

        return $res;
    }

    public function func_setOutputStreamInfo($idx, $properties)
    {
        if ($idx < 0 || $idx >= count($this->outputs))
            return false;

        if (isset($properties['content_type']))
            $properties['mime_type'] = $properties['content_type'];
        
        $properties = \Flexio\Base\Util::filterArray($properties, ["name","mime_type"]);
        $this->outputs[$idx]->set($properties);

        return true;
    }

    public function func_createOutputStream($properties)
    {
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
            $properties['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
            $properties['structure'] = $structure;
        }

        $stream = \Flexio\Object\Stream::create($properties);
        $this->getOutput()->addStream($stream);
        $this->outputs[] = $stream;

        return array('idx' => count($this->outputs)-1,
                     'name' => $stream->getName(),
                     'size' => $stream->getSize(),
                     'content_type' => $stream->getMimeType());
    }

    public function func_managedCreate($stream_idx, $properties)
    {
        if ($stream_idx < 0 || $idx >= count($this->outputs))
            return false;

        $stream = $this->outputs[$stream_idx];

        $set = array();

        if (isset($properties['name']))
            $set['name'] = $properties['name'];
        if (isset($properties['content_type']))
            $set['mime_type'] = $properties['content_type'];
        if (isset($properties['structure']))
        {
            $set['mime_type'] = \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE;
            $set['structure'] = $properties['structure'];
        }
        
        $stream->set($set);

        return true;
    }


    public function func_getManagedStreamIndex()
    {
        return $this->managed_stream_index;
    }

    public function func_getInputStreamStructure($stream_idx)
    {
        if ($stream_idx < 0 || $idx >= count($this->inputs))
            return false;

        $stream = $this->inputs[$stream_idx];

        return $stream->getStructure()->enum();
    }

    public function func_insertRow($stream_idx, $row)
    {
        $writer = $this->getOutputWriter($stream_idx);
        if (is_null($writer))
            return null;

        if ($row instanceof BinaryData)
            $writer->write($row->getData());
             else
            $writer->write($row);
    }

    public function func_insertRows($stream_idx, $row)
    {
        $writer = $this->getOutputWriter($stream_idx);
        if (is_null($writer))
            return null;
        
        foreach ($rows as $row)
        {
            $writer->write($row);
        }
    }

    public function func_write($stream_idx, $data)
    {
        $writer = $this->getOutputWriter($stream_idx);
        if (is_null($writer))
            return null;

        if ($data instanceof BinaryData)
            $writer->write($data->getData());
             else
            $writer->write($data);
    }


    public function func_read($stream_idx, $length, $associative)
    {
        $reader = $this->getInputReader($stream_idx);
        if (is_null($reader))
            return null;

        if (is_null($length))
        {
            $res = $reader->readRow();
            return $associative ? $res : array_values($res);
        }
         else
        {
            return new BinaryData($reader->read($length));
        }
    }




    // job definition info
    const MIME_TYPE = 'flexio.execute';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.execute",
        "params": {
            "lang": "python",
            "code": "<base64 encoded>"
        }
    }
EOD;
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.execute"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
