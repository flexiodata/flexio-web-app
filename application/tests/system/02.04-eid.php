<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-04
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: valid eid creation; create and check a number of eids

        // BEGIN TEST
        $total_eid_count = 1000;
        $valid_eid_count = 0;

        for ($i = 0; $i < $total_eid_count; ++$i)
        {
            $eid = \Eid::generate();
            $valid = \Eid::isValid($eid);

            if ($valid === true)
                $valid_eid_count++;
        }

        $actual = ($valid_eid_count === $total_eid_count); // all eids that were created should be valid
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Eid::generate() test for valid eid creation', $actual, $expected, $results);
    }
}
