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
// EXAMPLE:
{
    "op": "copy",
    "params": {
        "from": "",
        "to": ""
    }
}
*/

class Copy extends \Flexio\Jobs\Base
{
    private $recursive = false;   // similar to cp -r flag


    public function run(\Flexio\IFace\IProcess $process)
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'from'");
        if (is_null($to))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'to'");
        if (is_string($from) && strlen($from) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid/empty value specified in parameter 'from'");
        if (is_array($from) && count($from) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid/empty value specified in parameter 'from'");
        if (strlen($to) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid/empty value specified in parameter 'to'");

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);

        if (is_string($from))
        {
            $this->copyFiles($process->getOwner(), $vfs, $from, $to);
        }
         else if (is_array($from))
        {
            foreach ($from as $f)
            {
                $this->copyFiles($process->getOwner(), $vfs, $f, $to);
            }
        }
         else
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid/empty value specified in parameter 'from'");
        }
    }

    private function copyFiles(string $process_owner_eid, \Flexio\Services\Vfs $vfs, string $from, string $to)
    {

        $arr = \Flexio\Base\File::splitBasePathAndName($from);
        $base = $arr['base'];
        $name = $arr['name'];


        if (strpos($base, '*') !== false)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid parameter 'from'. Only the last part of the path may contain a wildcard");
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
                $this->copyFile($process_owner_eid, $vfs, $full_from_path, $full_to_path);
                 else
                $this->copyDirectory($process_owner_eid, $vfs, $full_from_path, $full_to_path);
        }
    }


    private function copyDirectory(string $process_owner_eid, \Flexio\Services\Vfs $vfs, string $from, string $to)
    {
        if (!$this->recursive) // if recursive mode is off (which is the default), then directory copying is disabled
            return;

        $vfs->createDirectory($to);
        $this->copyFiles($process_owner_eid, $vfs, $from . '/*', $to);
    }

    private function copyFile(string $process_owner_eid, \Flexio\Services\Vfs $vfs, string $from, string $to)
    {
        $subprocess = \Flexio\Jobs\Process::create();
        $subprocess->setOwner($process_owner_eid);
        $subprocess->execute([ 'op' => 'read', 'params' => [ 'path' => $from ] ]);
        $subprocess->execute([ 'op' => 'write', 'params' => [ 'path' => $to ] ]);  // executes can be chained; stdout of previous execute becomes stdin of next
/*
        $data = \Flexio\Base\Stream::create();

        $subprocess = \Flexio\Jobs\Process::create();
        $subprocess->setOwner($process_owner_eid);
        $subprocess->setStdout($data);
        $subprocess->execute([ 'op' => 'read', 'params' => [ 'path' => $from ] ]);

        $subprocess = \Flexio\Jobs\Process::create();
        $subprocess->setOwner($process_owner_eid);
        $subprocess->setStdin($data);
        $subprocess->execute([ 'op' => 'write', 'params' => [ 'path' => $to ] ]);
*/
    }
}
