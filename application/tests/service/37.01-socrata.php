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
        if (\Flexio\Tests\Base::TEST_SERVICE_SOCRATA === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $service = new \Flexio\Services\Socrata;
        $actual = get_class($service);
        $expected = 'Flexio\Services\Socrata';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\Socrata::create(); basic file syntax check',  $actual, $expected, $results);
    }
}