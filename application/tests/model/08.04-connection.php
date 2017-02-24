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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::create(); connection creation with no parameters

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $actual = \Flexio\System\Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::create(); for connection creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_CONNECTION, $info);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Model::create(); for connection creation, don\'t require input parameters; don\'t flag any errors',  $actual, $expected, $results);
    }
}
