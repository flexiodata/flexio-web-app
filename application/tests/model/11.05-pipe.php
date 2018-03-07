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
        // TEST: when creating a pipe, reject invalid parameters

        // BEGIN TEST
        $input_eid = 'xxxxxxxxxxxx';
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid' => $input_eid,
            'name' => $handle
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = $eid !== $input_eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Model::create(); in pipe creation, don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $eid_type = \Model::TYPE_STREAM;  // try something besides \Model::TYPE_UNDEFINED
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_type' => $eid_type,
            'name' => $handle
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_PIPE;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Model::create(); in pipe creation, don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'xxx' => $handle
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['xxx']);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Model::create(); in pipe creation, don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: when creating a pipe, make sure it has the essential fields
        // and make sure these are set when specified in the input

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::create(); in pipe creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_PIPE,
            'name' => '',
            'description'  => '',
            'input' => '{}',
            'output' => '{}',
            'task' => '{}',
            'schedule' => '',
            'schedule_status' => \Model::PIPE_STATUS_INACTIVE,
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        \Flexio\Tests\Check::assertInArray('B.2', '\Model::create(); in name creation, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: make sure fields that are specified are properly set

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        \Flexio\Tests\Check::assertInArray('C.1', '\Model::create(); in pipe creation, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => 'Test pipe name'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'name' => 'Test pipe name'
        );
        \Flexio\Tests\Check::assertInArray('C.2', '\Model::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'description' => 'Test pipe description'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'description' => 'Test pipe description'
        );
        \Flexio\Tests\Check::assertInArray('C.3', '\Model::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'input' => '{}'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'input' => '{}'
        );
        \Flexio\Tests\Check::assertInArray('C.5', '\Model::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'output' => '{}'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'output' => '{}'
        );
        \Flexio\Tests\Check::assertInArray('C.6', '\Model::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'task' => '{}'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'task' => '{}'
        );
        \Flexio\Tests\Check::assertInArray('C.7', '\Model::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'schedule' => '{}'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'schedule' => '{}'
        );
        \Flexio\Tests\Check::assertInArray('C.8', '\Model::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'schedule_status' => 'A'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'schedule_status' => \Model::PIPE_STATUS_ACTIVE
        );
        \Flexio\Tests\Check::assertInArray('C.9', '\Model::create(); in pipe creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'schedule_status' => 'D' // valid inputs are A and I
            );
            $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::CREATE_FAILED
        );
        \Flexio\Tests\Check::assertInArray('C.10', '\Model::create(); in pipe creation, make sure parameters are valid',  $actual, $expected, $results);
    }
}
