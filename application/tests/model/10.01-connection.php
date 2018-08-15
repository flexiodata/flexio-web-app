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
        // FUNCTION: \Flexio\Model\Connection::create()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->connection;


        // TEST: creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Connection::create(); for connection creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);


        // TEST: creation with basic input

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Connection::create(); make sure valid eid is returned when connection is created',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid_first_time_creation = $model->create($info);
        $eid_second_time_creation = $model->create($info);
        $actual = (\Flexio\Base\Eid::isValid($eid_first_time_creation) && \Flexio\Base\Eid::isValid($eid_second_time_creation));
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Model\Connection::create(); allow multiple connections with the same value',  $actual, $expected, $results);
    }
}
