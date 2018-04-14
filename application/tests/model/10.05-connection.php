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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = \Flexio\Tests\Util::getModel()->connection;


        // TEST: when creating a connection, reject invalid parameters

        // BEGIN TEST
        $input_eid = 'xxxxxxxxxxxx';
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid' => $input_eid,
            'name' => $handle
        );
        $eid = $model->create($info);
        $actual = $eid !== $input_eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Model::create(); in connection creation, don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $eid_type = \Model::TYPE_COMMENT;  // try something besides \Model::TYPE_UNDEFINED
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_type' => $eid_type,
            'name' => $handle
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_CONNECTION;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Model::create(); in connection creation, don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['comment']);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Model::create(); in connection creation, don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: when creating a connection, make sure it has the essential fields
        // and make sure these are set when specified in the input

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::create(); in connection creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_CONNECTION,
            'eid_status' => \Model::STATUS_AVAILABLE,
            'alias' => '',
            'name' => '',
            'description' => '',
            'connection_type' => '',
            'connection_status' => \Model::CONNECTION_STATUS_UNAVAILABLE,
            'connection_info' => '{}',
            'expires' => null,
            'owned_by' => '',
            'created_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('B.2', '\Model::create(); in connection creation, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: make sure fields that are specified are properly set

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        \Flexio\Tests\Check::assertInArray('C.1', '\Model::create(); in connection creation, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => 'Test Connection'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'name' => 'Test Connection'
        );
        \Flexio\Tests\Check::assertInArray('C.2', '\Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'description' => 'Test connection description'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'description' => 'Test connection description'
        );
        \Flexio\Tests\Check::assertInArray('C.3', '\Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'connection_status' => \Model::CONNECTION_STATUS_AVAILABLE
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'connection_status' => \Model::CONNECTION_STATUS_AVAILABLE
        );
        \Flexio\Tests\Check::assertInArray('C.5', '\Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'connection_type' => 'ct'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'connection_type' => 'ct'
        );
        \Flexio\Tests\Check::assertInArray('C.6', '\Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'connection_info' => 'ci'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'connection_info' => 'ci'
        );
        \Flexio\Tests\Check::assertInArray('C.7', '\Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'owned_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('C.8', '\Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $random_eid1 = \Flexio\Base\Eid::generate();
        $random_eid2 = \Flexio\Base\Eid::generate();
        $info = array(
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        \Flexio\Tests\Check::assertInArray('C.8', '\Model::create(); in connection creation, make sure parameter is set when specified',  $actual, $expected, $results);
    }
}
