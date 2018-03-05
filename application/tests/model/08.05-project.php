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
        // TEST: when creating a project, reject invalid parameters

        // BEGIN TEST
        $input_eid = 'xxxxxxxxxxxx';
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid' => $input_eid,
            'name' => $handle
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PROJECT, $info);
        $actual = $eid !== $input_eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Model::create(); in project creation, don\'t allow the eid to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $eid_type = \Model::TYPE_COMMENT;  // try something besides \Model::TYPE_UNDEFINED
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_type' => $eid_type,
            'name' => $handle
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PROJECT, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['eid_type']) && $info['eid_type'] === \Model::TYPE_PROJECT;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Model::create(); in project creation, don\'t allow the eid_type to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'user_name' => ''
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PROJECT, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['user_name']);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Model::create(); in project creation, don\'t allow random parameters to be set',  $actual, $expected, $results);



        // TEST: when creating a project, make sure it has the essential fields

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PROJECT, $info);
        $info = \Flexio\Tests\Util::getModel()->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::create(); in project creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PROJECT, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_PROJECT,
            'name' => '',
            'description' => '',
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        \Flexio\Tests\Check::assertInArray('B.2', '\Model::create(); in project creation, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: make sure fields that are specified are properly set

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING // currently, items are created in active state
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PROJECT, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        \Flexio\Tests\Check::assertInArray('C.1', '\Model::create(); in project creation, allow eid_status to be set',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'eid_status' => 'bad' // invalid status
            );
            $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PROJECT, $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::CREATE_FAILED
        );
        \Flexio\Tests\Check::assertInArray('C.2', '\Model::create(); in project creation, throw an exception for an invalid property value',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => 'Test Project'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PROJECT, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'name' => 'Test Project'
        );
        \Flexio\Tests\Check::assertInArray('C.3', '\Model::create(); in project creation, make sure parameter is set when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'description' => 'Test project description'
        );
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PROJECT, $info);
        $actual = \Flexio\Tests\Util::getModel()->get($eid);
        $expected = array(
            'description' => 'Test project description'
        );
        \Flexio\Tests\Check::assertInArray('C.4', '\Model::create(); in project creation, make sure parameter is set when specified',  $actual, $expected, $results);
    }
}
