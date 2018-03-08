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


        // TEST: search tests when results can't be found

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $path = "$eid";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('A.1', '\Model::search(); return empty array when results can\'t be found',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $edge_owns = \Model::EDGE_OWNS;
        $edge_following = \Model::EDGE_FOLLOWING;
        $type_pipe = \Model::TYPE_PIPE;
        $path = "$eid->($edge_owns,$edge_following)->($type_pipe)";
        $result = $search_model->exec($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('A.2', '\Model::search(); return empty array when results can\'t be found',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertArray('A.3', '\Model::search(); return empty array when results can\'t be found',  $actual, $expected, $results);
    }
}
