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
        // TEST: search tests when results can't be found

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $path = "$eid";
        $result = \Flexio\Tests\Util::getModel()->search($path);
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
        $result = \Flexio\Tests\Util::getModel()->search($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('A.2', '\Model::search(); return empty array when results can\'t be found',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Base\Eid::generate();
        $path = "$eid2";
        $result = \Flexio\Tests\Util::getModel()->search($path);
        $actual = $result;
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('A.3', '\Model::search(); return empty array when results can\'t be found',  $actual, $expected, $results);
    }
}
