<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-08
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: multiple registry entry creation

        // BEGIN TEST
        $entry_count = 1000; // create/check/read 5000 entries
        $entries = array();
        for ($i = 0; $i < $entry_count; $i++)
        {
            $object_eid = '';
            $name = Util::generateHandle();
            $value = $i;
            System::getModel()->registry->setNumber($object_eid, $name, $value); // expires in 4 seconds
            if (System::getModel()->registry->entryExists($object_eid, $name))
                $entries[] = $name;
        }
        $read_successes = 0;
        $entry_idx = 0;
        foreach ($entries as $i)
        {
            $object_eid = '';
            $name = $i;
            $read_value = System::getModel()->registry->getNumber($object_eid, $name);
            if ($read_value === $entry_idx)
                $read_successes++;
            $entry_idx++;
        }
        $actual = count($entries) === $entry_count && $read_successes === $entry_count;
        $expected = true;
        TestCheck::assertBoolean('A.1', 'RegistryModel; number of actual created entries vs. expected created entries', $actual, $expected, $results);
    }
}
