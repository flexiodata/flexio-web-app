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
        if (\Flexio\Tests\Base::TEST_STORAGE_GOOGLESHEETS === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $service = new \Flexio\Services\GoogleSheets;
        $actual = get_class($service);
        $expected = 'Flexio\Services\GoogleSheets';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\GoogleSheets; basic file syntax check',  $actual, $expected, $results);
    }
}
