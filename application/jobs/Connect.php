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
        "alias": <string>,
        "type": <string>, // connection type; required to specify type or full connection identifier
        "connection": <connection identifier>, required to specify type or full connection identifier
        <additional connection parameters here>
    }
}
*/

class Connect extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process)
    {
        parent::run($process);

        $params = $this->getJobParameters();

        $alias = $params['alias'] ?? false;
        unset($params['alias']);

        $connection_type = $params['type'] ?? false;
        unset($params['type']);

        $connection_identifier = $params['connection'] ?? false;
        unset($params['connection']);

        // get the alias to use to reference the connection; must be a valid identifier (no strictly necessary
        // here, but enforced to parallel the requirements of 'alias')
        if ($alias === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing connection 'alias' parameter.");
        if ($connection_identifier === false && $connection_type === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "Missing connection 'connection' or 'type' parameter.");
        if (\Flexio\Base\Identifier::isValid($alias) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid connection 'alias' parameter; 'alias' must be a valid identifier (lowercase string between 3 and 80 chars made up of only letters, numbers, hyphens and underscores)");

        $connection_properties = array();

        if ($connection_identifier === false)
        {
            // if a connection isn't specified, use the connection type and supplied params to
            // create the connection
            $connection_properties = array(
                'connection_type' => $connection_type,
                'connection_info' => $params
            );
        }
         else
        {
            // if the connection identifier is specified, use that to try to use it to get the
            // connection type and params

            $owner_user_eid = $process->getOwner();

            // load the connection
            if (\Flexio\Base\Eid::isValid($connection_identifier) === false)
            {
                $eid_from_identifier = \Flexio\Object\Connection::getEidFromName($owner_user_eid, $connection_identifier);
                $connection_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
            }

            $connection = \Flexio\Object\Connection::load($connection_identifier);
            $connection_properties = $connection->get();

            $connection_properties = array(
                'connection_type' => $connection_properties['connection_type'],
                'connection_info' => $connection_properties['connection_info']
            );
        }

        // attempt to connect to the service; throw an exception if we can't connect
        $service = \Flexio\Services\Factory::create($connection_properties);
        if (!$service)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CONNECTION_FAILED, "Could not create connection");

        // add the connection to the process
        $process->addLocalConnection($alias, $connection_properties);
    }
}
