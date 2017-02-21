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


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: search tests for multi-level eid/association combinations

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $edge_has_member = \Model::EDGE_HAS_MEMBER;
        $edge_owns = \Model::EDGE_OWNS;
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid4 = $model->create(\Model::TYPE_OBJECT, $info);
        $model->assoc_add($eid1, $edge_has_member, $eid2);
        $model->assoc_add($eid2, $edge_has_member, $eid3);
        $model->assoc_add($eid3, $edge_has_member, $eid4);
        $path = "$eid1->($edge_has_member)->$eid2->$edge_has_member";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
            $eid3,
        );
        TestCheck::assertArray('A.1', '\Model::search(); search eids multiple levels deep',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $edge_has_member = \Model::EDGE_HAS_MEMBER;
        $edge_owns = \Model::EDGE_OWNS;
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid4 = $model->create(\Model::TYPE_OBJECT, $info);
        $model->assoc_add($eid1, $edge_has_member, $eid2);
        $model->assoc_add($eid2, $edge_has_member, $eid3);
        $model->assoc_add($eid3, $edge_has_member, $eid4);
        $path = "$eid1->($edge_has_member)->$eid2->$edge_has_member->$eid3";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
            $eid3,
        );
        TestCheck::assertArray('A.2', '\Model::search(); search eids multiple levels deep',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $edge_has_member = \Model::EDGE_HAS_MEMBER;
        $edge_owns = \Model::EDGE_OWNS;
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid3 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid4 = $model->create(\Model::TYPE_OBJECT, $info);
        $model->assoc_add($eid1, $edge_has_member, $eid2);
        $model->assoc_add($eid2, $edge_has_member, $eid3);
        $model->assoc_add($eid3, $edge_has_member, $eid4);
        $model->assoc_add($eid1, $edge_owns, $eid2);
        $model->assoc_add($eid2, $edge_owns, $eid3);
        $model->assoc_add($eid3, $edge_owns, $eid4);
        $path = "$eid1->($edge_owns)->$eid2->$edge_has_member->$eid3";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
            $eid3,
        );
        TestCheck::assertArray('A.3', '\Model::search(); search eids multiple levels deep',  $actual, $expected, $results);
    }
}
