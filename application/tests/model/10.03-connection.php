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
    }
}
