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
        // TEST: search tests for multi-level eid/association combinations

        // BEGIN TEST
        $info = array(
        );
        $edge_has_member = \Model::EDGE_HAS_MEMBER;
        $edge_owns = \Model::EDGE_OWNS;
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid4 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        \Flexio\Tests\Util::getModel()->assoc_add($eid1, $edge_has_member, $eid2);
        \Flexio\Tests\Util::getModel()->assoc_add($eid2, $edge_has_member, $eid3);
        \Flexio\Tests\Util::getModel()->assoc_add($eid3, $edge_has_member, $eid4);
        $path = "$eid1->($edge_has_member)->$eid2->$edge_has_member";
        $result = \Flexio\Tests\Util::getModel()->search->exec($path);
        $actual = $result;
        $expected = array(
            $eid3,
        );
        \Flexio\Tests\Check::assertArray('A.1', '\Model::search(); search eids multiple levels deep',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $edge_has_member = \Model::EDGE_HAS_MEMBER;
        $edge_owns = \Model::EDGE_OWNS;
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid4 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        \Flexio\Tests\Util::getModel()->assoc_add($eid1, $edge_has_member, $eid2);
        \Flexio\Tests\Util::getModel()->assoc_add($eid2, $edge_has_member, $eid3);
        \Flexio\Tests\Util::getModel()->assoc_add($eid3, $edge_has_member, $eid4);
        $path = "$eid1->($edge_has_member)->$eid2->$edge_has_member->$eid3";
        $result = \Flexio\Tests\Util::getModel()->search->exec($path);
        $actual = $result;
        $expected = array(
            $eid3,
        );
        \Flexio\Tests\Check::assertArray('A.2', '\Model::search(); search eids multiple levels deep',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $edge_has_member = \Model::EDGE_HAS_MEMBER;
        $edge_owns = \Model::EDGE_OWNS;
        $eid1 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid3 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        $eid4 = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_OBJECT, $info);
        \Flexio\Tests\Util::getModel()->assoc_add($eid1, $edge_has_member, $eid2);
        \Flexio\Tests\Util::getModel()->assoc_add($eid2, $edge_has_member, $eid3);
        \Flexio\Tests\Util::getModel()->assoc_add($eid3, $edge_has_member, $eid4);
        \Flexio\Tests\Util::getModel()->assoc_add($eid1, $edge_owns, $eid2);
        \Flexio\Tests\Util::getModel()->assoc_add($eid2, $edge_owns, $eid3);
        \Flexio\Tests\Util::getModel()->assoc_add($eid3, $edge_owns, $eid4);
        $path = "$eid1->($edge_owns)->$eid2->$edge_has_member->$eid3";
        $result = \Flexio\Tests\Util::getModel()->search->exec($path);
        $actual = $result;
        $expected = array(
            $eid3,
        );
        \Flexio\Tests\Check::assertArray('A.3', '\Model::search(); search eids multiple levels deep',  $actual, $expected, $results);
    }
}
