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
    "type": "flexio.read",
    "params": {
        "path": ""
    }
}
*/

class Read extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Jobs\IProcess $process)
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
        $files = $vfs->read($path, function($data) use (&$streamwriter) {
            $streamwriter->write($data);
        });
    }
}
