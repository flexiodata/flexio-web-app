<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-08
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
        $model = \Flexio\Tests\Util::getModel()->action;


        // TEST: \Model::create(); action creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Action::create(); for action creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);



        // TEST: Action::create(); action creation with basic input

        // BEGIN TEST
        $info = array(
            'action_type' => 'action.test',
            'request_ip' => '127.0.0.1',
            'request_type' => 'HTTP',
            'request_method' => 'POST',
            'request_route' => '/api/url/endpoint',
            'request_created_by' => 'bxxxxxxxxxxx', // request_created_by, owned_by, and created_by will be the same in use; make sure they're uncoupled in the model
            'request_created' => '2018-01-02 01:02:03',
            'request_params' => '{}',
            'target_eid' => 'byyyyyyyyyyy',
            'target_eid_type' => 'PIP',
            'target_owned_by' => 'cyyyyyyyyyyy',
            'response_type' => 'HTTP',
            'response_code' => '200',
            'response_params' => '{}',
            'response_created' => '2018-01-02 01:02:04',
            'owned_by' => 'cxxxxxxxxxxx', // request_created_by, owned_by, and created_by will be the same in use; make sure they're uncoupled in the model
            'created_by' => 'dxxxxxxxxxxx' // request_created_by, owned_by, and created_by will be the same in use; make sure they're uncoupled in the model
        );
        $eid = $model->create($info);
        $actual = $model->get($eid);
        $expected = array(
            'action_type' => 'action.test',
            'request_ip' => '127.0.0.1',
            'request_type' => 'HTTP',
            'request_method' => 'POST',
            'request_route' => '/api/url/endpoint',
            'request_created_by' => 'bxxxxxxxxxxx',
            'request_created' => '2018-01-02 01:02:03',
            'request_params' => '{}',
            'target_eid' => 'byyyyyyyyyyy',
            'target_eid_type' => 'PIP',
            'target_owned_by' => 'cyyyyyyyyyyy',
            'response_type' => 'HTTP',
            'response_code' => '200',
            'response_params' => '{}',
            'response_created' => '2018-01-02 01:02:04',
            'owned_by' => 'cxxxxxxxxxxx',
            'created_by' => 'dxxxxxxxxxxx'
        );
        \Flexio\Tests\Check::assertInArray('B.1', 'Action::create(); make sure parameters can be set on creation',  $actual, $expected, $results);
    }
}
