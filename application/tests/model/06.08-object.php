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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: Model:assoc_delete(); tests for bad edge type input

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $delete_operation = \Flexio\Tests\Util::getModel()->assoc_delete($eid1, '', $eid2);
        $actual = $add_operation === true && $delete_operation === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Model::assoc_delete(); return false when an invalid edge is specified',  $actual, $expected, $results);



        // TODO: Model:assoc_delete(); tests for bad eid input

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $delete_operation = \Flexio\Tests\Util::getModel()->assoc_delete('x', \Model::EDGE_LINKED_TO, $eid2);
        $actual = $add_operation === true && $delete_operation === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::assoc_delete(); return false when an invalid eid is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Base\Eid::generate();
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $delete_operation = \Flexio\Tests\Util::getModel()->assoc_delete($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $actual = $add_operation === true && $delete_operation === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Model::assoc_delete(); return false when an invalid eid is specified',  $actual, $expected, $results);



        // TODO: Model:assoc_delete(); tests for deletion

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $delete_operation = \Flexio\Tests\Util::getModel()->assoc_delete($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $actual = $add_operation === true && $delete_operation === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Model::assoc_delete(); return true when a valid association is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count_after_addition = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $delete_operation = \Flexio\Tests\Util::getModel()->assoc_delete($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count_after_deletion = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $actual = $count_after_addition === 1 && $count_after_deletion === 0;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Model::assoc_delete(); make sure deletion removes association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $count_after_addition = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $delete_operation = \Flexio\Tests\Util::getModel()->assoc_delete($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count_after_deletion = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $actual = $count_after_addition === 2 && $count_after_deletion === 1;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Model::assoc_delete(); make sure deletion removes association',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $count_after_addition = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $delete_operation = \Flexio\Tests\Util::getModel()->assoc_delete($eid1, \Model::EDGE_LINKED_FROM, $eid2);
        $count_after_deletion = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $actual = $count_after_addition === 2 && $count_after_deletion === 2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', '\Model::assoc_delete(); make sure deletion is sensitive to the association type',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $count_after_addition = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $delete_operation = \Flexio\Tests\Util::getModel()->assoc_delete($eid2, \Model::EDGE_LINKED_TO, $eid3);
        $count_after_deletion = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $actual = $count_after_addition === 2 && $count_after_deletion === 2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.5', '\Model::assoc_delete(); make sure deletion is sensitive to the eid',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $add_operation = \Flexio\Tests\Util::getModel()->assoc_add($eid1, \Model::EDGE_LINKED_TO, $eid3);
        $count_of_all_items = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $delete_operation = \Flexio\Tests\Util::getModel()->assoc_delete($eid1, \Model::EDGE_LINKED_TO, $eid2);
        $count_after_deletion = \Flexio\Tests\Util::getModel()->assoc_count($eid1, \Model::EDGE_LINKED_TO);
        $actual = $count_of_all_items === 2 && $count_after_deletion === 1;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.6', '\Model::assoc_delete(); make sure deletion removes association',  $actual, $expected, $results);
    }
}
