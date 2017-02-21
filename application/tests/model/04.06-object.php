<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-18
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



        // TEST: Model:assoc_add(); tests for bad edge type input

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, '', $eid2);
        $actual = $association;
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Model::assoc_add(); return false when an invalid edge is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, 'x', $eid2);
        $actual = $association;
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Model::assoc_add(); return false when an invalid edge is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, 'x', $eid2);
        $has_errors = $model->hasErrors();
        $actual = $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.3', 'Model::assoc_add(); flag an error when an invalid edge is specified',  $actual, $expected, $results);



        // TODO: Model:assoc_add(); tests for bad eid inputs

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add('x', Model::EDGE_LINKED_TO, $eid2);
        $actual = $association;
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Model::assoc_add(); return false when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, 'x');
        $actual = $association;
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Model::assoc_add(); return false when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add('x', Model::EDGE_LINKED_TO, $eid2);
        $has_errors = $model->hasErrors();
        $actual = $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Model::assoc_add(); don\'t flag an error when an invalid eid is specified; simply return false',  $actual, $expected, $results);



        // TEST: Model:assoc_add(); tests for valid edge type input

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $actual = $association;
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Model::assoc_add(); return true when a valid association is created',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $has_errors = $model->hasErrors();
        $actual = $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Model::assoc_add(); make sure not to flag an error when a valid association is created',  $actual, $expected, $results);



        // TEST: Model:assoc_add(); tests for behavior when multiple edges are created on the same eid paris

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association1 = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association2 = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $actual = $association1 === true && $association2 === false;
        $expected = true;
        TestCheck::assertBoolean('D.1', 'Model::assoc_add(); two objects shouldn\'t allow the same association to be created twice on the same two objects in the same direction; return false when second association is created',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association1 = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association2 = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $has_errors = $model->hasErrors();
        $actual = $has_errors;
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Model::assoc_add(); two objects shouldn\'t allow the same association to be created twice on the same two objects in the same direction; however, don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association1 = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association2 = $model->assoc_add($eid2, Model::EDGE_LINKED_TO, $eid1);
        $actual = $association1 === true && $association2 === true;
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Model::assoc_add(); two objects should allow the same association to be used bidirectionally; return true when both associations are created',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association1 = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association2 = $model->assoc_add($eid2, Model::EDGE_LINKED_TO, $eid1);
        $has_errors = $model->hasErrors();
        $actual = $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('D.4', 'Model::assoc_add(); make sure not to flag an error when two objects use the same association bidirectionally',  $actual, $expected, $results);

        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association1 = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association2 = $model->assoc_add($eid1, Model::EDGE_LINKED_FROM, $eid2);
        $actual = $association1 === true && $association2 === true;
        $expected = true;
        TestCheck::assertBoolean('D.5', 'Model::assoc_add(); two objects should allow associations of different types; return true when both associations are created',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(Model::TYPE_OBJECT, $info);
        $association1 = $model->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association2 = $model->assoc_add($eid1, Model::EDGE_LINKED_FROM, $eid2);
        $has_errors = $model->hasErrors();
        $actual = $has_errors === false;
        $expected = true;
        TestCheck::assertBoolean('D.6', 'Model::assoc_add(); make sure not to flag an error when a two different associations are created on the same two objects',  $actual, $expected, $results);
    }
}
