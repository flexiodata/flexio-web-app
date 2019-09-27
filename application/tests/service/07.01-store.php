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
        if (\Flexio\Tests\Base::TEST_SERVICE_STORE === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\Store;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\Store';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Store; basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\Store;
        $actual = ($instance instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\Store; instance of IConnection ',  $actual, $expected, $results);
    }
}
