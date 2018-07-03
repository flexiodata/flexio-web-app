<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-14
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
        $search_model = \Flexio\Tests\Util::getModel()->search;
        $pipe_model = \Flexio\Tests\Util::getModel()->pipe;
        $base_model = \Flexio\Tests\Util::getModel();


        // TEST: \Flexio\Model\Search::search(); search tests for multi-level eid/association combinations

        // BEGIN TEST
        $info = array(
        );
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $eid1 = $pipe_model->create($info);
        $eid2 = $pipe_model->create($info);
        $eid3 = $pipe_model->create($info);
        $eid4 = $pipe_model->create($info);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid2);
        $base_model->assoc_add($eid2, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid3, $edge_copied_to, $eid4);
        $path = "$eid1->($edge_copied_to)->$eid2->$edge_copied_to";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid3,
        );
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Model\Search::search(); search eids multiple levels deep',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $eid1 = $pipe_model->create($info);
        $eid2 = $pipe_model->create($info);
        $eid3 = $pipe_model->create($info);
        $eid4 = $pipe_model->create($info);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid2);
        $base_model->assoc_add($eid2, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid3, $edge_copied_to, $eid4);
        $path = "$eid1->($edge_copied_to)->$eid2->$edge_copied_to->$eid3";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid3,
        );
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Model\Search::search(); search eids multiple levels deep',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $edge_linked_to = \Model::EDGE_LINKED_TO;
        $eid1 = $pipe_model->create($info);
        $eid2 = $pipe_model->create($info);
        $eid3 = $pipe_model->create($info);
        $eid4 = $pipe_model->create($info);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid2);
        $base_model->assoc_add($eid2, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid3, $edge_copied_to, $eid4);
        $base_model->assoc_add($eid1, $edge_linked_to, $eid2);
        $base_model->assoc_add($eid2, $edge_linked_to, $eid3);
        $base_model->assoc_add($eid3, $edge_linked_to, $eid4);
        $path = "$eid1->($edge_linked_to)->$eid2->$edge_copied_to->$eid3";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid3,
        );
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Model\Search::search(); search eids multiple levels deep',  $actual, $expected, $results);
    }
}
