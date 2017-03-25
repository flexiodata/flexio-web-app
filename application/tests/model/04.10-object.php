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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: Model:assoc_get(); tests for bad edge type input

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $get_result = $model->assoc_get($eid1, 'x', $eid2);
        $actual = $add_result === true && $get_result === array();
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Model::assoc_get(); return an empty array() when an invalid edge is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_UNDEFINED, $eid2);
        $actual = $add_result === true && $get_result === array();
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Model::assoc_get(); return an empty array() when an undefined edge is specified',  $actual, $expected, $results);



        // TEST: Model:assoc_get(); tests for bad eid input

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $get_result = $model->assoc_get('x', \Model::EDGE_LINKED_TO, $eid2);
        $actual = $add_result === true && $get_result === array();
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::assoc_get(); return an empty array() when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, 'x');
        $actual = $add_result === true && $get_result === array();
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Model::assoc_get(); return an empty array() when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Base\Eid::generate();
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $get_result = $model->assoc_get($eid3, \Model::EDGE_LINKED_TO, $eid2);
        $actual = $add_result === true && $get_result === array();
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Model::assoc_get(); return an empty array() when a non-existing eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Base\Eid::generate();
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $actual = $add_result === true && $get_result === array();
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Model::assoc_get(); return an empty array() when a non-existing eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Base\Eid::generate();
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, "$eid3,$eid3");
        $actual = $add_result === true && $get_result === array();
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Model::assoc_get(); return an empty array() when a non-existing eid is specified',  $actual, $expected, $results);



        // TEST: Model:assoc_get(); tests for valid eid input

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2)
        );
        TestCheck::assertInArray('C.1', '\Model::assoc_get(); return array() of target eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2)
        );
        TestCheck::assertInArray('C.2', '\Model::assoc_get(); return array() of target eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid3)
        );
        TestCheck::assertInArray('C.3', '\Model::assoc_get(); return array() of target eids that have the specified association',  $actual, $expected, $results);



        // TEST: Model:assoc_get(); tests to make sure function is sensitive to various types of deletion

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $delete_result = $model->assoc_delete($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $actual = $get_result;
        $expected = array(
        );
        TestCheck::assertInArray('D.1', '\Model::assoc_get(); return array() of eids that have the specified association; don\'t return deleted associations',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $delete_result = $model->delete($eid1);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $actual = $get_result;
        $expected = array(
        );
        TestCheck::assertInArray('D.2', '\Model::assoc_get(); return array() of eids that have the specified association; don\'t return associations for deleted objects',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $delete_result = $model->delete($eid2);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $actual = $get_result;
        $expected = array(
        );
        TestCheck::assertInArray('D.3', '\Model::assoc_get(); return array() of eids that have the specified association; don\'t return associations for deleted objects',  $actual, $expected, $results);



        // TEST: Model:assoc_get(); tests for delimited eid string for second parameter

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, "$eid2,$eid3");
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2),
            array('eid' => $eid3)
        );
        TestCheck::assertInArray('E.1', '\Model::assoc_get(); return array() of target eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, "$eid3,$eid2");
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2),
            array('eid' => $eid3)
        );
        TestCheck::assertInArray('E.2', '\Model::assoc_get(); in return array(), order eids in order they were created',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid4 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, "$eid2,$eid3,$eid4");
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2),
            array('eid' => $eid3)
        );
        TestCheck::assertInArray('E.3', '\Model::assoc_get(); when getting associated eids, only return eids that have that association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid4 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, "$eid4,$eid2,x,null,,,,$eid3");
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2),
            array('eid' => $eid3)
        );
        TestCheck::assertInArray('E.4', '\Model::assoc_get(); when getting associated eids, only return eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid4 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, ",,,,$eid2,,,,");
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2)
        );
        TestCheck::assertInArray('E.5', '\Model::assoc_get(); when getting associated eids, only return eids that have that association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid4 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, "$eid2,$eid2,$eid2");
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2)
        );
        TestCheck::assertInArray('E.6', '\Model::assoc_get(); when getting associated eids, only return a given eid once',  $actual, $expected, $results);



        // TEST: Model:assoc_get(); tests for array of eids for second parameter

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, array($eid2));
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2)
        );
        TestCheck::assertInArray('F.1', '\Model::assoc_get(); allow second parameter to be array of eids; return array() of target eids that have the specified association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_result1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_result2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $get_result = $model->assoc_get($eid1, \Model::EDGE_LINKED_TO, array($eid2,$eid3));
        $actual = $get_result;
        $expected = array(
            array('eid' => $eid2),
            array('eid' => $eid3)
        );
        TestCheck::assertInArray('F.2', '\Model::assoc_get(); allow second parameter to be array of eids; return array() of target eids that have the specified association',  $actual, $expected, $results);
    }
}
