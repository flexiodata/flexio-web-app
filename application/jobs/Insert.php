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
        "type": "flexio.insert",
        "params": {
            "path": "test",
            "values": [ { "field": "value"}, ... ]
        }
    }
*/

class Insert extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // create job adds new streams; don't clear existing streams
        $job_definition = $this->getProperties();
        $params = $job_definition['params'] ?? [];


        // TODO: factor

        if (isset($params['path']))
        {
            $columns = $params['columns'] ?? [];

            $vfs = new \Flexio\Services\Vfs();
            $vfs->setProcess($process);

            $create_params = [];
            if (is_array($columns) && count($columns) > 0)
            {
                $create_params['structure'] = $columns;
            }

            if (!$vfs->createFile($params['path'], $create_params))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            // TODO: return created stream in stdout
        }

    }
}
