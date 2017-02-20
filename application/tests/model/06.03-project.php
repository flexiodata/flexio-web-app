<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-11
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: Model::create(); multiple unique project creation

        // BEGIN TEST
        $total_count = 1000;
        $created_eids = array();
        $failed_project_creation = 0;
        for ($i = 0; $i < $total_count; $i++)
        {
            $handle = Util::generateHandle();
            $info = array(
            );
            $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
            $created_eids[$eid] = 1;
            if (!Eid::isValid($eid))
                $failed_project_creation++;
        }
        $actual = count($created_eids) == $total_count && $failed_project_creation == 0;
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Model::create(); creating projects should succeed and produce a unique eid for each new project',  $actual, $expected, $results);
    }
}
