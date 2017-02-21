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
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::create(); project creation with no parameters

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $actual = Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::create(); for project creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Model::create(); for project creation, don\'t require input parameters; don\'t flag any errors',  $actual, $expected, $results);



        // TEST: \Model::create(); project creation with basic name input

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $actual = Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::create(); make sure valid eid is returned when project with name is created',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid_first_time_creation = $model->create(\Model::TYPE_PROJECT, $info);
        $eid_second_time_creation = $model->create(\Model::TYPE_PROJECT, $info);
        $actual = (Eid::isValid($eid_first_time_creation) && Eid::isValid($eid_second_time_creation));
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Model::create(); allow multiple projects with the same name',  $actual, $expected, $results);
    }
}
