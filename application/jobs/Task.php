<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-28
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.task",
    "params": {
        // another task here
    }
}
*/

class Task extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        // STEP 1: create a subprocess and add the task to run
        $job_definition = $this->getProperties();
        $job_tasks = array($job_definition['params']);

        $subprocess = \Flexio\Jobs\Process::create();
        $subprocess->setTasks($job_tasks);

        // STEP 2: pass on the stdin from the main process to the subprocess
        $subprocess_stdin = $subprocess->getStdin();
        self::copyStream($instream, $subprocess_stdin);

        // STEP 3: execute the subprocess
        $subprocess->execute();

        // STEP 4: copy the output from the subprocess to the stdout of the main process
        $subprocess_stdout = $subprocess->getStdout();
        self::copyStream($subprocess_stdout, $outstream);
    }

    private static function copyStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        while (true)
        {
            $content = $streamreader->read();
            if ($content === false)
                break;

            $streamwriter->write($content);
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
        $outstream->setMimeType($instream->getMimeType());
    }
}
