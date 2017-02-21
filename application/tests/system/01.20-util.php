<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: platform check tests; see if we're on a standard platform

        // BEGIN TEST
        $is_windows = \Util::isPlatformWindows();
        $is_mac = \Util::isPlatformMac();
        $is_linux = \Util::isPlatformLinux();
        $platform_check = $is_windows || $is_mac || $is_linux;
        $actual = $platform_check;
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Platform detection test',  $actual, $expected, $results);
    }
}
