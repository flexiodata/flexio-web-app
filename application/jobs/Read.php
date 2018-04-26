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
    }
}
