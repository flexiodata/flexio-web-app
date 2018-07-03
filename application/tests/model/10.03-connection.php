<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-11
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = \Flexio\Tests\Util::getModel()->connection;


        // TEST: \Flexio\Model\Connection::create(); multiple unique connection creation

        // BEGIN TEST
        $total_count = 1000;
        $created_eids = array();
        $failed_connection_creation = 0;
        for ($i = 0; $i < $total_count; $i++)
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'name' => $handle,
                'description' => "Test connection $i"
            );
            $eid = $model->create($info);
            $created_eids[$eid] = 1;
            if (!\Flexio\Base\Eid::isValid($eid))
                $failed_connection_creation++;
        }
        $actual = count($created_eids) == $total_count && $failed_connection_creation == 0;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Connection::create(); creating connections should succeed and produce a unique eid for each new connection',  $actual, $expected, $results);
    }
}
