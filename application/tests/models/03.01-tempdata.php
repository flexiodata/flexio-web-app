<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
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
        // TEST: model tempdata tests

        // BEGIN TEST
        $actual = true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Model tempdata tests',  $actual, $expected, $results);
    }
}
