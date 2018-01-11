<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-01-11
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "diff",
    "params": {
        "file1": "",
        "file2": ""
    }
}
*/

class Diff extends \Flexio\Jobs\Base
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

        $streamwriter = $outstream->getWriter();

        $vfs = new \Flexio\Services\Vfs();
        $vfs->setProcess($process);
        $files = $vfs->list($path);



        $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
        $streamwriter->write(json_encode($results));
    }
}
