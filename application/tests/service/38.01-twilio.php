<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-31
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
        if (\Flexio\Tests\Base::TEST_SERVICE_TWILIO === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $service = new \Flexio\Services\Twilio;
        $actual = get_class($service);
        $expected = 'Flexio\Services\Twilio';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Twilio; basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $service = new \Flexio\Services\Twilio;
        $actual = ($service instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\Twilio; instance of IConnection ',  $actual, $expected, $results);
    }
}
