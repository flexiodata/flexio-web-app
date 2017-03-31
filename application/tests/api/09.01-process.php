<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-05-09
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add tests

        // TEST: object creation

        // BEGIN TEST
        $params = array();
        $params['run'] = false;
        $params['task'] = json_decode('[
            {
                "type": "flexio.sleep",
                "params": {
                    "value": 4000
                }
            }
        ]',true);
        $user_eid = TestUtil::getDefaultTestUser();
        $request = \Flexio\Api\Request::create()->setRequestingUser($user_eid);
        $process_info = \Flexio\Api\Process::create($params, $request);
        $actual = $process_info['task'][0]['type'];
        $expected = 'flexio.sleep';
        TestCheck::assertString('A.1', '\Flexio\Api\Process::create(); return the object',  $actual, $expected, $results);


        // TEST: process background job processing and status

        // BEGIN TEST
        $params = array();
        $params['run'] = true;
        $params['background'] = true;
        $params['task'] = json_decode('[
            {
                "type": "flexio.sleep",
                "params": {
                    "value": 4000
                }
            }
        ]',true);
        $user_eid = TestUtil::getDefaultTestUser();
        $request = \Flexio\Api\Request::create()->setRequestingUser($user_eid);
        $process_info1 = \Flexio\Api\Process::create($params, $request);
        sleep(2);
        $process_info2 = \Flexio\Api\Process::get($process_info1, $request);
        $status2 = $process_info2['process_status'];
        sleep(4);
        $process_info3 = \Flexio\Api\Process::get($process_info1, $request);
        $status3 = $process_info3['process_status'];
        $actual = ($status2 === \Model::PROCESS_STATUS_RUNNING && $status3 === \Model::PROCESS_STATUS_COMPLETED);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\Api\Process::create(); make sure a process runs in the background and the appropriate process status codes are set',  $actual, $expected, $results);
    }
}
