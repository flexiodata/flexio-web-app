<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
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
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Write implements \Flexio\IFace\IJob
{
    private $properties = array();

    public static function validate(array $task) : array
    {
        $errors = array();
        return $errors;
    }

    public static function run(\Flexio\IFace\IProcess $process, array $task) : void
    {
        unset($task['op']);
        \Flexio\Jobs\Util::replaceParameterTokens($process, $task);

        $object = new static();
        $object->properties = $task;
        $object->run_internal($process);
    }

    private function run_internal(\Flexio\IFace\IProcess $process) : void
    {
        $instream = $process->getStdin();
        $job_params = $this->getJobParameters();
        $path = $job_params['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing parameter 'path'");

        $stream_properties = $instream->get();

        // write to 'path'
        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);

        if (strlen(($job_params['connection'] ?? '')) > 0)
            $vfs->setRootConnection($job_params['connection']);

        // not all services support open/readers/writers; try that first
        try
        {
            $create_params = [];
            if (count($stream_properties['structure']) > 0)
            {
                $create_params['structure'] = $stream_properties['structure'];
            }
            $create_params['mime_type'] = $stream_properties['mime_type'];

            // call getServiceFromPath() -- this will return a service object, or throw
            $connection_identifier = '';
            $rpath = '';
            $service = $vfs->getServiceFromPath($path, $connection_identifier, $rpath);

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
            $files = $vfs->write($path, function($length) use (&$reader) {
                return $reader->read($length);
            });
        }
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}
