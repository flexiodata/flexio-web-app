<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-29
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
        $model = \Flexio\Tests\Util::getModel()->process;


        // TEST: \Flexio\Model\Process::create(); process creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Process::create(); for process creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);



        // TEST: \Flexio\Model\Process::create(); when creating a pipe, make sure it has the essential fields
        // and make sure these are set when specified in the input

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create($info);
        $info = $model->get($eid);
        $actual = isset($info['eid']) && isset($info['eid_type']) && isset($info['created']) && isset($info['updated']);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Process::create(); in process creation, make sure the identifier and date fields are returned',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'eid_type' => \Model::TYPE_PROCESS,
            'eid_status' => \Model::STATUS_AVAILABLE,
            'parent_eid' => '',
            'process_mode' => '',
            'impl_revision' => '',
            'input' => '{}',
            'output' => '{}',
            'task' => '{}',
            'started_by' => '',
            'started' => null,
            'finished' => null,
            'duration' => null,
            'process_info' => '{}',
            'process_status' => '',
            'cache_used' => '',
            'owned_by' => '',
            'created_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('B.2', '\Flexio\Model\Process::create(); in process creation, make sure essential fields are created',  $actual, $expected, $results);



        // TEST: \Flexio\Model\Process::create(); process creation with basic parameters

        // TODO: add tests

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'owned_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('C.11', '\Flexio\Model\Process::create(); in process creation, make sure parameter is set when specified',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('C.2', '\Flexio\Model\Process::create(); in process creation, make sure parameter is set when specified',  $actual, $expected, $results);



        // TEST: \Flexio\Model\Process::set(); make settable properties are set

        // BEGIN TEST
        $random_eid1 = \Flexio\Base\Eid::generate();
        $random_eid2 = \Flexio\Base\Eid::generate();
        $info = array(
        );
        $eid = $model->create($info);
        $info = array(
            'eid_status' => \Model::STATUS_PENDING,
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        $result = $model->set($eid, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid_status' => \Model::STATUS_PENDING,
            'owned_by' => $random_eid1,
            'created_by' => $random_eid2
        );
        \Flexio\Tests\Check::assertInArray('E.1', '\Flexio\Model\Process::set(); make sure properties are updated',  $actual, $expected, $results);
    }
}
