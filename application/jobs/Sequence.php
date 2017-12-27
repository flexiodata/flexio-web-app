<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-12-27
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "sequence",
    "params": {
        "items": []
    }
}
*/

class Sequence extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $job_definition = $this->getProperties();
        $job_task = $job_definition['params'];
        $job_sequence_tasks = $job_task['items'] ?? false;

        if ($job_sequence_tasks === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

        foreach ($job_sequence_tasks as $task)
        {
            $process->execute($task);
        }
    }
}
