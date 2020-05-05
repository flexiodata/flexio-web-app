<?php
/**
 *
 * Copyright (c) 2019, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-11-22
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
        if (\Flexio\Tests\Base::TEST_SERVICE_CAPSULECRM === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\CapsuleCRM;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\CapsuleCRM';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\CapsuleCRM::create(); basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\CapsuleCRM;
        $actual = ($instance instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\CapsuleCRM; instance of IConnection ',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\CapsuleCRM;
        $actual = ($instance instanceof \Flexio\IFace\IOAuthConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', 'new \Flexio\Services\CapsuleCRM; instance of IOAuthConnection ',  $actual, $expected, $results);
    }
}
