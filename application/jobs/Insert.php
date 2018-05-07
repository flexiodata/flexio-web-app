<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-12
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
    {
        "op": "insert",
        "path": "test",
        "values": [ { "field": "value"}, ... ]
    }
*/

class Insert extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // create job adds new streams; don't clear existing streams
        $params = $this->getJobParameters();
        $path = $params['path'] ?? '';
        $values = $params['values'] ?? [];

        if (strlen($path) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);


        if (!$vfs->insert($path, $values))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

    }
}
