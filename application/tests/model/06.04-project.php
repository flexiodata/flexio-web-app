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
        // TEST: Model::create(); project creation with no parameters

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $actual = Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Model::create(); for project creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_PROJECT, $info);
        $has_errors = System::getModel()->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Model::create(); for project creation, don\'t require input parameters; don\'t flag any errors',  $actual, $expected, $results);
    }
}
