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


class Test
{
    public function run(&$results)
    {
        // TEST: Model::create(); comment creation with no parameters

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $actual = Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Model::create(); for comment creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $has_errors = System::getModel()->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Model::create(); for comment creation, don\'t require input parameters; don\'t flag any errors',  $actual, $expected, $results);



        // TEST: Model::create(); comment creation with basic parameter input

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => 'This is a test comment'
        );
        $eid = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $actual = Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Model::create(); make sure valid eid is returned when comment is created',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'comment' => $handle
        );
        $eid_first_time_creation = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $eid_second_time_creation = System::getModel()->create(Model::TYPE_COMMENT, $info);
        $actual = (Eid::isValid($eid_first_time_creation) && Eid::isValid($eid_second_time_creation));
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Model::create(); allow multiple comments with the same value',  $actual, $expected, $results);
    }
}
