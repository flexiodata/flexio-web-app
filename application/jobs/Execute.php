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


class Execute extends \Flexio\Jobs\Base
{
    public function run()
    {
        $input = $this->getInput()->enum();
        foreach ($input as $instream)
        {
            $mime_type = $instream->getMimeType();
            $this->createOutput($instream);
/*
            switch ($mime_type)
            {
                // unhandled input
                default:
                    $this->getOutput()->push($instream->copy());
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
        $this->getOutput()->push($outstream);

        // if the input mime type is a table, set the output type to text
        $is_table = false;
        if ($instream->getMimeType() === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            $is_table = true;
            $outstream->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT);
        }

        // properties
        $job_definition = $this->getProperties();
        $input_structure = $instream->getStructure();

        // determine what program to load
        $program_type = false;
        $program_extension = false;
        switch ($job_definition['params']['lang'])
        {
            case 'r':
                $program_type = 'r';
                $program_extension = 'r';
                break;

            case 'python':
                $program_type = 'python';
                $program_extension = 'py';
                break;

            case 'javascript':
                $program_type = 'javascript';
                $program_extension = 'js';
                break;

            case 'go':
                $program_type = 'go';
                $program_extension = 'go';
                break;
        }

        if ($program_type === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $program_path = \Flexio\System\System::getBinaryPath($program_type);
        if (!isset($program_path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // get the code from the template
        $code = $job_definition['params']['code'] ?? '';
        if (strlen($code) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        $streamreader = \Flexio\Object\StreamReader::create($instream);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

        // the code is base64 encoded, so decode it and write it out
        // to a temporary file
        $code = base64_decode($code);
        $filename = \Flexio\Base\Util::createTempFile('fxscript', $program_extension);
        file_put_contents($filename, $code);

        $cmd = $program_path . ' ' . "\"$filename\"";
        $cwd = sys_get_temp_dir();


        $env = array('PYTHONPATH' => dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'python_include');


        $process = new \Flexio\Base\ProcessPipe;
        if (!$process->exec($cmd, $cwd, $env))
        {
            @unlink($filename);
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        }


        // first, write a json header record to the process, followed by \r\n\r\n
        $header = array(
            'name' => $instream->getName(),
            'size' => $instream->getSize(),
            'content_type' => $instream->getMimeType(),
            'structure' => ($is_table ? $instream->getStructure() : null)
        );
        $header_json = json_encode($header);
        $process->write($header_json . "\r\n\r\n");




        $done_writing = false;
        $first_chunk = true;
        $chunk = '';

        //$tot = 0;
        //$totw = 0;

        do
        {
            $is_running = $process->isRunning();

            // write a chunk to the stdin

            if (!$done_writing)
            {
                if ($is_table)
                {
                    // write data

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

                    $buf = $streamreader->read(1024);

                    //ob_start();
                    //var_dump($buf);
                    //$s = ob_get_clean();
                    //fxdebug("\n\n\n\nStream Reader: ".$s."***");

                    if ($buf === false)
                    {
                        $process->closeWrite();
                        $done_writing = true;
                        //fxdebug("Closed Writing; Total Written: $totw");
                    }
                     else
                    {
                        $len = strlen($buf);

                        if ($len > 0)
                            $process->write($buf);

                        //$totw += $len;
                    }
                }
            }



            if ($process->canRead())
            {
                //fxdebug("Reading...\n");
                $chunk .= $process->read(1024);

                if (strlen($chunk) > 0)
                {
                    if ($first_chunk)
                    {
                        $content_type = 'application/octet-stream';

                        $end = strpos($chunk, "\r\n\r\n");

                        if ($chunk[0] == '{' && $end !== false)
                        {
                            $header = @json_decode(substr($chunk, 0, $end), true);
                            if (!is_null($header))
                            {
                                if (isset($header['content_type']))
                                {
                                    $content_type = $header['content_type'];
                                }
                            }
                            $chunk = substr($chunk, $end+4);
                        }

                        $outstream->setMimeType($content_type);
                        $first_chunk = false;
                    }

                    // var_dump($chunk);

                    //ob_start();
                    //var_dump($chunk);
                    //$s = ob_get_clean();
                    //fxdebug("From process: ".$s."***\n\n\n\n");

                    //fxdebug("Writing " . strlen($chunk) . " bytes\n");
                    //$tot += strlen($chunk);

                    $streamwriter->write($chunk);
                    $chunk = '';
                }

            }


        } while ($is_running);


        // write any remaining data from process
        while (true)
        {
            $chunk = $process->read(1024);
            if (strlen($chunk) == 0)
                break;
            //fxdebug("Writing (after process ended)  " . strlen($chunk) . " bytes\n");
            //$tot += strlen($chunk);
            $streamwriter->write($chunk);
        }

        //fxdebug("Total bytes written: " . $tot);

        $err = $process->getError();
       // var_dump($err);
        if (isset($err))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
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
