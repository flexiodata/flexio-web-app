<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-14
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



        // TEST: \Model::getType(); tests for when object doesn't exist

        // BEGIN TEST
        $model->clearErrors();
        $type = $model->getType('x');
        $actual = $type;
        $expected = \Model::TYPE_UNDEFINED;
        TestCheck::assertString('A.1', '\Model::getType(); return undefined type if eid is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $exists = $model->getType('x');
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Model::getType(); don\'t flag an error if eid is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $eid = \Flexio\Base\Eid::generate();
        $type = $model->getType($eid);
        $actual = $type;
        $expected = \Model::TYPE_UNDEFINED;
        TestCheck::assertString('A.3', '\Model::getType(); return undefined type if eid doesn\'t exist',  $actual, $expected, $results);



        // TEST: \Model::getType(); tests for when object exists

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $type = $model->getType($eid);
        $actual = $type;
        $expected = \Model::TYPE_OBJECT;
        TestCheck::assertString('B.1', '\Model::getType(); return correct object type',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $type = $model->getType($eid);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Model::getType(); don\'t flag errors if when getting the type of a valid eid',  $actual, $expected, $results);



        // TEST: \Model::getStatus(); tests for when object doesn't exist

        // BEGIN TEST
        $model->clearErrors();
        $status = $model->getStatus('x');
        $actual = $status;
        $expected = \Model::STATUS_UNDEFINED;
        TestCheck::assertString('C.1', '\Model::getStatus(); return undefined type if eid is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $exists = $model->getStatus('x');
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('C.2', '\Model::getStatus(); don\'t flag an error if eid is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $eid = \Flexio\Base\Eid::generate();
        $status = $model->getStatus($eid);
        $actual = $status;
        $expected = \Model::STATUS_UNDEFINED;
        TestCheck::assertString('C.3', '\Model::getStatus(); return undefined type if eid doesn\'t exist',  $actual, $expected, $results);



        // TEST: \Model::getStatus(); tests for when object exists

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $status = $model->getStatus($eid);
        $actual = $status;
        $expected = \Model::STATUS_PENDING;
        TestCheck::assertString('D.1', '\Model::getStatus(); return correct object type',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $status = $model->getStatus($eid);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('D.2', '\Model::getStatus(); don\'t flag errors if when getting the type of a valid eid',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_AVAILABLE
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $status = $model->getStatus($eid);
        $actual = $status;
        $expected = \Model::STATUS_AVAILABLE;
        TestCheck::assertString('D.3', '\Model::getStatus(); return correct object type',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_DELETED
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $status = $model->getStatus($eid);
        $actual = $status;
        $expected = \Model::STATUS_DELETED;
        TestCheck::assertString('D.4', '\Model::getStatus(); return correct object type',  $actual, $expected, $results);



        // TEST: \Model::setStatus(); tests for when object doesn't exist

        // BEGIN TEST
        $model->clearErrors();
        $result = $model->setStatus('x', \Model::STATUS_AVAILABLE);
        $actual = $result;
        $expected = false;
        TestCheck::assertBoolean('E.1', '\Model::setStatus(); return false if eid is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $result = $model->setStatus('x', \Model::STATUS_AVAILABLE);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('E.2', '\Model::setStatus(); don\'t flag an error if eid is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $eid = \Flexio\Base\Eid::generate();
        $result = $model->setStatus($eid, \Model::STATUS_AVAILABLE);
        $actual = $result;
        $expected = false;
        TestCheck::assertBoolean('E.3', '\Model::setStatus(); return false if eid doesn\'t exist',  $actual, $expected, $results);



        // TEST: \Model::getStatus(); tests for when object exists

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $result = $model->setStatus($eid, \Model::STATUS_AVAILABLE);
        $actual = $result;
        $expected = true;
        TestCheck::assertBoolean('F.1', '\Model::setStatus(); return true if status is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $status = $model->getStatus($eid);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('F.2', '\Model::setStatus(); don\'t flag errors if when getting the type of a valid eid',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_PENDING
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $eid_status1 = $model->getStatus($eid);
        $result = $model->setStatus($eid, \Model::STATUS_AVAILABLE);
        $eid_status2 = $model->getStatus($eid);
        $actual = ($eid_status1 === \Model::STATUS_PENDING && $eid_status2 === \Model::STATUS_AVAILABLE);
        $expected = true;
        TestCheck::assertBoolean('F.3', '\Model::setStatus(); make sure status is set',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
            'eid_status' => \Model::STATUS_DELETED
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $eid_status1 = $model->getStatus($eid);
        $result = $model->setStatus($eid, \Model::STATUS_AVAILABLE);
        $eid_status2 = $model->getStatus($eid);
        $actual = ($eid_status1 === \Model::STATUS_DELETED && $eid_status2 === \Model::STATUS_AVAILABLE);
        $expected = true;
        TestCheck::assertBoolean('F.4', '\Model::setStatus(); make sure status is recoverable from \'deleted\'',  $actual, $expected, $results);
    }
}
