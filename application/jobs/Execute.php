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
}

class ExecuteProxy
{
    private $context = null;
    private $socket = null;
    private $port = null;

    public function initialize()
    {
        $this->context = new \ZMQContext(1);

        //  Socket to talk to clients
        $this->socket = new \ZMQSocket($this->context, ZMQ::SOCKET_REP);

        // it's unfortunate that we can't use port :*, but with the
        // current php bindings, we are unable to figure out which
        // port the kernel chose
        $port = 10000 + rand(0, 10000);
$port = 10000;
        while (true)
        {
            try
            {
                $abc = $this->socket->bind("tcp://*:$port");
                break;
            }
            catch (Exception $e)
            {
                //echo("Could not bind to port $port\n");
                $port++;
                if ($port == 20000)
                    return false;
            }
        }

        $this->port = $port;
    }


    public function getListenPort()
    {
        return $this->port;
    }

    public function run()
    {
        while (true)
        {
            //  Wait for next request from client
            $request = $this->socket->recv();
            printf ("Received request: [%s]\n", $request);

            $arr = $this->parseCallString($request);
var_dump($arr);

            if ($arr === false || count($arr) == 0)
            {
                // bad parse -- send exception
                $this->socket->send("E19,Request parse error");
                continue;
            }  

            $func = array_shift($arr);

            // check if function exists
            if (!method_exists($this, 'func_' . $func))
            {
                // bad parse -- send exception
                $this->socket->send("E14,Unknown method");
                continue;
            }

            // make function call
            $retval = call_user_func_array([ $this, 'func_'.$func ], $arr);

            //  Send reply back to client
            $this->socket->send(self::encodepart($retval));
        }

    }


    public static function encodepart($val)
    {
       if ($val instanceof BinaryData)
       {
           $type = 'B';
           $val = $val->data;
       }
        else if (is_int($val))
       {
           $type = 'i';
           $val = (string)$val;
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

    private function func_read()
    {
        // returns php string, but should return binary type
        return new BinaryData("This is binary data");
    }
}



class Execute extends \Flexio\Jobs\Base
{
    public function run()
    {
        $this->getOutput()->setEnv($this->getInput()->getEnv()); // by default, pass on all params; however, execute script can change them
        $input = $this->getInput()->getStreams();

        foreach ($input as $instream)
        {
            $mime_type = $instream->getMimeType();
            $this->createOutput($instream);
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

    private function createOutput(\Flexio\Object\Stream $instream)
    {
        // input/output
        $outstream = $instream->copy()->setPath(\Flexio\Base\Util::generateHandle());
        $this->getOutput()->addStream($outstream);

        $is_input_table = false;
        $is_output_table = false;
        if ($instream->getMimeType() === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
            $is_input_table = true;

        // by default, set output content type to text
        $outstream->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT);

        // properties
        $job_definition = $this->getProperties();

        // get the code from the template
        // $code contains the base64-encoded program source
        $code = $job_definition['params']['code'] ?? '';
        if (strlen($code) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);


        // determine what program to load
        $program_type = false;
        $program_extension = false;
        switch ($job_definition['params']['lang'])
        {
            case 'python':
                $program_type = 'python';
                $program_extension = 'py';
                $dockerbin = \Flexio\System\System::getBinaryPath('docker');
                if (is_null($dockerbin))
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);


                $code = base64_decode($code);

                if (strpos($code, "flexio_handler") !== false)
                {
                    // add code that invokes the main handler -- this is the preferred
                    // way of coding python scripts
                    if (false !== strpos($code, "\r\n"))
                        $endl = "\r\n";
                        else
                        $endl = "\n";

                    $code .= $endl . "import flexio as flexioext";
                    $code .= $endl . "flexioext.run(flexio_handler)";
                    $code .= $endl;
                }

                $code = base64_encode($code);

                $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxpython sh -c '(echo $code | base64 -d > /tmp/script.py && timeout 30s python3 /tmp/script.py)'";
                //$cmd = "$dockerbin run -a stdin -a stdout -a stderr --net none --rm -i fxpython sh -c 'runscript $code'";

                break;

            case 'html':
                $program_type = 'html';
                $program_extension = 'html';

                $code = base64_decode($code);

                $streamreader = \Flexio\Object\StreamReader::create($instream);
                if (strpos($code, 'flexio.input.json_assoc()') !== false)
                {
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

                break;
/*
            case 'javascript':
                $program_type = 'javascript';
                $program_extension = 'js';
                break;

            case 'go':
                $program_type = 'go';
                $program_extension = 'go';
                break;
            case 'r':
                $program_type = 'r';
                $program_extension = 'r';
                break;
*/
            default:
                // unknown language
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }






        $streamreader = \Flexio\Object\StreamReader::create($instream);
        $streamwriter = null; // created below

        $cwd = sys_get_temp_dir();

        $process = new \Flexio\Base\ProcessPipe;
        if (!$process->exec($cmd, $cwd))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        }

        // first, write a json header record to the process, followed by \r\n\r\n
        $structure = ($is_input_table ? $instream->getStructure() : null);
        if (isset($structure))
        {
            $structure = $structure->get();

            // don't need/want to report store name to script
            foreach ($structure as &$fld)
                unset($fld['store_name']);
            unset($fld);
        }

        // merge in this order so that user-supplied variables don't override environment variables
        $environment_variables = $this->getInput()->getEnv();
        $header = array(
            'name' => $instream->getName(),
            'size' => $instream->getSize(),
            'content_type' => $instream->getMimeType(),
            'structure' => $structure,
            'env' => $environment_variables
        );

        $header_json = json_encode($header);
        $process->write($header_json . "\r\n\r\n");

        $done_writing = false; // "done writing input to process"
        $done_reading = false; // "done reading result from process"
        $first_chunk = true;
        $readbuf = '';         // read from process buffer
        $writebuf = '';        // write to process buffer

        //$tot = 0;
        //$totw = 0;

        do
        {
            $is_running = $process->isRunning();

            // read chunk of data from input stream and write it to the process

            if (!$is_running && !$done_writing)
            {
                // can't write to a process that's not running
                $process->closeWrite();
                $done_writing = true;
            }

            if ($is_running && !$done_writing)
            {
                if ($is_input_table)
                {
                    $rowcnt = 0;
                    $row = $streamreader->readRow();
                    if ($row)
                    {
                        $str = join(',', array_values($row)) . "\n";
                        $process->write($str);

                        ++$rowcnt;
                        /*
                        if ($maxrows != -1 && ++$rowcnt >= $maxrows)
                        {
                            $process->closeWrite();
                            $done_writing = true;
                        }
                        */
                    }
                     else
                    {
                        $process->closeWrite();
                        $done_writing = true;
                    }
                }
                 else
                {
                    // fill our to-write buffer with data from input stream,
                    // if it isn't already full of data waiting to be written
                    if (strlen($writebuf) < 65536)
                    {
                        $chunk = $streamreader->read(1024);
                        if ($chunk !== false)
                        {
                            $writebuf .= $chunk;
                        }
                    }

                    //ob_start();
                    //var_dump($buf);
                    //$s = ob_get_clean();
                    //fxdebug("\n\n\n\nStream Reader: ".$s."***");

                    $writebuflen = strlen($writebuf);
                    if ($writebuflen == 0)
                    {
                        $process->closeWrite();
                        $done_writing = true;
                        //fxdebug("Closed Writing; Total Written: $totw");
                    }
                     else
                    {

                       // fxdebug("\n\n\n\nWriting to process: ".$writebuf."***");
                        $written = $process->write($writebuf);
                        if ($written == $writebuflen)
                        {
                            $writebuf = '';
                        }
                        else
                        {
                            $writebuf = substr($writebuf, $written);
                        }

                       // fxdebug("\nBlock finished.\n\n");

                        //$totw += $len;
                    }
                }
            }



            if (!$done_reading)
            {
                //fxdebug("Reading... IsRunning=".($is_running?"Yes":"No")." DoneReading=".($done_reading?"Yes":"No")." DoneWriting=".($done_writing?"Yes":"No")."\n");
                $chunk = $process->read(1024);
                //fxdebug("Read from process: len=".($chunk===false?"false":"".strlen($chunk))." Data=$chunk\n\n\n");

                if ($chunk !== false)
                    $readbuf .= $chunk;

                if (strlen($readbuf) == 0)
                {
                    if (!$is_running)
                    {
                        $done_reading = true;
                    }
                }
                 else
                {
                    if ($first_chunk)
                    {
                        $end = strpos($readbuf, "\r\n\r\n");

                        if ($end === false)
                        {
                            // did not find response header end -- we need more data; however
                            // if the response header size becomes unrealistically large, fail out
                            if (strlen($readbuf) > 100000)
                            {
                                // could not response header -- job failed
                                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_GENERAL, "Script did not provide valid response header");
                            }
                        }
                         else
                        {
                            $header = @json_decode(substr($readbuf, 0, $end), true);
                            if (is_null($header))
                            {
                                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_GENERAL, "Script did not provide valid response header (invalid JSON)");
                            }


                            $content_type = $header['content_type'] ?? 'application/octet-stream';
                            $structure = $header['structure'] ?? null;
                            $env = $header['env'] ?? null;

                            if (isset($env))
                                $this->getOutput()->setEnv($env);

                            $readbuf = substr($readbuf, $end+4);

                            if ($content_type == \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
                            {
                                if (!isset($structure))
                                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

                                $outstream->setMimeType($content_type);
                                $outstream->setStructure($structure);
                                $is_output_table = true;
                            }
                            else
                            {
                                $outstream->setMimeType($content_type);
                                $is_output_table = false;
                            }

                            $first_chunk = false;
                            $streamwriter = \Flexio\Object\StreamWriter::create($outstream);
                        }
                    }

                    // var_dump($readbuf);

                    //ob_start();
                    //var_dump($readbuf);
                    //$s = ob_get_clean();
                    //fxdebug("From process: ".$s."***\n\n\n\n");

                    //fxdebug("Writing " . strlen($readbuf) . " bytes\n");
                    //$tot += strlen($readbuf);
                    if ($streamwriter)
                    {
                        if ($is_output_table)
                        {
                            $offset = 0;
                            while (true)
                            {
                                $eolpos = \Flexio\Jobs\Convert::indexOfLineTerminator($readbuf, '"', $offset);
                                if ($eolpos === false)
                                {
                                    $readbuf = substr($readbuf, $offset);
                                    break;
                                }

                                $line = substr($readbuf, $offset, $eolpos - $offset);

                                $offset = $eolpos+1;
                                if ($readbuf[$offset-1] == "\r" && ($readbuf[$offset] ?? '') == "\n")
                                    $offset++;

                                $row = str_getcsv($line);

                                if ($row !== false)
                                {
                                    $row = \Flexio\Jobs\Convert::conformValuesToStructure($structure, $row);
                                    $streamwriter->write($row);
                                }

                            }
                        }
                        else
                        {
                            $streamwriter->write($readbuf);
                            $readbuf = '';
                        }
                    }
                }

            }

            // fxdebug("Done writing to process? " . ($done_writing?"true":"false") . " Done reading from process? " . ($done_reading?"true":"false")."\n");

        } while (!$done_writing || !$done_reading);

        //fxdebug("Loop done");
/*
        // write any remaining data from process
        while (true)
        {
            $readbuf = $process->read(1024);
            if (strlen($readbuf) == 0)
                break;
            //fxdebug("Writing (after process ended)  " . strlen($readbuf) . " bytes\n");
            //$tot += strlen($readbuf);
            $streamwriter->write($readbuf);
        }
*/

        //fxdebug("Total bytes written: " . $tot);

        $err = $process->getError();



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
