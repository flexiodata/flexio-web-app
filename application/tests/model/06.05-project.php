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
        // TEST: when creating a project, reject invalid parameters

        // BEGIN TEST
        System::getModel()->clearErrors();
        $input_eid = 'xxxxxxxxxxxx';
        $handle = Util::generateHandle();
        $info = array(
            'eid' => $input_eid,
            'name' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $actual = $eid !== $input_eid;
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Model::create(); in project creation, don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $eid_type = Model::TYPE_COMMENT;  // try something besides Model::TYPE_UNDEFINED
        $handle = Util::generateHandle();
        $info = array(
            'eid_type' => $eid_type,
            'name' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $info = System::getModel()->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === Model::TYPE_PROJECT;
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Model::create(); in project creation, don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'user_name' => ''
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $info = System::getModel()->get($eid);
        $actual = isset($info['user_name']);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Model::create(); in project creation, don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: when creating a project, make sure it has the essential fields

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $info = System::getModel()->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Model::create(); in project creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $actual = System::getModel()->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => Model::TYPE_PROJECT,
            'name' => '',
            'description' => '',
            'display_icon' => '',
            'eid_status' => Model::STATUS_AVAILABLE
        );
        TestCheck::assertInArray('B.2', 'Model::create(); in project creation, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: make sure fields that are specified are properly set

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'eid_status' => Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $actual = System::getModel()->get($eid);
        $expected = array(
            'eid_status' => Model::STATUS_PENDING
        );
        TestCheck::assertInArray('C.1', 'Model::create(); in project creation, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'name' => 'Test Project'
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $actual = System::getModel()->get($eid);
        $expected = array(
            'name' => 'Test Project'
        );
        TestCheck::assertInArray('C.2', 'Model::create(); in project creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'description' => 'Test project description'
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $actual = System::getModel()->get($eid);
        $expected = array(
            'description' => 'Test project description'
        );
        TestCheck::assertInArray('C.3', 'Model::create(); in project creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'display_icon' => 'Test project display icon'
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $actual = System::getModel()->get($eid);
        $expected = array(
            'display_icon' => 'Test project display icon'
        );
        TestCheck::assertInArray('C.4', 'Model::create(); in project creation, make sure parameter is set when specified',  $actual, $expected, $results);
    }
}
