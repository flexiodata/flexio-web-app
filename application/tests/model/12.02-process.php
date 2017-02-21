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


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: Model::create(); process creation with no parameters

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(Model::TYPE_PROCESS, $info);
        $actual = Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Model::create(); for process creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(Model::TYPE_PROCESS, $info);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Model::create(); for process creation, don\'t require input parameters; don\'t flag any errors',  $actual, $expected, $results);



        // TEST: Model::create(); process creation with basic parameters

        // TODO: add tests
    }
}
