<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-23
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::create(); multiple unique pipe creation

        // BEGIN TEST
        $total_count = 1000;
        $created_eids = array();
        $failed_pipe_creation = 0;
        for ($i = 0; $i < $total_count; $i++)
        {
            $handle = \Flexio\System\Util::generateHandle();
            $info = array(
                'name' => "Test $i",
                'description' => "Test $i description"
            );
            $eid = $model->create(\Model::TYPE_PIPE, $info);
            $created_eids[$eid] = 1;
            if (!\Flexio\System\Eid::isValid($eid))
                $failed_pipe_creation++;
        }
        $actual = count($created_eids) == $total_count && $failed_pipe_creation == 0;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::create(); creating pipes should succeed and produce a unique eid for each new pipe',  $actual, $expected, $results);
    }
}
