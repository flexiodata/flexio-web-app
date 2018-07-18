<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-13
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "copy",  // string, required
    "from": "",    // string, required
    "to": ""       // string, required
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true, 'enum' => ['copy']),
        'path'       => array('required' => true, 'type' => 'string'),
        'to'         => array('required' => true, 'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Copy extends \Flexio\Jobs\Base
{
    private $recursive = false;   // similar to cp -r flag

    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $params = $this->getJobParameters();

        $from = $params['from'] ?? null;
        $to = $params['to'] ?? null;
        $this->recursive = $params['options']['recursive'] ?? false; // recursive (similar to cp -r) is off by default

        if (is_null($from))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing parameter 'from'");
        if (is_null($to))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Missing parameter 'to'");
        if (is_string($from) && strlen($from) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid/empty value specified in parameter 'from'");
        if (is_array($from) && count($from) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid/empty value specified in parameter 'from'");
        if (strlen($to) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid/empty value specified in parameter 'to'");

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);

        if (is_string($from))
        {
            $this->copyFiles($process, $vfs, $from, $to);
        }
         else if (is_array($from))
        {
            foreach ($from as $f)
            {
                $this->copyFiles($process, $vfs, $f, $to);
            }
        }
         else
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid/empty value specified in parameter 'from'");
        }
    }

    private function copyFiles(\Flexio\IFace\IProcess $process, \Flexio\Services\Vfs $vfs, string $from, string $to) : void
    {

        $arr = \Flexio\Base\File::splitBasePathAndName($from);
        $base = $arr['base'];
        $name = $arr['name'];


        if (strpos($base, '*') !== false)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid parameter 'from'. Only the last part of the path may contain a wildcard");
        }

        if (strpos($base, '://') === false && substr($base, 0, 1) != '/')
        {
            $base = '/' . $base;
        }

        if (strpos($name, '*') !== false)
        {
            $from_files = [];
            $wildcard = $name;

            $files = $vfs->list($base);
            foreach ($files as $f)
            {
                if (\Flexio\Base\File::matchPath($f['name'], $wildcard, false))
                {
                    $from_files[] = $f;
                }
            }
        }
        else
        {
            $info = $vfs->getFileInfo($from);
            $from_files = [ $info ];
        }


        $destination_is_directory = false;

        try
        {
            $to_info = $vfs->getFileInfo($to);
            $destination_is_directory = ($to_info['type'] == 'DIR' ? true:false);
        }
        catch (\Exception $e)
        {
        }

        foreach ($from_files as $file)
        {
            $full_from_path = \Flexio\Base\File::appendPath($base, $file['name']);
            $full_to_path = $destination_is_directory ? \Flexio\Base\File::appendPath($to, $file['name']) : $to;

            if ($file['type'] == 'FILE')
                $this->copyFile($process, $vfs, $full_from_path, $full_to_path);
                 else
                $this->copyDirectory($process, $vfs, $full_from_path, $full_to_path);
        }
    }

    private function copyDirectory(\Flexio\IFace\IProcess $process, \Flexio\Services\Vfs $vfs, string $from, string $to) : void
    {
        if (!$this->recursive) // if recursive mode is off (which is the default), then directory copying is disabled
            return;

        $vfs->createDirectory($to);
        $this->copyFiles($process, $vfs, $from . '/*', $to);
    }

    private function copyFile(\Flexio\IFace\IProcess $process, \Flexio\Services\Vfs $vfs, string $from, string $to) : void
    {
        $subprocess = \Flexio\Jobs\Process::create();

        // proxy the local connections
        $local_connections = $process->getLocalConnections();
        foreach ($local_connections as $key => $value)
        {
            $subprocess->addLocalConnection($key, $value);
        }

        $process_owner_eid = $process->getOwner();
        $subprocess->setOwner($process_owner_eid);
        $subprocess->execute([ 'op' => 'read', 'path' => $from ]);
        $subprocess->execute([ 'op' => 'write', 'path' => $to ]);  // executes can be chained; stdout of previous execute becomes stdin of next
    }
}
