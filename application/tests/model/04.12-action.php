<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-21
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TODO: add more tests

        // TEST: Action\Model::record(); basic action recording

        // BEGIN TEST
        $action_params = array(
            'user_eid' => \Flexio\System\Eid::generate(),
            'request_method' => 'GET',
            'url_params' => json_encode(array('eid' => \Flexio\System\Eid::generate())),
            'query_params' => json_encode(array())
        );
        $actual = $model->action->record($action_params);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Action\Model::record(); basic action recording', $actual, $expected, $results);
    }
}
