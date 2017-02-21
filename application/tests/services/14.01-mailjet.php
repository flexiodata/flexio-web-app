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
        $service = \MailJetService::create(null);
        $actual = get_class($service) === 'MailJetService';
        $expected = true;
        TestCheck::assertBoolean('A.1', '\MailJetService::create(); create an instance of the class',  $actual, $expected, $results);
    }
}
