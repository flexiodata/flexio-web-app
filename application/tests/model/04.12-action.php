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


class Test
{
    public function run(&$results)
    {
        // TODO: add more tests

        // TEST: ActionModel::record(); basic action recording

        // BEGIN TEST
        $action_params = array(
            'user_eid' => Eid::generate(),
            'request_method' => 'GET',
            'url_params' => json_encode(array('eid' => Eid::generate())),
            'query_params' => json_encode(array())
        );
        $actual = System::getModel()->action->record($action_params);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'ActionModel::record(); basic action recording', $actual, $expected, $results);
    }
}
