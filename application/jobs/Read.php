<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-05
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "read",
    "params": {
        "path": ""
    }
}
*/

class Read extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $outstream = $process->getStdout();
        $job_params = $this->getJobParameters();
        $path = $job_params['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'path'");

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);

        $info = $vfs->getFileInfo($path);

        $properties = [ 'mime_type' => ($info['content_type'] ?? 'application/octet-stream') ];
        if (isset($info['structure']))
        {
            $properties['mime_type'] = \Flexio\Base\ContentType::FLEXIO_TABLE;
            $properties['structure'] = $info['structure'];
        }

        $outstream->set($properties);
        $streamwriter = $outstream->getWriter();

        $files = $vfs->read($path, function($data) use (&$streamwriter) {
            $streamwriter->write($data);
        });


        /*
        // process buffer
        $outstream = $process->getStdout();
        $job_definition = $this->getProperties();
        $path = $job_definition['params']['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'path'");

        $outstream->setMimeType('text/plain');

        $streamwriter = $outstream->getWriter();

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);

        // not all services support open(). Try that first, and if it fails,
        // fall back to the read() method

        try
        {
            $stream = $vfs->open($path);

            if (!$stream)
            {
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);
            }

            $stream_properties = $stream->get();
            $outstream->set(['structure' => $stream_properties['structure'],
                             'mime_type' => $stream_properties['mime_type']]);

            $writer = $outstream->getWriter();
            $reader = $stream->getReader();

            if (count($stream_properties['structure']) > 0)
            {
                while (($row = $reader->readRow()) !== false)
                    $writer->write($row);
            }
             else
            {
                while (($data = $reader->read(16384)) !== false)
                {
                    $writer->write($data);
                }
            }

        }
        catch (\Exception $e)
        {
            $files = $vfs->read($path, function($data) use (&$streamwriter) {
                $streamwriter->write($data);
            });
        }
        */
    }
}
