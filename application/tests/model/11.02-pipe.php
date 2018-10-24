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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // FUNCTION: \Flexio\Model\Pipe::create()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->pipe;


        // TEST: reject invalid parameters

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
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Pipe::create(); in pipe creation, don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $eid_type = \Model::TYPE_STREAM;  // try something besides \Model::TYPE_UNDEFINED
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_type' => $eid_type,
            'name' => $handle
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_PIPE;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\Pipe::create(); in pipe creation, don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'xxx' => $handle
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['xxx']);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Model\Pipe::create(); in pipe creation, don\'t allow random parameters to be set',  $actual, $expected, $results);


        // TEST:  make sure it has the essential fields and make sure these are set when specified in the input

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Pipe::create(); in pipe creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_PIPE,
            'eid_status' => \Model::STATUS_AVAILABLE,
            'alias' => '',
            'name' => '',
            'description'  => '',
            'task' => '{}',
            'schedule' => '',
            'ui' => '{}',
            'deploy_mode' => \Model::PIPE_DEPLOY_MODE_BUILD,
            'deploy_schedule' => \Model::PIPE_DEPLOY_STATUS_INACTIVE,
            'deploy_email' => \Model::PIPE_DEPLOY_STATUS_INACTIVE,
            'deploy_api' => \Model::PIPE_DEPLOY_STATUS_INACTIVE,
            'deploy_ui' => \Model::PIPE_DEPLOY_STATUS_INACTIVE,
            'owned_by' => '',
            'created_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('B.2', '\Flexio\Model\Pipe::create(); in name creation, make sure essential fields are created',  $actual, $expected, $results);


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
        \Flexio\Tests\Check::assertInArray('C.1', '\Flexio\Model\Pipe::create(); in pipe creation, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => 'Test pipe name'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'name' => 'Test pipe name'
        );
        \Flexio\Tests\Check::assertInArray('C.2', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'description' => 'Test pipe description'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'description' => 'Test pipe description'
        );
        \Flexio\Tests\Check::assertInArray('C.3', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'ui' => '{}'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'ui' => '{}'
        );
        \Flexio\Tests\Check::assertInArray('C.4', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'task' => '{}'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'task' => '{}'
        );
        \Flexio\Tests\Check::assertInArray('C.5', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'schedule' => '{}'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'schedule' => '{}'
        );
        \Flexio\Tests\Check::assertInArray('C.6', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'deploy_mode' => \Model::PIPE_DEPLOY_MODE_BUILD
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'deploy_mode' => \Model::PIPE_DEPLOY_MODE_BUILD
        );
        \Flexio\Tests\Check::assertInArray('C.7', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'deploy_schedule' => 'A',
            'deploy_email' => 'A',
            'deploy_api' => 'A',
            'deploy_ui' => 'A'
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'deploy_schedule' => \Model::PIPE_DEPLOY_STATUS_ACTIVE,
            'deploy_email' => \Model::PIPE_DEPLOY_STATUS_ACTIVE,
            'deploy_api' => \Model::PIPE_DEPLOY_STATUS_ACTIVE,
            'deploy_ui' => \Model::PIPE_DEPLOY_STATUS_ACTIVE
        );
        \Flexio\Tests\Check::assertInArray('C.8', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'deploy_schedule' => 'D' // valid inputs are A and I
            );
            $eid = $model->create($info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_SYNTAX
        );
        \Flexio\Tests\Check::assertInArray('C.9', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameters are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'deploy_email' => 'D' // valid inputs are A and I
            );
            $eid = $model->create($info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_SYNTAX
        );
        \Flexio\Tests\Check::assertInArray('C.10', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameters are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'deploy_api' => 'D' // valid inputs are A and I
            );
            $eid = $model->create($info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_SYNTAX
        );
        \Flexio\Tests\Check::assertInArray('C.11', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameters are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'deploy_ui' => 'D' // valid inputs are A and I
            );
            $eid = $model->create($info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_SYNTAX
        );
        \Flexio\Tests\Check::assertInArray('C.12', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameters are valid',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'owned_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('C.13', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('C.14', '\Flexio\Model\Pipe::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);
    }
}
