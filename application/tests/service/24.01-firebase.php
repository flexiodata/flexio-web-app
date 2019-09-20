<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-11
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
        if (\Flexio\Tests\Base::TEST_SERVICE_FIREBASE === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $service = new \Flexio\Services\Firebase;
        $actual = get_class($service);
        $expected = 'Flexio\Services\Firebase';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Firebase; basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $service = new \Flexio\Services\Firebase;
        $actual = ($service instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\Firebase; instance of IConnection ',  $actual, $expected, $results);
    }
}
