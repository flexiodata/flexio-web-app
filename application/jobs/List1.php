<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-10-06
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "list",
    "path": ""
}
*/

class List1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // process buffer
        $outstream = $process->getStdout();
        $params = $this->getJobParameters();
        $path = $params['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'path'");

        $parts = \Flexio\Base\File::splitPath($path);
        $lastpart = array_pop($parts);


        foreach ($parts as $part)
        {
            if (strpos($part, '*') !== false)
            {
                // only the last part of the path may contain a wildcard
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid parameter 'path'. Only the last part of the path may contain a wildcard");
            }
        }

        $wildcard = null;
        if ($lastpart !== null)
        {
            if (strpos($lastpart, '*') !== false)
                $wildcard = $lastpart;
                else
                $parts[] = $lastpart;
        }

        $path = '/' . implode('/', $parts);


        $streamwriter = $outstream->getWriter();



        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);
        $files = $vfs->list($path);

        $results = [];
        foreach ($files as $f)
        {
            if ($wildcard !== null)
            {
                if (!\Flexio\Base\File::matchPath($f['name'], $wildcard, false))
                    continue;
            }

            $entry = array(
                'name' => $f['name'],
                'path' => $f['path'],
                'size' => $f['size'],
                'modified' => $f['modified'],
                'type' => $f['type']
            );

            if (isset($f['.connection_eid']))
                $entry['.connection_eid'] = $f['.connection_eid'];

            if (isset($f['.connection_type']))
                $entry['.connection_type'] = $f['.connection_type'];

            $results[] = $entry;
        }

        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
        $streamwriter->write(json_encode($results));
    }
}
