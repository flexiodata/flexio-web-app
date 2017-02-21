<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-12
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TODO: handle commented out tests

        // TEST: Query::exec(); valid query with properties that are a subset of those available

        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "eid" : null,
            "name" : null
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "eid" => $eid,
            "name" => "Sample Project"
        );
        TestCheck::assertArray('A.1', 'Query::exec(); only requested properties should be returned, even if more are available',  $actual, $expected, $results);



        // TEST: Query::exec(); valid query with requested properties that don't exist in object

        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "eid" : null,
            "name" : null,
            "property1" : null,
            "property2" : "abc"
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "eid" => $eid,
            "name" => "Sample Project",
            "property1" => null,
            "property2" => "abc"
        );
        TestCheck::assertArray('B.1', 'Query::exec(); requested properties that don\'t exist should be echoed back',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "eid" : null,
            "property" : []
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "eid" => $eid,
            "property" => array()
        );
        TestCheck::assertArray('B.2', 'Query::exec(); requested properties that don\'t exist should be echoed back',  $actual, $expected, $results);
/*
        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "eid" : null,
            "property" : {
            }
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "eid" => $eid,
            "property" => array(
            )
        );
        TestCheck::assertArray('B.3', 'Query::exec(); requested properties that don\'t exist should be echoed back',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "eid" : null,
            "property" : {
                "a" : "b"
            }
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "eid" => $eid,
            "property" => array(
                "a" => "1",
                "b" => "2"
            )
        );
        TestCheck::assertArray('B.4', 'Query::exec(); requested properties that don\'t exist should be echoed back',  $actual, $expected, $results);
*/


        // TEST: Query::exec(); valid query with properties in different order than existing naturally

        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "description" : "Sample Description",
            "name" : "Sample Project"
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            'description' => 'Sample Description',
            'name' => 'Sample Project'
        );
        TestCheck::assertArray('C.1', 'Query::exec(); return properties in requested order',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "property2" : null,
            "name" : null,
            "property1" : "abc",
            "eid" : null
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "property2" => null,
            "name" => "Sample Project",
            "property1" => "abc",
            "eid" => $eid
        );
        TestCheck::assertArray('C.2', 'Query::exec(); return properties in requested order',  $actual, $expected, $results);



        // TEST: Query::exec(); return properties with specified name

        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "object_eid=eid" : null,
            "object_name=name" : null
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "object_eid" => $eid,
            "object_name" => "Sample Project"
        );
        TestCheck::assertArray('D.1', 'Query::exec(); return properties with specified name',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "object_name=name" : null,
            "object_eid=eid" : null
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "object_name" => "Sample Project",
            "object_eid" => $eid
        );
        TestCheck::assertArray('D.2', 'Query::exec(); return properties with specified name',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'name' => 'Sample Project',
            'description' => 'Sample Description'
        );
        $eid = $model->create(\Model::TYPE_PROJECT, $info);
        $query = '
        {
            "object_eid=eid" : null,
            "object_property1=property1" : "abc"
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "object_eid" => $eid,
            "object_property1" => "abc"
        );
        TestCheck::assertArray('D.3', 'Query::exec(); return properties with specified name',  $actual, $expected, $results);
    }
}
