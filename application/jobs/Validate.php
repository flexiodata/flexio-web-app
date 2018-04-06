<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-06
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "validate",
    "params": {
        "path": ""
    }
}
*/

class Validate extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
    }
}
