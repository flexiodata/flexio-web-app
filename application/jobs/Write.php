<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-06
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "write",
    "params": {
        "path": ""
    }
}
*/

class Write extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $job_definition = $this->getProperties();
        $path = $job_definition['params']['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'path'");

        // the write job's output is always just a copy of its input; do this first
        $outstream->copyFrom($instream);
        $outstream = null;        

        $vfs = new \Flexio\Services\Vfs();
        $vfs->setProcess($process);

        // not all services support open/readers/writers; try that first

        try
        {
            $service = $vfs->getServiceFromPath($path); // this will return or service, or throw

            // check if the service only supports write(), and if so, go to the catch
            if (($service->getFlags() & \Flexio\IFace\IFileSystem::FLAG_RANDOM_ACCESS) == 0)
                throw new \Exception(""); // go to the catch and use $vfs->write()

            $stream_properties = $instream->get();
            
            $create_params = [];
            if (count($stream_properties['structure']) > 0)
                $create_params['structure'] = $stream_properties['structure'];
            $create_params['mime_type'] = $stream_properties['mime_type'];


            if (!$vfs->createFile($path, $create_params))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
    
            $outstream = $vfs->open($path);
            if (!$outstream)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        
            $reader = $instream->getReader();
            $writer = $outstream->getWriter();

            if (count($stream_properties['structure']) > 0)
            {
                while (($row = $reader->readRow()) !== false)
                {
                    $writer->write($row);
                }
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
            $reader = $instream->getReader();
            $files = $vfs->write($path, function($length) use (&$reader) {
                return $reader->read($length);
            });
        }
    }
}
