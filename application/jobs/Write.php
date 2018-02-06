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

        $stream_properties = $instream->get();
            
        // the write job's "stdout" is always just a copy of its "stdin"; do this first
        $outstream->copyFrom($instream);
        $outstream = null;        

        // now perform the write to 'path'
        $vfs = new \Flexio\Services\Vfs();
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
            if (($service->getFlags() & \Flexio\IFace\IFileSystem::FLAG_RANDOM_ACCESS) == 0)
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
            if (count($stream_properties['structure']) > 0)
            {
                $fp = fopen('php://memory', 'w');
                $field_names = array_column($stream_properties['structure'], 'name');
                fputcsv($fp, $field_names);

                $reader = $instream->getReader();
                $files = $vfs->write($path, function($length) use (&$reader, &$fp) {

                    if ($fp === false)
                        return false;

                    while (ftell($fp) < $length)
                    {
                        $row = $reader->readRow();
                        if ($row === false)
                        {
                            fseek($fp, 0);
                            $contents = stream_get_contents($fp);
                            fclose($fp);
                            $fp = false;
                            return $contents;
                        }

                        $row = array_values($row);
                        fputcsv($fp, $row);
                    }

                    fseek($fp, 0);
                    $contents = stream_get_contents($fp);
                    fclose($fp);

                    $fp = fopen('php://memory', 'w');
                    fwrite($fp, substr($contents, $length));
                    return substr($contents, 0, $length);
                });

                if ($fp)
                {
                    fclose($fp);
                }
            }
             else
            {
                $reader = $instream->getReader();
                $files = $vfs->write($path, function($length) use (&$reader) {
                    return $reader->read($length);
                });
            }
        }
    }
}
