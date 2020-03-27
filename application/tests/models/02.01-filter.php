<?php
/**
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-03-27
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
        $db = \Flexio\System\System::getModel()->getDatabase();


        // TEST: build function

        // BEGIN TEST
        $actual = '';
        try
        {
            $allowed_items = array();
            $filter = array('owned_by' => 'A');
            $file_expr = \Filter::build($db, $filter, $allowed_items);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Filter::build(); throw an exception if filter parameters are included that aren\'t allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $allowed_items = array('owned_by' => 'A');
            $filter = array('eid_status' => 'A');
            $file_expr = \Filter::build($db, $filter, $allowed_items);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Model\Filter::build(); throw an exception if filter parameters are included that aren\'t allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $allowed_items = array('owned_by' => 'A');
            $filter = array('OWNED_BY' => 'A');
            $file_expr = \Filter::build($db, $filter, $allowed_items);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Model\Filter::build(); throw an exception if filter parameters are included that aren\'t allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array();
        $filter = array();
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = '(true)';
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Model\Filter::build(); don\'t require filter/allowed parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array();
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true)";
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Model\Filter::build(); don\'t require allowed parameters to have associated filter items',  $actual, $expected, $results);


        // TEST: build function

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => 'A');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by = 'A'))";
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Model\Filter::build(); build filter string',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('eid_status');
        $filter = array('eid_status' => 'A');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (eid_status = 'A'))";
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Model\Filter::build(); build filter string',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by', 'eid_status');
        $filter = array('owned_by' => 'A', 'eid_status' => 'B');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by = 'A') and (eid_status = 'B'))";
        \Flexio\Tests\Check::assertString('B.3', '\Flexio\Model\Filter::build(); build filter string',  $actual, $expected, $results);
    }
}
