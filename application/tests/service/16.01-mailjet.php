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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: service creation

        // BEGIN TEST
        $service = new \Flexio\Services\MailJet;
        $actual = get_class($service);
        $expected = 'Flexio\Services\MailJet';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\MailJet::create(); basic file syntax check',  $actual, $expected, $results);
    }
}
