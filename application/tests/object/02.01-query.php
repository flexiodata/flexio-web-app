<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-25
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
        // TODO: test:
        // 1. field renaming for object subsets and associations
        // 2. object query based on association
        // 3. object query based on eid lookup
        // 4. make sure filter parameters work

        // TODO: determine if following tests are proper behavior



        // TEST: Query::exec(); invalid query

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $query = null;
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.1', 'Query::exec(); invalid eid value should return null',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = true;
        $query = json_decode('
        {
           "eid" : null
        }
        ');
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.2', 'Query::exec(); invalid eid value should return null',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $query = null;
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.3', 'Query::exec(); invalid query should return null',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $query = 1;
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.4', 'Query::exec(); invalid query should return null',  $actual, $expected, $results);



        // TEST: Query::exec(); non-existent eid

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $query = json_decode('
        {
        }
        ');
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('B.1', 'Query::exec(); empty query should return an empty object',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $query = json_decode('
        {
           "eid" : null
        }
        ');
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "eid" => null
        );
        \Flexio\Tests\Check::assertArray('B.2', 'Query::exec(); query with top-level properties that don\'t match anything should return object with same properties',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $query = json_decode('
        {
           "property1" : "value1",
           "property2" : "value2"
        }
        ');
        $actual = \Flexio\Object\Query::exec($eid, $query);
        $expected = array(
            "property1" => "value1",
            "property2" => "value2"
        );
        \Flexio\Tests\Check::assertArray('B.3', 'Query::exec(); query with top-level properties that don\'t match anything should return object with same properties',  $actual, $expected, $results);
    }
}
