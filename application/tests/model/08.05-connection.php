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



        // TEST: when creating a connection, reject invalid parameters

        // BEGIN TEST
        $model->clearErrors();
        $input_eid = 'xxxxxxxxxxxx';
        $handle = Util::generateHandle();
        $info = array(
            'eid' => $input_eid,
            'name' => $handle
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $eid !== $input_eid;
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Model::create(); in connection creation, don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $eid_type = Model::TYPE_COMMENT;  // try something besides Model::TYPE_UNDEFINED
        $handle = Util::generateHandle();
        $info = array(
            'eid_type' => $eid_type,
            'name' => $handle
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $info = $model->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === Model::TYPE_CONNECTION;
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Model::create(); in connection creation, don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $info = $model->get($eid);
        $actual = isset($info['comment']);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Model::create(); in connection creation, don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: when creating a connection, make sure it has the essential fields
        // and make sure these are set when specified in the input

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $info = $model->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Model::create(); in connection creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => Model::TYPE_CONNECTION,
            'name' => '',
            'description' => '',
            'display_icon' => '',
            'host' => '',
            'port' => 0,
            'connection_type' => '',
            'connection_status' => Model::CONNECTION_STATUS_UNAVAILABLE,
            'eid_status' => Model::STATUS_AVAILABLE
        );
        TestCheck::assertInArray('B.2', 'Model::create(); in connection creation, make sure essential fields are created',  $actual, $expected, $results);


/*
BIW: Jan, 2016: this test is no longer valid because connection model now does straight value storage
        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $object = $model->get($eid);
        $actual = isset($object['database']) && Eid::isValid($object['database']);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Model::create(); in connection creation, make sure essential fields are created',  $actual, $expected, $results);
*/



        // TEST: make sure fields that are specified are properly set

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'eid_status' => Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => Model::STATUS_PENDING
        );
        TestCheck::assertInArray('C.1', 'Model::create(); in connection creation, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'name' => 'Test Connection'
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $model->get($eid);
        $expected = array(
            'name' => 'Test Connection'
        );
        TestCheck::assertInArray('C.2', 'Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'description' => 'Test connection description'
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $model->get($eid);
        $expected = array(
            'description' => 'Test connection description'
        );
        TestCheck::assertInArray('C.3', 'Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'display_icon' => '<blank>'
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $model->get($eid);
        $expected = array(
            'display_icon' => '<blank>'
        );
        TestCheck::assertInArray('C.4', 'Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'connection_status' => Model::CONNECTION_STATUS_AVAILABLE
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $model->get($eid);
        $expected = array(
            'connection_status' => Model::CONNECTION_STATUS_AVAILABLE
        );
        TestCheck::assertInArray('C.5', 'Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'port' => 3025
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $model->get($eid);
        $expected = array(
            'port' => 3025
        );
        TestCheck::assertInArray('C.6', 'Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'database' => 'db'
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $model->get($eid);
        $expected = array(
            'database' => 'db'
        );
        TestCheck::assertInArray('C.7', 'Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'connection_type' => 'ct'
        );
        $eid = $model->create(Model::TYPE_CONNECTION, $info);
        $actual = $model->get($eid);
        $expected = array(
            'connection_type' => 'ct'
        );
        TestCheck::assertInArray('C.8', 'Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);
    }
}
