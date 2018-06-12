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
// DESCRIPTION:
{
    "op": "write",  // string, required
    "path": ""      // string, required
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['write']),
        'path'       => array('required' => true,  'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
*/

class Write extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $job_params = $this->getJobParameters();
        $path = $job_params['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'path'");

        $stream_properties = $instream->get();

        // the write job's "stdout" is always just a copy of its "stdin"; do this first
        $outstream->copyFrom($instream);
        $outstream = null;

        // now perform the write to 'path'
        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);

        // not all services support open/readers/writers; try that first
        try
        {
            $create_params = [];
            if (count($stream_properties['structure']) > 0)
            {
                $create_params['structure'] = $stream_properties['structure'];
            }
            $create_params['mime_type'] = $stream_properties['mime_type'];


            $service = $vfs->getServiceFromPath($path); // this will return or service, or throw
            // check if the service only supports write(), and if so, go to the catch
            if (($service->getFlags() & \Flexio\IFace\IFileSystem::FLAG_HAS_OPEN) == 0)
                throw new \Exception(""); // go to the catch and use $vfs->write()


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

            $is_table = false;
            if (isset($stream_properties['structure']) && is_array($stream_properties['structure']) && count($stream_properties['structure']) > 0)
                $is_table = true;

            if ($is_table)
            {
                $params = [
                    'path' => $path,
                    'structure' => $stream_properties['structure']
                ];

                $files = $vfs->write($params, function() use (&$reader) {
                    return $reader->readRow();
                });
            }
             else
            {
                $files = $vfs->write($path, function($length) use (&$reader) {
                    return $reader->read($length);
                });
            }
        }
    }
}
