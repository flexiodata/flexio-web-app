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



        // TEST: search tests when results can't be found

        // BEGIN TEST
        $model->clearErrors();
        $eid = \Eid::generate();
        $path = "$eid";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
        );
        TestCheck::assertArray('A.1', '\Model::search(); return empty array when results can\'t be found',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $eid = \Eid::generate();
        $edge_owns = \Model::EDGE_OWNS;
        $edge_following = \Model::EDGE_FOLLOWING;
        $type_project = \Model::TYPE_PROJECT;
        $path = "$eid->($edge_owns,$edge_following)->($type_project)";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
        );
        TestCheck::assertArray('A.2', '\Model::search(); return empty array when results can\'t be found',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid1 = $model->create(\Model::TYPE_OBJECT, $info);
        $eid2 = \Eid::generate();
        $path = "$eid2";
        $result = $model->search($path);
        $actual = $result;
        $expected = array(
        );
        TestCheck::assertArray('A.3', '\Model::search(); return empty array when results can\'t be found',  $actual, $expected, $results);
    }
}
