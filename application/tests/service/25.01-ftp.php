<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-05-16
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
        if (\Flexio\Tests\Base::TEST_SERVICE_FTP === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $service = new \Flexio\Services\Ftp;
        $actual = get_class($service);
        $expected = 'Flexio\Services\Ftp';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Ftp::create(); basic file syntax check',  $actual, $expected, $results);

        // BEGIN TEST
        $service = new \Flexio\Services\Ftp;
        $actual = ($service instanceof \Flexio\IFace\IConnection) ? true : false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'new \Flexio\Services\Ftp; instance of IConnection ',  $actual, $expected, $results);
    }
}
