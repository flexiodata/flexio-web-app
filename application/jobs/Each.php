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
    public function run(\Flexio\Object\Context &$context)
    {
        parent::run($context);

        // process stdin
        $stdin = $context->getStdin();
        $stdout = $context->getStdout();
        $this->processStream($stdin, $stdout);

        // process stream array
        $input = $context->getStreams();
        $context->clearStreams();

        foreach ($input as $instream)
        {
            $outstream = \Flexio\Base\StreamMemory::create();
            $this->processStream($instream, $outstream);
            $context->addStream($outstream);
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

            // create a new context; pass on the params, but set the stdin
            // to the content for the row
            $rowinstream = \Flexio\Base\StreamMemory::create();
            $rowinstream->write($row);

            $context = \Flexio\Object\Context::create();
            $context->setParams($instream->getParams());
            $context->setStdin($rowinstream);

            $this->execute($job_tasks, $context);

            // write the result to the output stream
            $rowoutstream = $context->getStdout();
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

    private function execute(array $jobs_tasks, \Flexio\Object\Context &$context)
    {
        $first_task = true;
        foreach ($job_tasks as $task)
        {
            if ($first_task === false)
            {
                // set the stdin for the next job step to be the output from the stdout
                // of the step just executed and create a new stdout
                $context->setStdin($context->getStdout());
                $stdout = \Flexio\Base\StreamMemory::create();
                $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT); // default mime type
                $context->setStdout($stdout);
            }

            // execute the task
            $this->executeTask($task, $context);
            $first_task = false;
        }
    }

    private function executeTask(array $task, \Flexio\Object\Context &$context)
    {
        $job = \Flexio\Jobs\Factory::create($task);
        $job->run($context);
    }
}
