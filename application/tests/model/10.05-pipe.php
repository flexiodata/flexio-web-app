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


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: when creating a container, reject invalid parameters

        // BEGIN TEST
        $model->clearErrors();
        $input_eid = 'xxxxxxxxxxxx';
        $handle = \Util::generateHandle();
        $info = array(
            'eid' => $input_eid,
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $eid !== $input_eid;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::create(); in container creation, don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $eid_type = \Model::TYPE_PROJECT;  // try something besides \Model::TYPE_UNDEFINED
        $handle = \Util::generateHandle();
        $info = array(
            'eid_type' => $eid_type,
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $info = $model->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_PIPE;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Model::create(); in container creation, don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'xxx' => $handle
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $info = $model->get($eid);
        $actual = isset($info['xxx']);
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::create(); in container creation, don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: when creating a container, make sure it has the essential fields
        // and make sure these are set when specified in the input

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $info = $model->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::create(); in container creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_PIPE,
            'name' => '',
            'description'  => '',
            'display_icon' => '',
            'input' => '[]',
            'output' => '[]',
            'task' => '[]',
            'schedule' => '',
            'schedule_status' => \Model::PIPE_STATUS_INACTIVE,
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        TestCheck::assertInArray('B.2', '\Model::create(); in name creation, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: make sure fields that are specified are properly set

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        TestCheck::assertInArray('C.1', '\Model::create(); in container creation, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'name' => 'Test container name'
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'name' => 'Test container name'
        );
        TestCheck::assertInArray('C.2', '\Model::create(); in container creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'description' => 'Test container description'
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'description' => 'Test container description'
        );
        TestCheck::assertInArray('C.3', '\Model::create(); in container creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'display_icon' => 'Test container display icon data'
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'display_icon' => 'Test container display icon data'
        );
        TestCheck::assertInArray('C.4', '\Model::create(); in container creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'input' => '{}'
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'input' => '{}'
        );
        TestCheck::assertInArray('C.5', '\Model::create(); in container creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'output' => '{}'
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'output' => '{}'
        );
        TestCheck::assertInArray('C.6', '\Model::create(); in container creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'task' => '{}'
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'task' => '{}'
        );
        TestCheck::assertInArray('C.7', '\Model::create(); in container creation, make sure parameter is set when specified',  $actual, $expected, $results);


        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'schedule' => '{}'
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'schedule' => '{}'
        );
        TestCheck::assertInArray('C.8', '\Model::create(); in container creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'schedule_status' => 'A'
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'schedule_status' => \Model::PIPE_STATUS_ACTIVE
        );
        TestCheck::assertInArray('C.9', '\Model::create(); in container creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Util::generateHandle();
        $info = array(
            'schedule_status' => 'D' // valid inputs are A and I
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = $model->get($eid);
        $expected = array(
            'schedule_status' => \Model::PIPE_STATUS_INACTIVE
        );
        TestCheck::assertInArray('C.10', '\Model::create(); in container creation, make sure parameter is set when specified',  $actual, $expected, $results);
    }
}
