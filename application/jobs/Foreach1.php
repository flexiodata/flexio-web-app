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
// DESCRIPTION:
{
    "op": "foreach"
    // TODO: fill out
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('type' => 'string',     'required' => true)
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
*/

class Foreach1 extends \Flexio\Jobs\Base
{
    private $env;
    private $task;
    private $varname;

    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        // default stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();


        $this->env = $process->getParams();

        $job_params = $this->getJobParameters();
        $this->task = $job_params['run'] ?? [];
        $this->varname = 'item';

        if (isset($job_params['spec']))
        {
            $forspec = $job_params['spec'];

            $parts = explode(':', $forspec);
            foreach ($parts as &$part)
            {
                $part = trim($part);
            }

            if (count($parts) != 2)
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid forspec. The forspec must have two parts, separate by a colon");
            }

            $this->varname = $parts[0];

            $instream = $this->getParameterStream($process, $parts[1]);
        }


        if (!$instream)
        {
            // if the input stream could not be found, what's the correct behavior? exception, or do nothing?
            return;
        }


        $mime_type = $instream->getMimeType();
        $streamreader = $instream->getReader();

        if ($mime_type == \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            while (($row = $streamreader->readRow()) !== false)
            {
                $this->doIteration($process->getOwner(), $row);
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
                    $this->doIteration($process->getOwner(), $item);
                }
            }
            else
            {
                $this->doIteration($process->getOwner(), $json);
            }
        }
        else
        {
            // nothing to iterate over -- use the input stream
            $this->doIteration($process->getOwner(), $instream);
        }


        $process->setParams($this->env);
    }

    private function doIteration(string $process_user_eid, $input) : void
    {
        $subprocess = \Flexio\Jobs\Process::create();
        $subprocess->setOwner($process_user_eid);
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
                $json = json_encode($input);

                $p = $subprocess->getParams();
                $param_stream = \Flexio\Base\Stream::create();
                $param_stream->setMimeType(\Flexio\Base\ContentType::JSON);
                $param_stream->buffer = $json;
                $p[$this->varname] = $param_stream;
                $subprocess->setParams($p);

                $stream->setMimeType(\Flexio\Base\ContentType::JSON);
                $writer->write($json);
            }
            else
            {
                $p = $subprocess->getParams();
                $p[$this->varname] = (string)$input;
                $subprocess->setParams($p);

                $stream->setMimeType(\Flexio\Base\ContentType::TEXT);
                $writer->write((string)$input);
            }

            $subprocess->setStdin($stream);
        }

        $subprocess->execute($this->task);
        $this->env = $subprocess->getParams();
    }
}
