<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-23
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



        // TEST: \Model::create(); pipe creation with no parameters

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\System\Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::create(); for pipe creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Model::create(); for pipe creation, don\'t require input parameters; don\'t flag any errors',  $actual, $expected, $results);



        // TEST: \Model::create(); pipe creation with basic name input

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\System\Util::generateHandle();
        $info = array(
            'name' => 'This is a test pipe'
        );
        $eid = $model->create(\Model::TYPE_PIPE, $info);
        $actual = \Flexio\System\Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::create(); make sure valid eid is returned when pipe is created',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\System\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid_first_time_creation = $model->create(\Model::TYPE_PIPE, $info);
        $eid_second_time_creation = $model->create(\Model::TYPE_PIPE, $info);
        $actual = (\Flexio\System\Eid::isValid($eid_first_time_creation) && \Flexio\System\Eid::isValid($eid_second_time_creation));
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Model::create(); allow multiple pipes with the same value',  $actual, $expected, $results);
    }
}
