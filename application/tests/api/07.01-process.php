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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
/*
// TODO: old tests; convert over to new

        // TODO: add tests

        // TEST: object creation

        // BEGIN TEST
        $params = array();
        $params['run'] = false;
        $params['task'] = json_decode('[
            {
                "op": "sleep",
                "params": {
                    "value": 4000
                }
            }
        ]',true);
        $request = \Flexio\Api1\Request::create();
        $request->setPostParams($params);
        $request->setRequestingUser(\Flexio\Tests\Util::getDefaultTestUser());
        $process_info = \Flexio\Api1\Process::create($request);
        $actual = $process_info['task'][0]['op'];
        $expected = 'sleep';
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Api1\Process::create(); return the object',  $actual, $expected, $results);


        // TEST: process background job processing and status

        // BEGIN TEST
        $params = array();
        $params['run'] = true;
        $params['background'] = true;
        $params['task'] = json_decode('[
            {
                "op": "sleep",
                "params": {
                    "value": 4000
                }
            }
        ]',true);
        $user_eid = \Flexio\Tests\Util::getDefaultTestUser();
        $request_create = \Flexio\Api1\Request::create();
        $request_create->setPostParams($params);
        $request_create->setRequestingUser($user_eid);
        $process_info1 = \Flexio\Api1\Process::create($request_create);
        $process = \Flexio\Object\Process::load($process_info1['eid']);
        $process->grant($user_eid, \Model::ACCESS_CODE_TYPE_EID, array(
            \Flexio\Object\Right::TYPE_READ,
            \Flexio\Object\Right::TYPE_WRITE,
            \Flexio\Object\Right::TYPE_DELETE,
            \Flexio\Object\Right::TYPE_EXECUTE
        ));
        sleep(2);
        $request_get = \Flexio\Api1\Request::create();
        $request_get->setQueryParams($process_info1);
        $request_get->setRequestingUser(\Flexio\Tests\Util::getDefaultTestUser());
        $process_info2 = \Flexio\Api1\Process::get($request_get);
        $status2 = $process_info2['process_status'];
        sleep(4);
        $process_info3 = \Flexio\Api1\Process::get($request_get);
        $status3 = $process_info3['process_status'];
        $actual = ($status2 === \Flexio\Jobs\Process::STATUS_RUNNING && $status3 === \Flexio\Jobs\Process::STATUS_COMPLETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Api1\Process::create(); make sure a process runs in the background and the appropriate process status codes are set',  $actual, $expected, $results);
*/
    }
}
