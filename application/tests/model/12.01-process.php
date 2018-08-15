<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-22
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
        // FUNCTION: \Flexio\Model\Process::create()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->process;


        // TEST: creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Process::create(); for process creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);


        // TEST: creation with basic input

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


        // TEST: creation with basic parameters

        // TODO: add tests

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'owned_by' => ''
        );
        \Flexio\Tests\Check::assertInArray('C.1', '\Flexio\Model\Process::create(); in process creation, make sure parameter is set when specified',  $actual, $expected, $results);

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
    }
}
