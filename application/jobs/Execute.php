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

    private function createOutput($instream)
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
            return $this->fail(\Flexio\Base\Error::INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        $program_path = \Flexio\System\System::getBinaryPath($program_type);
        if (!isset($program_path))
            return $this->fail(\Flexio\Base\Error::INVALID_PARAMETER, _(''), __FILE__, __LINE__);

        // get the code from the template
        $code = $job_definition['params']['code'] ?? '';
        if (strlen($code) == 0)
            return $this->fail(\Flexio\Base\Error::MISSING_PARAMETER, _(''), __FILE__, __LINE__);



        $streamreader = \Flexio\Object\StreamReader::create($instream);
        $streamwriter = \Flexio\Object\StreamWriter::create($outstream);

        // the code is base64 encoded, so decode it and write it out
        // to a temporary file
        $code = base64_decode($code);
        $filename = \Flexio\Base\Util::createTempFile('fxscript', $program_extension);
        file_put_contents($filename, $code);

        $cmd = $program_path . ' ' . "\"$filename\"";
        $cwd = sys_get_temp_dir();

        $process = new \Flexio\Base\ProcessPipe;
        if (!$process->exec($cmd, $cwd))
        {
            @unlink($filename);
            return $this->fail(\Flexio\Base\Error::INVALID_SYNTAX, _(''), __FILE__, __LINE__);
        }

        $done_writing = false;

        do
        {
            $is_running = $process->isRunning();

            // write a chunk to the stdin

            if (!$done_writing)
            {
                if ($is_table)
                {
                    // write data

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
                        break;

                    $len = strlen($buf);

                    if ($len > 0)
                        $process->write($buf);

                    //fxdebug("Write Done\n");

                    if ($len != 1024)
                    {
                        $process->closeWrite();
                        $done_writing = true;
                        fxdebug("Closed Writing\n");
                    }
                }
            }



            if ($process->canRead())
            {
                //fxdebug("Reading...\n");
                $chunk = $process->read(1024);

                //ob_start();
                //var_dump($chunk);
                //$s = ob_get_clean();
                //fxdebug("From process: ".$s."***\n\n\n\n");

                $streamwriter->write($chunk);
            }


        } while ($is_running);

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
