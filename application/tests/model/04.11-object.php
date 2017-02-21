<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-19
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



        // TEST: Model:assoc_range(); tests for bad edge type input

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range($eid1, 'x');
        $actual = $add_result === true && $range_result === false;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::assoc_range(); return false when an invalid edge is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range($eid1, 'x');
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Model::assoc_range(); flag an error when an undefined edge is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_UNDEFINED);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Model::assoc_range(); flag an error when an undefined edge is specified',  $actual, $expected, $results);



        // TEST: Model:assoc_range(); tests for bad eid input

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range('x', \Model::EDGE_LINKED_TO);
        $actual = $add_result === true && $range_result === array();
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::assoc_range(); return empty array() when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range('x', \Model::EDGE_LINKED_TO);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Model::assoc_range(); don\'t flag an error when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Eid::generate();
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range($eid3, \Model::EDGE_LINKED_TO);
        $actual = $add_result === true && $range_result === array();
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Model::assoc_range(); return empty array() when a non-existing eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Eid::generate();
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range($eid3, \Model::EDGE_LINKED_TO);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = false;
        TestCheck::assertBoolean('B.4', '\Model::assoc_range(); don\'t flag an error when a non-existing eid is specified',  $actual, $expected, $results);



        // TEST: Model:assoc_range(); tests for valid eid input

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2)
        );
        TestCheck::assertInArray('C.1', '\Model::assoc_range(); return array() of eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2),
            array('eid' => $eid3)
        );
        TestCheck::assertInArray('C.2', '\Model::assoc_range(); return array() of target eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_FROM, $eid3);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_FROM);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid3)
        );
        TestCheck::assertInArray('C.3', '\Model::assoc_range(); return array() of target eids that have the specified association; make sure function is sensitive to association type',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid2, \Model::EDGE_LINKED_TO, $eid1);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2)
        );
        TestCheck::assertInArray('C.4', '\Model::assoc_range(); return array() of target eids that have the specified association; make sure function is sensitive to input eid in bidirectional relationships',  $actual, $expected, $results);



        // TEST: Model:assoc_range(); tests to make sure function is sensitive to various types of deletion

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $delete_result = $model->assoc_delete($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid3, 'eid_status' => \Model::STATUS_AVAILABLE)
        );
        TestCheck::assertInArray('D.1', '\Model::assoc_range(); return array() of eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $delete_result = $model->delete($eid3);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2, 'eid_type' => 'OBJ', 'eid_status' => \Model::STATUS_AVAILABLE),
            array('eid' => $eid3, 'eid_type' => 'OBJ', 'eid_status' => \Model::STATUS_DELETED)
        );
        \Util::sortByFieldAsc($actual, 'eid');
        \Util::sortByFieldAsc($expected, 'eid');
        TestCheck::assertArray('D.2', '\Model::assoc_range(); return array() of eids that have the specified association; deleted objects are included in associations',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $delete_result = $model->delete($eid1);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2, 'eid_type' => 'OBJ', 'eid_status' => \Model::STATUS_AVAILABLE),
            array('eid' => $eid3, 'eid_type' => 'OBJ', 'eid_status' => \Model::STATUS_AVAILABLE)
        );
        \Util::sortByFieldAsc($actual, 'eid');
        \Util::sortByFieldAsc($expected, 'eid');
        TestCheck::assertArray('D.3', '\Model::assoc_range(); return array() of eids that have the specified association; deleted objects are included in associations',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $delete_result = $model->assoc_delete($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid3, 'eid_type' => 'OBJ', 'eid_status' => \Model::STATUS_AVAILABLE)
        );
        TestCheck::assertArray('D.4', '\Model::assoc_range(); return array() of eids that have the specified association',  $actual, $expected, $results);



        // TEST: Model:assoc_range(); tests for extra object info

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2),
            array('eid' => $eid3)
        );
        TestCheck::assertInArray('E.1', '\Model::assoc_range(); return array() of eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2, 'eid_type' => \Model::TYPE_OBJECT),
            array('eid' => $eid3, 'eid_type' => \Model::TYPE_OBJECT)
        );
        TestCheck::assertInArray('E.2', '\Model::assoc_range(); return array() of eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2, 'eid_status' => \Model::STATUS_AVAILABLE),
            array('eid' => $eid3, 'eid_status' => \Model::STATUS_AVAILABLE)
        );
        TestCheck::assertInArray('E.3', '\Model::assoc_range(); return array() of eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2, 'eid_type' => \Model::TYPE_OBJECT, 'eid_status' => \Model::STATUS_AVAILABLE),
            array('eid' => $eid3, 'eid_type' => \Model::TYPE_OBJECT, 'eid_status' => \Model::STATUS_AVAILABLE)
        );
        TestCheck::assertInArray('E.4', '\Model::assoc_range(); return array() of eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2, 'eid_status' => \Model::STATUS_AVAILABLE),
            array('eid' => $eid3, 'eid_status' => \Model::STATUS_AVAILABLE)
        );
        TestCheck::assertInArray('E.5', '\Model::assoc_range(); return array() of eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $range_result = $model->assoc_range($eid1, \Model::EDGE_LINKED_TO);
        $actual = $range_result;
        $expected = array(
            array('eid' => $eid2, 'eid_status' => \Model::STATUS_AVAILABLE),
            array('eid' => $eid3, 'eid_status' => \Model::STATUS_AVAILABLE)
        );
        TestCheck::assertInArray('E.6', '\Model::assoc_range(); return array() of eids that have the specified association',  $actual, $expected, $results);
    }
}
