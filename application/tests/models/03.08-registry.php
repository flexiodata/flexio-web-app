<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-08
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
        // SETUP
        $model = \Flexio\Tests\Util::getModel()->registry;


        // TEST: multiple registry entry creation

        // BEGIN TEST
        $entry_count = 1000; // create/check/read 5000 entries
        $entries = array();
        for ($i = 0; $i < $entry_count; $i++)
        {
            $object_eid = '';
            $name = \Flexio\Base\Util::generateHandle();
            $value = $i;
            $model->setNumber($object_eid, $name, $value); // expires in 4 seconds
            if ($model->entryExists($object_eid, $name))
                $entries[] = $name;
        }
        $read_successes = 0;
        $entry_idx = 0;
        foreach ($entries as $i)
        {
            $object_eid = '';
            $name = $i;
            $read_value = $model->getNumber($object_eid, $name);
            if ($read_value == $entry_idx)
                $read_successes++;
            $entry_idx++;
        }
        $actual = count($entries) === $entry_count && $read_successes === $entry_count;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Registry; number of actual created entries vs. expected created entries', $actual, $expected, $results);
    }
}
