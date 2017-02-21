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



        // TEST: \Model::create(); multiple unique user creation

        // BEGIN TEST
        $total_count = 1000;
        $created_eids = array();
        $failed_user_creation = 0;
        for ($i = 0; $i < $total_count; $i++)
        {
            $handle1 = \Util::generateHandle();
            $handle2 = TestUtil::generateEmail();
            $info = array(
                'user_name' => $handle1,
                'email' => $handle2
            );
            $eid = $model->create(\Model::TYPE_USER, $info);
            $created_eids[$eid] = 1;
            if (!\Eid::isValid($eid))
                $failed_user_creation++;
        }
        $actual = count($created_eids) == $total_count && $failed_user_creation == 0;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::create(); creating users should succeed and produce a unique eid for each new user',  $actual, $expected, $results);
    }
}
