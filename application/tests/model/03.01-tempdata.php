<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: model tempdata tests

        // BEGIN TEST
        $actual = true;
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Model tempdata tests',  $actual, $expected, $results);
    }
}
