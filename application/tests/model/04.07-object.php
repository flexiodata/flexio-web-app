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
        // TEST: Model:assoc_count(); tests for bad edge type input

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $count = System::getModel()->assoc_count($eid1, '');
        $actual = $association === true && $count === false;
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Model::assoc_count(); return false when an invalid edge is specified',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $count = System::getModel()->assoc_count($eid1, '');
        $has_errors = System::getModel()->hasErrors();
        $actual = $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Model::assoc_count(); flag an error when an invalid edge is specified',  $actual, $expected, $results);



        // TODO: Model:assoc_count(); tests for bad eid input

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $count = System::getModel()->assoc_count('', Model::EDGE_LINKED_TO);
        $actual = 0;
        $expected = 0;
        TestCheck::assertNumber('B.1', 'Model::assoc_count(); return zero when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid3 = Eid::generate();
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $count = System::getModel()->assoc_count($eid3, Model::EDGE_LINKED_TO);
        $actual = 0;
        $expected = 0;
        TestCheck::assertNumber('B.2', 'Model::assoc_count(); return zero when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $count = System::getModel()->assoc_count('', Model::EDGE_LINKED_TO);
        $actual = System::getModel()->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.3', 'Model::assoc_count(); don\'t flag an error when an invalid eid is specified',  $actual, $expected, $results);



        // TODO: Model:assoc_count(); tests for correct association count

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $count = System::getModel()->assoc_count($eid1, Model::EDGE_LINKED_TO);
        $actual = $count;
        $expected = 1;
        TestCheck::assertNumber('C.1', 'Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association = System::getModel()->assoc_add($eid2, Model::EDGE_LINKED_TO, $eid1);
        $count = System::getModel()->assoc_count($eid1, Model::EDGE_LINKED_TO);
        $actual = $count;
        $expected = 1;
        TestCheck::assertNumber('C.2', 'Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_FROM, $eid2);
        $count = System::getModel()->assoc_count($eid1, Model::EDGE_LINKED_TO);
        $actual = $count;
        $expected = 1;
        TestCheck::assertNumber('C.3', 'Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_FROM, 'x'); // shouldn't be created, so only 1 valid association
        $count = System::getModel()->assoc_count($eid1, Model::EDGE_LINKED_TO);
        $actual = $count;
        $expected = 1;
        TestCheck::assertNumber('C.4', 'Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid3 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid4 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid3);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid4);
        $count = System::getModel()->assoc_count($eid1, Model::EDGE_LINKED_TO);
        $actual = $count;
        $expected = 3;
        TestCheck::assertNumber('C.5', 'Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid1 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid2 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid3 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $eid4 = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid2);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid3);
        $association = System::getModel()->assoc_add($eid1, Model::EDGE_LINKED_TO, $eid4);
        $association = System::getModel()->delete($eid3);
        $association = System::getModel()->delete($eid4);
        $count_all = System::getModel()->assoc_count($eid1, Model::EDGE_LINKED_TO);
        $count_all_with_filter = System::getModel()->assoc_count($eid1, Model::EDGE_LINKED_TO, [Model::STATUS_AVAILABLE, Model::STATUS_DELETED]);
        $count_available = System::getModel()->assoc_count($eid1, Model::EDGE_LINKED_TO, [Model::STATUS_AVAILABLE]);
        $count_deleted = System::getModel()->assoc_count($eid1, Model::EDGE_LINKED_TO, [Model::STATUS_DELETED]);
        $actual = $count_all === 3 && $count_all_with_filter ===3 && $count_available === 1 && $count_deleted === 2;
        $expected = true;
        TestCheck::assertBoolean('C.6', 'Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);
    }
}
