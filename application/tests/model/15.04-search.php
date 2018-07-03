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


        // TEST: \Flexio\Model\Search::search(); search tests for basic paths

        // BEGIN TEST
        $info = array(
        );
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $eid1 = $pipe_model->create($info);
        $eid2 = $pipe_model->create($info);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid2);
        $path = "$eid1->$edge_copied_to";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid2
        );
        sort($actual);
        sort($expected);
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Model\Search::search(); search for eids specified by basic path',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $eid1 = $pipe_model->create($info);
        $eid2 = $pipe_model->create($info);
        $eid3 = $pipe_model->create($info);
        $eid4 = $pipe_model->create($info);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid2);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid4);
        $path = "$eid1->$edge_copied_to";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid2,
            $eid3,
            $eid4
        );
        sort($actual);
        sort($expected);
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Model\Search::search(); search for eids specified by basic path',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $eid1 = $pipe_model->create($info);
        $eid2 = $pipe_model->create($info);
        $eid3 = $pipe_model->create($info);
        $eid4 = $pipe_model->create($info);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid2);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid4);
        $path = "$eid1->($edge_copied_to)->$eid3";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid3
        );
        sort($actual);
        sort($expected);
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Model\Search::search(); search for eids specified by basic path',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $eid1 = $pipe_model->create($info);
        $eid2 = $pipe_model->create($info);
        $eid3 = $pipe_model->create($info);
        $eid4 = $pipe_model->create($info);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid2);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid4);
        $path = "$eid1->($edge_copied_to)->($eid3,$eid4)";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid3,
            $eid4
        );
        sort($actual);
        sort($expected);
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Model\Search::search(); search for eids specified by basic path',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $eid1 = $pipe_model->create($info);
        $eid2 = $pipe_model->create($info);
        $eid3 = $pipe_model->create($info);
        $eid4 = $pipe_model->create($info);
        $eid5 = $pipe_model->create($info);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid2);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid4);
        $path = "$eid1->($edge_copied_to)->($eid4,$eid5)";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid4
        );
        sort($actual);
        sort($expected);
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Model\Search::search(); search for eids specified by basic path',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $eid1 = $pipe_model->create($info);
        $eid2 = $pipe_model->create($info);
        $eid3 = $pipe_model->create($info);
        $eid4 = $pipe_model->create($info);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid2);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid4);
        $path = "$eid1->()->$eid3";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        sort($actual);
        sort($expected);
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Model\Search::search(); search for eids specified by basic path',  $actual, $expected, $results);

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
        $base_model->assoc_add($eid1, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid4);
        $path = "$eid1->$edge_linked_to->$eid3";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        sort($actual);
        sort($expected);
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Model\Search::search(); search for eids specified by basic path',  $actual, $expected, $results);

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
        $base_model->assoc_add($eid1, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid4);
        $path = "$eid1->($edge_linked_to,$edge_copied_to)";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid2,
            $eid3,
            $eid4
        );
        sort($actual);
        sort($expected);
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Model\Search::search(); search for eids specified by basic path',  $actual, $expected, $results);

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
        $base_model->assoc_add($eid1, $edge_copied_to, $eid3);
        $base_model->assoc_add($eid1, $edge_copied_to, $eid4);
        $base_model->assoc_add($eid1, $edge_linked_to, $eid2);
        $base_model->assoc_add($eid1, $edge_linked_to, $eid3);
        $base_model->assoc_add($eid1, $edge_linked_to, $eid4);
        $path = "$eid1->($edge_linked_to,$edge_copied_to)";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
            $eid2,
            $eid3,
            $eid4
        );
        sort($actual);
        sort($expected);
        \Flexio\Tests\Check::assertArray('A.9', '\Flexio\Model\Search::search(); search for eids specified by basic path; return unique eids',  $actual, $expected, $results);
    }
}
