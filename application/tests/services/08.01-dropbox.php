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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add additional tests

        // TEST: service creation

        // BEGIN TEST
        $service = \Flexio\Services\Dropbox::create(null);
        $actual = get_class($service);
        $expected = 'Flexio\Services\Dropbox';
        TestCheck::assertString('A.1', '\Flexio\Services\Dropbox::create(); create an instance of the class',  $actual, $expected, $results);
    }
}
