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


declare(strict_types=1);
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
            'user_eid' => \Flexio\Base\Eid::generate(),
            'subject_eid' => \Flexio\Base\Eid::generate(),
            'object_eid' => \Flexio\Base\Eid::generate(),
            'action' => 'create',
            'params' => json_encode(array("key" => "value")),
        );
        $actual = $model->action->record($action_params);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Action\Model::record(); basic action recording', $actual, $expected, $results);
    }
}
