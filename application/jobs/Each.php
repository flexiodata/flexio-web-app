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
    public function run(\Flexio\Jobs\IProcess $process)
    {
        parent::run($process);

        // process buffer
        $instream = $process->getBuffer();
        $outstream = \Flexio\Base\StreamMemory::create();
        $this->processStream($instream, $outstream);
        $process->setBuffer($outstream);

        // process stream array
        $input = $process->getStreams();
        $process->clearStreams();

        foreach ($input as $instream)
        {
            $outstream = \Flexio\Base\StreamMemory::create();
            $this->processStream($instream, $outstream);
            $process->addStream($outstream);
        }
    }

    private function processStream(\Flexio\Base\IStream &$instream, \Flexio\Base\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        switch ($mime_type)
        {
            default:
                $outstream = $instream;
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
            $rowinstream = \Flexio\Base\StreamMemory::create();
            $rowinstream->write($row);

            $process = \Flexio\Jobs\Process::create();
            $process->addTasks($job_tasks);
            $process->setBuffer($rowinstream);
            $process->execute();
            $rowoutstream = $process->getBuffer();

            while (true)
            {
                $outrow = $rowoutstream->read();
                if ($outrow === false)
                    break;

                $streamwriter->write($outrow);
            }
        }

        $streamwriter->close();
        $outstream->setSize($streamwriter->getBytesWritten());
    }
}
