<?php
/**
 *
 * Copyright (c) 2019, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-12-20
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
        if (\Flexio\Tests\Base::TEST_SERVICE_EVENTBRITE === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\Eventbrite;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\Eventbrite';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Eventbrite::create(); basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\Eventbrite;
        $actual = ($instance instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\Eventbrite; instance of IConnection ',  $actual, $expected, $results);

        // BEGIN TEST
        $instance = new \Flexio\Services\Eventbrite;
        $actual = ($instance instanceof \Flexio\IFace\IOAuthConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', 'new \Flexio\Services\Eventbrite; instance of IOAuthConnection ',  $actual, $expected, $results);
    }
}
