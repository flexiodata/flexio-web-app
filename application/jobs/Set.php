<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-14
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "type": "flexio.set",
    "params": {
        "name": "myvar",
        "value": "new_value"
    }
}
*/

class Set extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        // get the duration
        $job_definition = $this->getProperties();
        $var = $job_definition['params']['var'];
        $value = $job_definition['params']['value'];
        
        $params = $process->getParams();
        $params[$var] = $value;
        $process->setParams($value);

        $process->getStdout()->getWriter()->write($value);
    }
}
