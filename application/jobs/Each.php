<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-08
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.each",
    "params": [
    ]
}
*/

class Each extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);
    }

    private function processStream(\Flexio\Base\IStream &$instream, \Flexio\Base\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
                $outstream->copy($instream);
                return;

            case \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE:
                $this->getOutput($instream, $outstream);
                return;
        }
    }

    private function getOutput(\Flexio\Base\IStream &$instream, \Flexio\Base\IStream &$outstream)
    {
        $job_definition = $this->getProperties();
        $job_tasks = $job_definition['params'];

        // execute each of the job tasks for the input row
        $streamreader = $instream->getReader();
        $streamwriter = $outstream->getWriter();

        while (true)
        {
            $row = $streamreader->readRow();
            if ($row === false)
                break;

            // create a new process; pass on the params, but set the stdin
            // to the content for the row; TODO: need to pass on the params from
            // the process
            $subprocess = \Flexio\Jobs\Process::create();
            $subprocess->addTasks($job_tasks);
            $subprocess->getStdin()->write($row);
            $subprocess->execute();
            $subprocess_stdout = $subprcess->getStdout();

            while (true)
            {
                $outrow = $subprocess_stdout->read();
                if ($outrow === false)
                    break;

                $streamwriter->write($outrow);
            }
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }
}
