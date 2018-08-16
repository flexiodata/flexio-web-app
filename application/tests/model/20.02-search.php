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


        // TEST: \Flexio\Model\Search::exec(); search tests when results can't be found

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $path = "$eid";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Model\Search::exec(); return empty array when results can\'t be found',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $edge_copied_to = \Model::EDGE_COPIED_TO;
        $edge_linked_to = \Model::EDGE_LINKED_TO;
        $type_pipe = \Model::TYPE_PIPE;
        $path = "$eid->($edge_copied_to,$edge_linked_to)->($type_pipe)";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Model\Search::exec(); return empty array when results can\'t be found',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = $pipe_model->create($info);
        $eid2 = \Flexio\Base\Eid::generate();
        $path = "$eid2";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Model\Search::exec(); return empty array when results can\'t be found',  $actual, $expected, $results);
    }
}
