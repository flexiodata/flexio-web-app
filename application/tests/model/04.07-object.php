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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: Model:assoc_count(); tests for bad edge type input

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count = $model->assoc_count($eid1, '');
        $actual = $association === true && $count === 0;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::assoc_count(); return zero when an invalid edge is specified',  $actual, $expected, $results);



        // TODO: Model:assoc_count(); tests for bad eid input

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count = $model->assoc_count('', \Model::EDGE_LINKED_TO);
        $actual = 0;
        $expected = 0;
        TestCheck::assertNumber('B.1', '\Model::assoc_count(); return zero when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Base\Eid::generate();
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count = $model->assoc_count($eid3, \Model::EDGE_LINKED_TO);
        $actual = 0;
        $expected = 0;
        TestCheck::assertNumber('B.2', '\Model::assoc_count(); return zero when an invalid eid is specified',  $actual, $expected, $results);



        // TODO: Model:assoc_count(); tests for correct association count

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $actual = $count;
        $expected = 1;
        TestCheck::assertNumber('C.1', '\Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $association = $model->assoc_add($eid2, \Model::EDGE_LINKED_TO, $eid1);
        $count = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $actual = $count;
        $expected = 1;
        TestCheck::assertNumber('C.2', '\Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_FROM, $eid2);
        $count = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $actual = $count;
        $expected = 1;
        TestCheck::assertNumber('C.3', '\Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid4 = $model->create(\Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid4);
        $count = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $actual = $count;
        $expected = 3;
        TestCheck::assertNumber('C.4', '\Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid4 = $model->create(\Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid4);
        $association = $model->delete($eid3);
        $association = $model->delete($eid4);
        $count_all = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $count_all_with_filter = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO, [\Model::STATUS_AVAILABLE, \Model::STATUS_DELETED]);
        $count_available = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO, [\Model::STATUS_AVAILABLE]);
        $count_deleted = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO, [\Model::STATUS_DELETED]);
        $actual = $count_all === 3 && $count_all_with_filter ===3 && $count_available === 1 && $count_deleted === 2;
        $expected = true;
        TestCheck::assertBoolean('C.5', '\Model::assoc_count(); return the number of associations of a given type for a particular eid',  $actual, $expected, $results);
    }
}
