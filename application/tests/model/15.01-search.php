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
        // TEST: search tests with invalid search path

        // BEGIN TEST
        System::getModel()->clearErrors();
        $path = null;
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Model::search(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $path = true;
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Model::search(); return false with invalid search path',  $actual, $expected, $results);

        System::getModel()->clearErrors();
        $path = "";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Model::search(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $path = "->";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = false;
        TestCheck::assertBoolean('A.4', 'Model::search(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $eid = Eid::generate();
        $path = "$eid->";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = false;
        TestCheck::assertBoolean('A.5', 'Model::search(); return false with invalid search path',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $eid = Eid::generate();
        $path = "->$eid";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = false;
        TestCheck::assertBoolean('A.6', 'Model::search(); return false with invalid search path',  $actual, $expected, $results);



        // TEST: search tests with text parameters that aren't valid eids or edges in appropriate places

        // BEGIN TEST
        System::getModel()->clearErrors();
        $path = "abc";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = array(
        );
        TestCheck::assertArray('B.1', 'Model::search(); tolerate text as a search term; return empty if term can\'t be interpreted',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $edge_owns = Model::EDGE_OWNS;
        $path = "$edge_owns";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = array(
        );
        TestCheck::assertArray('B.2', 'Model::search(); tolerate text as a search term; return empty if term can\'t be interpreted',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $edge_owns = Model::EDGE_OWNS;
        $path = "($edge_owns)";
        $result = System::getModel()->search($path);
        $actual = $result;
        $expected = array(
        );
        TestCheck::assertArray('B.3', 'Model::search(); tolerate text as a search term; return empty if term can\'t be interpreted',  $actual, $expected, $results);
    }
}
