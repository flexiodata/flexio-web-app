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


class Test
{
    public function run(&$results)
    {
        // TEST: Model database connection

        // BEGIN TEST
        $db = System::getModel()->getDatabase();
        $actual = is_object($db);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Model::getDatabase(); basic connection with default credentials',  $actual, $expected, $results);



        // TODO: check connection failure



        // TEST: Model database check connection caching

        // BEGIN TEST
        $actual = true;
        for ($i = 0; $i < 10000; $i++)
        {
            $db = System::getModel()->getDatabase();
            if (!$db)
            {
                $actual = false;
                break;
            }
        }
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Model::getDatabase(); connection caching',  $actual, $expected, $results);
    }
}
