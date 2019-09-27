<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-09-12
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
        if (\Flexio\Tests\Base::TEST_SERVICE_PROCESSCONTEXT === false)
            return;


        // TEST: service creation

        // BEGIN TEST
        $instance = new \Flexio\Services\ProcessContext;
        $actual = get_class($instance);
        $expected = 'Flexio\Services\ProcessContext';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\ProcessContext; basic file syntax check',  $actual, $expected, $results);
    }
}
