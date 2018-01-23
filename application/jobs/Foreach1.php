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
    "op": "foreach",
    "params": [
    ]
}
*/

class Foreach1 extends \Flexio\Jobs\Base
{
    private $env = null;
    private $task = null;

    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $this->env = $process->getParams();

        $job_definition = $this->getProperties();
        $this->task = $job_definition['params']['run'] ?? [];


        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream);

        $process->setParams($this->env);
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream)
    {
        $mime_type = $instream->getMimeType();
        $streamreader = $instream->getReader();

        if ($mime_type == \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            while (($row = $streamreader->readRow()) !== false)
            {
                $this->doIteration($row);
            }
        }
        else if ($mime_type == \Flexio\Base\ContentType::JSON)
        {
            $buf = '';
            while (($chunk = $streamreader->read(32768)) !== false)
                $buf .= $chunk;
            $json = json_decode($buf);
            if (is_array($json))
            {
                foreach ($json as $item)
                {
                    $this->doIteration($item);
                }
            }
            else
            {
                $this->doIteration($json);
            }
        }
        else
        {

        }

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

    private function doIteration($row)
    {
        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();

        if (is_array($row) || is_object($row))
        {
            $stream->setMimeType(\Flexio\Base\ContentType::JSON);
            $writer->write(json_encode($row));
        }
        else
        {
            $stream->setMimeType(\Flexio\Base\ContentType::TEXT);
            $writer->write((string)$row);
        }

        $subprocess = \Flexio\Jobs\Process::create();
        $subprocess->setParams($this->env);
        $subprocess->setStdin($stream);
        $subprocess->execute($this->task);
        $this->env = $subprocess->getParams();
    }
}
