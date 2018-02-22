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
        // TEST: \Model::create(); action creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = \Flexio\Tests\Util::getModel()->action->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Action::create(); for action creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);



        // TEST: Action::create(); action creation with basic input

        // BEGIN TEST
        $info = array(
            'invoked_from' => 'API',
            'invoked_by' => 'xxxxxxxxxxxx',
            'action_type' => 'action.test',
            'action_info' => '{"url": "https://www.flex.io/api/v1/test"}',
            'action_target' => 'zzzzzzzzzzzz',
            'result_type' => 'result.type',
            'result_info' => '{}',
            'started' => '2018-01-02 01:02:03',
            'finished' => '2018-01-02 01:02:04'
        );
        $eid = \Flexio\Tests\Util::getModel()->action->create($info);
        $actual = \Flexio\Tests\Util::getModel()->action->get($eid);
        $expected = array(
            'invoked_from' => 'API',
            'invoked_by' => 'xxxxxxxxxxxx',
            'action_type' => 'action.test',
            'action_info' => '{"url": "https://www.flex.io/api/v1/test"}',
            'action_target' => 'zzzzzzzzzzzz',
            'result_type' => 'result.type',
            'result_info' => '{}',
            'started' => '2018-01-02 01:02:03',
            'finished' => '2018-01-02 01:02:04'
        );
        \Flexio\Tests\Check::assertInArray('B.1', 'Action::create(); make sure parameters can be set on creation',  $actual, $expected, $results);
    }
}
