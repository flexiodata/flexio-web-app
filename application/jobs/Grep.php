<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-11-30
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "grep"
    // TODO: fill out
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['grep'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Grep extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
                $outstream->copyFrom($instream);
                return;

            case \Flexio\Base\ContentType::FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream) : void
    {
        // input/output
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        // get the code from the template
        $params = $this->getJobParameters();
        $grepexpr = $params['expression'] ?? '';
        if (strlen($grepexpr) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // build command line
        $cmd = \Flexio\System\System::getBinaryPath('grep') . ' ' . $grepexpr;
        //$cmd = "\"C:\Program Files\Git\usr\bin\cat.exe\"";

        // run the process

        /*
        $descriptorspec = array(
           0 => array("pipe", "r"),
           1 => array("pipe", "w"),
           2 => array("pipe", "w")
        );
        $cwd = sys_get_temp_dir();
        $env = array('some_option' => 'aeiou');
        $external_process = proc_open($cmd, $descriptorspec, $pipes, $cwd, NULL);

        if (!is_resource($external_process))
        {
            @unlink($filename);
            return false;
        }
        */

        $cwd = sys_get_temp_dir();
        $external_process = new \Flexio\Base\ProcessPipe;
        if (!$external_process->exec($cmd, $cwd))
        {
            @unlink($filename);
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        }

        $mime_type = $instream->getMimeType();
        if ($mime_type === \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            // write header row
            $row = $instream->getStructure()->getNames();
            $str = join(',', $row) . "\n";
            $external_process->write($str);
        }


        // $pipes now looks like this:
        // 0 => writeable handle connected to child stdin
        // 1 => readable handle connected to child stdout
        // Any error output will be appended to /tmp/error-output.txt


        $buf = '';
        $first_row = true;
        $strip_rownum_column = false;


        $rowcnt = 0;
        $maxrows = 1000;
        $done_writing = false;

        while (true)
        {
            $is_running = $external_process->isRunning();



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
                        $external_process->write($str);

                        ++$rowcnt;
                        if ($maxrows != -1 && ++$rowcnt >= $maxrows)
                        {
                            $external_process->closeWrite();
                            $done_writing = true;
                        }
                    }
                     else
                    {
                        $external_process->closeWrite();
                        $done_writing = true;
                    }
                }
                 else
                {
                    $buf = $streamreader->read(1024);
                    if ($buf === false)
                        break;

                    $len = strlen($buf);

                    if ($len > 0)
                        $external_process->write($buf);

                    if ($len != 1024)
                    {
                        $external_process->closeWrite();
                        $done_writing = true;
                    }
                }
            }


            if ($external_process->canRead())
            {
                $chunk = $external_process->read(1024);
                $streamwriter->write($chunk);
            }

            if (!$is_running)
                break;

        }

        $external_process->closeRead();
        $external_process->closeError();

        @unlink($filename);


        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }
}











class WindowsPipe
{
    public $wsh = null;
    public $exec = null;
    public $open_streams = [false,false,false];  // write, read, stderr

    function __construct()
    {
        $this->wsh = new \COM("WScript.Shell");
    }

    function __destruct()
    {
        $this->closeWrite();
        $this->closeRead();
        $this->closeError();
    }

    public function exec($cmd, $cwd) : void
    {
        $this->exec = $this->wsh->Exec($cmd);
        $this->open_streams = [true,true,true];
    }

    public function isRunning() : bool
    {
        if (is_null($this->exec))
            return false;

        return ($this->exec->Status == 0);
    }

    public function closeWrite() : void
    {
        if (!$this->open_streams[0])
        {
            $this->exec->Stdin->Close();
            $this->open_streams[0] = false;
        }
    }

    public function closeRead() : void
    {
        if (!$this->open_streams[1])
        {
            $this->exec->Stdout->Close();
            $this->open_streams[1] = false;
        }
    }

    public function closeError() : void
    {
        if (!$this->open_streams[2])
        {
            $this->exec->Stderr->Close();
            $this->open_streams[2] = false;
        }
    }

    public function write($buf) : void
    {
        $this->exec->Stdin->Write($buf);
    }

    public function read($size) // TODO: add return type
    {
        if (!$this->exec->Stderr->AtEndOfStream)
            $stderr = $this->exec->Stderr->ReadAll();
        return $this->exec->Stdout->Read($size);
    }

    public function canRead() : bool
    {
        return $this->exec->Stdout->AtEndOfStream ? false : true;
    }
}
