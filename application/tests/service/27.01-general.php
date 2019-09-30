<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-09-30
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
        if (\Flexio\Tests\Base::TEST_SERVICE_GENERAL === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\General;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\General';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\General; basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\General;
        $actual = ($instance instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\General; instance of IConnection ',  $actual, $expected, $results);
    }
}
