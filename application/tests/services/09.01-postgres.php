<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-05-16
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TODO: add additional tests

        // TEST: service creation

        // BEGIN TEST
        $service = PostgresService::create(null);
        $actual = get_class($service) === 'PostgresService';
        $expected = true;
        TestCheck::assertBoolean('A.1', 'PostgresService::create(); create an instance of the class',  $actual, $expected, $results);
    }
}
