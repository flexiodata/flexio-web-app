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



        // TEST: Model:assoc_add(); tests for bad edge type input

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
            $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
            $association = $model->assoc_add($eid1, '', $eid2);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        TestCheck::assertInArray('A.1', '\Model::assoc_add(); throw an exception when an invalid edge is specified',  $actual, $expected, $results);


        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
            $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
            $association = $model->assoc_add($eid1, 'x', $eid2);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        TestCheck::assertInArray('A.2', '\Model::assoc_add(); throw an exception when an invalid edge is specified',  $actual, $expected, $results);



        // TODO: Model:assoc_add(); tests for bad eid inputs

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
            $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
            $association = $model->assoc_add('x', \Model::EDGE_LINKED_TO, $eid2);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        TestCheck::assertInArray('B.1', '\Model::assoc_add(); throw an exception when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
            $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
            $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, 'x');
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        TestCheck::assertInArray('B.2', '\Model::assoc_add(); throw an exception when an invalid eid is specified',  $actual, $expected, $results);



        // TEST: Model:assoc_add(); tests for valid edge type input

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $association = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $actual = $association;
        $expected = true;
        TestCheck::assertBoolean('C.1', '\Model::assoc_add(); return true when a valid association is created',  $actual, $expected, $results);



        // TEST: Model:assoc_add(); tests for behavior when multiple edges are created on the same eid paris

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $association1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $association2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $assoc_count = count($model->assoc_get($eid1, \Model::EDGE_LINKED_TO, [$eid2]));
        $actual = $association1 === true && $association2 === true && $assoc_count === 1;
        $expected = true;
        TestCheck::assertBoolean('D.1', '\Model::assoc_add(); if the same association is created twice, don\'t create it twice but succeed',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $association1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $association2 = $model->assoc_add($eid2, \Model::EDGE_LINKED_TO, $eid1);
        $actual = $association1 === true && $association2 === true;
        $expected = true;
        TestCheck::assertBoolean('D.2', '\Model::assoc_add(); two objects should allow the same association to be used bidirectionally; return true when both associations are created',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $association1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $association2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_FROM, $eid2);
        $actual = $association1 === true && $association2 === true;
        $expected = true;
        TestCheck::assertBoolean('D.3', '\Model::assoc_add(); two objects should allow associations of different types; return true when both associations are created',  $actual, $expected, $results);
    }
}
