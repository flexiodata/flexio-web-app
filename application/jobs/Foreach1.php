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

        $varname = 'item';

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();


        $mime_type = $instream->getMimeType();
        $streamreader = $instream->getReader();

        if ($mime_type == \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            while (($row = $streamreader->readRow()) !== false)
            {
                $this->doIteration($row, $varname);
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
                    $this->doIteration($item, $varname);
                }
            }
            else
            {
                $this->doIteration($json, $varname);
            }
        }
        else
        {
            // nothing to iterate over -- use the input stream
            $this->doIteration($instream, $varname);
        }


        $process->setParams($this->env);
    }


    private function doIteration($input, $varname)
    {
        $subprocess = \Flexio\Jobs\Process::create();
        $subprocess->setParams($this->env);

        if ($input instanceof \Flexio\Iface\IStream)
        {
            // input is a stream
            $subprocess->setStdin($input);
        }
        else
        {
            $stream = \Flexio\Base\Stream::create();
            $writer = $stream->getWriter();

            if (is_array($input) || is_object($input))
            {
                $p = $subprocess->getParams();
                foreach ((array)$input as $k => $v)
                {
                    $p[$varname . '.' . $k] = $v;
                }
                $subprocess->setParams($p);

                $stream->setMimeType(\Flexio\Base\ContentType::JSON);
                $writer->write(json_encode($input));
            }
            else
            {
                $stream->setMimeType(\Flexio\Base\ContentType::TEXT);
                $writer->write((string)$input);
            }

            $subprocess->setStdin($stream);
        }

        $subprocess->execute($this->task);
        $this->env = $subprocess->getParams();
    }
}
