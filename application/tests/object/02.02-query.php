<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-12
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
        $model = \Flexio\Tests\Util::getModel();


        // TODO: handle commented out tests

        // TEST: Query::exec(); valid query with properties that are a subset of those available

        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
        $query = '
        {
            "eid" : null,
            "title" : null
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "eid" => $eid,
            "title" => 'Sample Pipe'
        );
        \Flexio\Tests\Check::assertArray('A.1', 'Query::exec(); only requested properties should be returned, even if more are available',  $actual, $expected, $results);



        // TEST: Query::exec(); valid query with requested properties that don't exist in object

        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
        $query = '
        {
            "eid" : null,
            "title" : null,
            "property1" : null,
            "property2" : "abc"
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "eid" => $eid,
            "title" => "Sample Pipe",
            "property1" => null,
            "property2" => "abc"
        );
        \Flexio\Tests\Check::assertArray('B.1', 'Query::exec(); requested properties that don\'t exist should be echoed back',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
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
        \Flexio\Tests\Check::assertArray('B.2', 'Query::exec(); requested properties that don\'t exist should be echoed back',  $actual, $expected, $results);
/*
        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
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
        \Flexio\Tests\Check::assertArray('B.3', 'Query::exec(); requested properties that don\'t exist should be echoed back',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
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
        \Flexio\Tests\Check::assertArray('B.4', 'Query::exec(); requested properties that don\'t exist should be echoed back',  $actual, $expected, $results);
*/


        // TEST: Query::exec(); valid query with properties in different order than existing naturally

        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
        $query = '
        {
            "description" : "Sample Description",
            "title" : "Sample Pipe"
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            'description' => 'Sample Description',
            'title' => 'Sample Pipe'
        );
        \Flexio\Tests\Check::assertArray('C.1', 'Query::exec(); return properties in requested order',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
        $query = '
        {
            "property2" : null,
            "title" : null,
            "property1" : "abc",
            "eid" : null
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "property2" => null,
            "title" => "Sample Pipe",
            "property1" => "abc",
            "eid" => $eid
        );
        \Flexio\Tests\Check::assertArray('C.2', 'Query::exec(); return properties in requested order',  $actual, $expected, $results);



        // TEST: Query::exec(); return properties with specified name

        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
        $query = '
        {
            "object_eid=eid" : null,
            "object_title=title" : null
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "object_eid" => $eid,
            "object_title" => "Sample Pipe"
        );
        \Flexio\Tests\Check::assertArray('D.1', 'Query::exec(); return properties with specified name',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
        $query = '
        {
            "object_title=title" : null,
            "object_eid=eid" : null
        }
        ';
        $query = json_decode($query);
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "object_title" => "Sample Pipe",
            "object_eid" => $eid
        );
        \Flexio\Tests\Check::assertArray('D.2', 'Query::exec(); return properties with specified name',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
            'title' => 'Sample Pipe',
            'description' => 'Sample Description'
        );
        $eid = $model->pipe->create($info);
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
        \Flexio\Tests\Check::assertArray('D.3', 'Query::exec(); return properties with specified name',  $actual, $expected, $results);
    }
}
