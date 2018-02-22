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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = \Flexio\Tests\Util::getModel();



        // TEST: Model:assoc_change_type(); tests for bad edge type input

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
            $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
            $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
            $change_operation = $model->assoc_change_type($eid1, \Model::EDGE_LINKED_TO, $eid2, '');
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        \Flexio\Tests\Check::assertInArray('A.1', '\Model::assoc_change_type(); throw an exception when an invalid new edge is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
            $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
            $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
            $change_operation = $model->assoc_change_type($eid1, '', $eid2, \Model::EDGE_LINKED_FROM);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        \Flexio\Tests\Check::assertInArray('A.2', '\Model::assoc_change_type(); throw an exception when an invalid identifying edge is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $change_operation = $model->assoc_change_type($eid1, \Model::EDGE_LINKED_FROM, $eid2, \Model::EDGE_LINKED_TO);
        $actual = $add_operation === true && $change_operation === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Model::assoc_change_type(); return false when an identifying edge that doesn\'t exist is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $change_operation = $model->assoc_change_type($eid1, \Model::EDGE_LINKED_TO, $eid2, \Model::EDGE_LINKED_TO);
        $actual = $add_operation === true && $change_operation === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Model::assoc_change_type(); return true when an identifying edge is the same as the same type as the new edge even though no change is made',  $actual, $expected, $results);



        // TODO: Model:assoc_change_type(); tests for bad eid input

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $change_operation = $model->assoc_change_type('x', \Model::EDGE_LINKED_TO, $eid2, \Model::EDGE_LINKED_FROM);
        $actual = $add_operation === true && $change_operation === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::assoc_change_type(); return false when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $change_operation = $model->assoc_change_type($eid1, \Model::EDGE_LINKED_TO, 'x', \Model::EDGE_LINKED_FROM);
        $actual = $add_operation === true && $change_operation === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Model::assoc_change_type(); return false when an invalid eid is specified',  $actual, $expected, $results);



        // TODO: Model:assoc_change_type(); tests for change

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $change_operation = $model->assoc_change_type($eid1, \Model::EDGE_LINKED_TO, $eid2, \Model::EDGE_LINKED_FROM);
        $actual = $add_operation === true && $change_operation === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Model::assoc_change_type(); return true when an association is changed',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count_after_addition = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $change_operation = $model->assoc_change_type($eid1, \Model::EDGE_LINKED_TO, $eid2, \Model::EDGE_LINKED_FROM);
        $count_original_after_change = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $count_new_after_change = $model->assoc_count($eid1, \Model::EDGE_LINKED_FROM);
        $actual = $count_after_addition === 1 && $count_original_after_change === 0 && $count_new_after_change === 1;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Model::assoc_change_type(); make sure the association is changed',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_operation1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_operation2 = $model->assoc_add($eid1, \Model::EDGE_LINKED_FROM, $eid2);
        $change_operation = $model->assoc_change_type($eid1, \Model::EDGE_LINKED_TO, $eid2, \Model::EDGE_LINKED_FROM);
        $assoc_count1 = count($model->assoc_get($eid1, \Model::EDGE_LINKED_TO, [$eid2]));
        $assoc_count2 = count($model->assoc_get($eid1, \Model::EDGE_LINKED_FROM, [$eid2]));
        $actual = $add_operation1 === true && $add_operation2 === true && $change_operation === true && $assoc_count1 === 0 && $assoc_count2 === 1;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Model::assoc_change_type(); return true when trying to change to an association that already exists, but make sure to not add a new association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Base\Eid::generate();
        $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count_after_addition = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $change_operation = $model->assoc_change_type($eid3, \Model::EDGE_LINKED_TO, $eid2, \Model::EDGE_LINKED_FROM);
        $count_original_after_change = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $count_new_after_change = $model->assoc_count($eid1, \Model::EDGE_LINKED_FROM);
        $actual = $count_after_addition === 1 && $count_original_after_change === 1 && $count_new_after_change === 0;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Model::assoc_change_type(); make sure the association is sensitive to eid',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Base\Eid::generate();
        $add_operation = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count_after_addition = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $change_operation = $model->assoc_change_type($eid1, \Model::EDGE_LINKED_TO, $eid3, \Model::EDGE_LINKED_FROM);
        $count_original_after_change = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $count_new_after_change = $model->assoc_count($eid1, \Model::EDGE_LINKED_FROM);
        $actual = $count_after_addition === 1 && $count_original_after_change === 1 && $count_new_after_change === 0;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.5', '\Model::assoc_change_type(); make sure the association is sensitive to eid',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $add_operation1 = $model->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_operation2 = $model->assoc_add($eid1, \Model::EDGE_HAS_MEMBER, $eid2);
        $count_association1 = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $count_association2 = $model->assoc_count($eid1, \Model::EDGE_HAS_MEMBER);
        $change_operation = $model->assoc_change_type($eid1, \Model::EDGE_LINKED_TO, $eid2, \Model::EDGE_LINKED_FROM);
        $count_original_after_change = $model->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $count_new_after_change = $model->assoc_count($eid1, \Model::EDGE_LINKED_FROM);
        $actual = $count_association1 === 1 && $count_association2 === 1 && $count_original_after_change === 0 && $count_new_after_change === 1;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.6', '\Model::assoc_change_type(); make sure the association is sensitive to the association type',  $actual, $expected, $results);
    }
}
