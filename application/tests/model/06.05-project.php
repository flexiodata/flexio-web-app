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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: when creating a project, reject invalid parameters

        // BEGIN TEST
        $input_eid = 'xxxxxxxxxxxx';
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid' => $input_eid,
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $actual = $eid !== $input_eid;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::create(); in project creation, don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $eid_type = \Model::TYPE_COMMENT;  // try something besides \Model::TYPE_UNDEFINED
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_type' => $eid_type,
            'name' => $handle
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $info = $model->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_PROJECT;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Model::create(); in project creation, don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'user_name' => ''
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $info = $model->get($eid);
        $actual = isset($info['user_name']);
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::create(); in project creation, don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: when creating a project, make sure it has the essential fields

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $info = $model->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::create(); in project creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_PROJECT,
            'name' => '',
            'description' => '',
            'display_icon' => '',
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        TestCheck::assertInArray('B.2', '\Model::create(); in project creation, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: make sure fields that are specified are properly set

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        TestCheck::assertInArray('C.1', '\Model::create(); in project creation, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'eid_status' => 'bad' // invalid status
            );
            $eid = $model->create(\Model::TYPE_PROJECT, $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::CREATE_FAILED
        );
        TestCheck::assertInArray('C.2', '\Model::create(); in project creation, throw an exception for an invalid property value',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => 'Test Project'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $actual = $model->get($eid);
        $expected = array(
            'name' => 'Test Project'
        );
        TestCheck::assertInArray('C.3', '\Model::create(); in project creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'description' => 'Test project description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $actual = $model->get($eid);
        $expected = array(
            'description' => 'Test project description'
        );
        TestCheck::assertInArray('C.4', '\Model::create(); in project creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'display_icon' => 'Test project display icon'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $actual = $model->get($eid);
        $expected = array(
            'display_icon' => 'Test project display icon'
        );
        TestCheck::assertInArray('C.5', '\Model::create(); in project creation, make sure parameter is set when specified',  $actual, $expected, $results);
    }
}
