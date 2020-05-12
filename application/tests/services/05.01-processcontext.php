<?php
/**
 *
 * Copyright (c) 2019, Flex Research LLC. All rights reserved.
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
        $process_engine = \Flexio\Jobs\Process::create();
        $instance = new \Flexio\Services\ProcessContext($process_engine);
        $actual = get_class($instance);
        $expected = 'Flexio\Services\ProcessContext';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Services\ProcessContext; basic file syntax check',  $actual, $expected, $results);
    }
}
