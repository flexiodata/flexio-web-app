<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-09-12
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
        if (\Flexio\Tests\Base::TEST_SERVICE_GMAIL === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\Gmail;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\Gmail';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Gmail::create(); basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\Gmail;
        $actual = ($instance instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\Gmail; instance of IConnection ',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\Gmail;
        $actual = ($instance instanceof \Flexio\IFace\IOAuthConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', 'new \Flexio\Services\Gmail; instance of IOAuthConnection ',  $actual, $expected, $results);
    }
}
