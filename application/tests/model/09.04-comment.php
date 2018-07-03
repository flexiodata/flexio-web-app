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
        $model = \Flexio\Tests\Util::getModel()->comment;


        // TEST: \Flexio\Model\Comment::create(); when creating a comment, reject invalid parameters

        // BEGIN TEST
        $input_eid = 'xxxxxxxxxxxx';
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid' => $input_eid,
            'comment' => $handle
        );
        $eid = $model->create($info);
        $actual = $eid !== $input_eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Comment::create(); in comment creation, don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $eid_type = \Model::TYPE_USER;  // try something besides \Model::TYPE_UNDEFINED
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_type' => $eid_type,
            'comment' => $handle
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_COMMENT;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\Comment::create(); in comment creation, don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['name']);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Model\Comment::create(); in comment creation, don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: \Flexio\Model\Comment::create(); when creating a comment, make sure it has the essential fields and
        // make sure these are set when specified in the input

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Comment::create(); in comment creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_COMMENT,
            'eid_status' => \Model::STATUS_AVAILABLE,
            'comment' => '',
            'owned_by' => '',
            'created_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('B.2', '\Flexio\Model\Comment::create(); in comment creation, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: \Flexio\Model\Comment::create(); make sure fields that are specified are properly set

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
        \Flexio\Tests\Check::assertInArray('C.1', '\Flexio\Model\Comment::create(); in comment creation, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'comment' => 'Test comment'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'comment' => 'Test comment'
        );
        \Flexio\Tests\Check::assertInArray('C.2', '\Flexio\Model\Comment::create(); in comment creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'owned_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('C.3', '\Flexio\Model\Comment::create(); in comment creation, make sure parameter is set when specified',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('C.4', '\Flexio\Model\Comment::create(); in comment creation, make sure parameter is set when specified',  $actual, $expected, $results);
    }
}
