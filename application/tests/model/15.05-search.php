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
        // TEST: make sure that duplicate edges and eids are removed in search

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $path = "$eid,$eid";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = array(
            $eid
        );
        TestCheck::assertArray('A.1', 'Model::search(); make sure that duplicate edges and eids are removed in search',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $path = "($eid,$eid)";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = array(
            $eid
        );
        TestCheck::assertArray('A.2', 'Model::search(); make sure that duplicate edges and eids are removed in search',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_OBJECT, $info);
        $path = "($eid,$eid,$eid,$eid,$eid,$eid,$eid,$eid,$eid,$eid)";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = array(
            $eid
        );
        TestCheck::assertArray('A.3', 'Model::search(); make sure that duplicate edges and eids are removed in search',  $actual, $expected, $results);
    }
}
