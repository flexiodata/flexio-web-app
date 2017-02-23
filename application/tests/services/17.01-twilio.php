<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-31
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add additional tests

        // TEST: service creation

        // BEGIN TEST
        $service = \Flexio\Services\TwilioService::create(null);
        $actual = get_class($service);
        $expected = 'Flexio\Services\TwilioService';
        TestCheck::assertString('A.1', '\Flexio\Services\TwilioService::create(); create an instance of the class',  $actual, $expected, $results);
    }
}
