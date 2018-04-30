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

        $handle = $params['handle'] ?? false;
        unset($params['handle']);

        $connection_type = $params['type'] ?? false;
        unset($params['type']);

        $connection_properties = [ 'connection_type' => $connection_type, 'connection_info' => $params ];

        // get the handle to use to reference the connection; must be a valid identifier (no strictly necessary
        // here, but enforced to parallel the requirements of 'alias')
        if ($handle === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing connection 'handle' parameter.");
        if ($type === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing connection 'type' parameter.");

        if (\Flexio\Base\Identifier::isValid($handle) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid connection 'handle' parameter; 'handle' must be a valid identifier (lowercase string between 3 and 80 chars made up of only letters, numbers, hyphens and underscores)");

        // attempt to connect to the service; throw an exception if we can't connect
        $service = \Flexio\Services\Factory::create($connection_properties);
        if (!$service)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED, "Could not create connection");

        // add the connection to the process
        $process->addLocalConnection($handle, $connection_properties);
    }
}
