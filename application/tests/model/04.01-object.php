<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
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



        // TEST: \Model::create(); invalid type

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create('', $info);
        $actual = $eid;
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Model::create(); invalid type should return false',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create('', $info);
        $actual = $model->getErrors();
        $expected = array(array(
            'code' => \Model::ERROR_INVALID_PARAMETER,
            'message' => 'Invalid parameter'
        ));
        TestCheck::assertInArray('A.2', '\Model::create(); invalid type should set an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_UNDEFINED, $info);
        $actual = $eid;
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::create(); undefined type should return false',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_UNDEFINED, $info);
        $actual = $model->getErrors();
        $expected = array(array(
            'code' => \Model::ERROR_INVALID_PARAMETER,
            'message' => 'Invalid parameter'
        ));
        TestCheck::assertInArray('A.4', '\Model::create(); undefined type should set an error',  $actual, $expected, $results);



        // TEST: \Model::create(); valid type

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::create(); for object creation, don\'t require input parameters; return valid eid on success',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Model::create(); for object creation, don\'t require input parameters; don\'t flag any errors',  $actual, $expected, $results);
    }
}
