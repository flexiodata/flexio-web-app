<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-04
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
        // TEST: valid eid creation; create and check a number of eids

        // BEGIN TEST
        $total_eid_count = 1000;
        $valid_eid_count = 0;

        for ($i = 0; $i < $total_eid_count; ++$i)
        {
            $eid = \Flexio\Base\Eid::generate();
            $valid = \Flexio\Base\Eid::isValid($eid);

            if ($valid === true)
                $valid_eid_count++;
        }

        $actual = ($valid_eid_count === $total_eid_count); // all eids that were created should be valid
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Eid::generate() test for valid eid creation', $actual, $expected, $results);
    }
}
