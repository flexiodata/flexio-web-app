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
    "type": "flexio.read",
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

        // process buffer
        $outstream = $process->getStdout();
        $job_definition = $this->getProperties();
        $path = $job_definition['params']['path'] ?? null;

        if (is_null($path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing parameter 'path'");

        $outstream->setMimeType('text/plain');

        $streamwriter = $outstream->getWriter();

        $vfs = new \Flexio\Services\Vfs();
        $vfs->setProcess($process);
        $files = $vfs->read($path, function($data) use (&$streamwriter) {
            $streamwriter->write($data);
        });
    }
}
