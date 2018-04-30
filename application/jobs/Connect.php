<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-23
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// EXAMPLE:
{
    "op": "connect",
    "params": {
    }
}
*/

class Connect extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $params = $this->getJobParameters();


        $handle = $params['handle'] ?? '';
        // TODO: check for valid connection type; throw if not found
        //if ($connection_type not in 'dropbox'....)
        //  throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Connection handle is missing or invalid");

        unset($params['handle']);


        $connection_type = $params['type'] ?? '';

        // TODO: check for valid connection type; throw if not found
        //if ($connection_type not in 'dropbox'....)
        //  throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND, "Connection type is missing or invalid");
        
        unset($params['type']);


        $connection_properties = [ 'connection_type' => $connection_type, 'connection_info' => $params ];

        $process->addLocalConnection($handle, $connection_properties);
    }
}
