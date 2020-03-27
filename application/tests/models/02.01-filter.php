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


        // TEST: build function; allowed items

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



        // TEST: build function; basic filters

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => '');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('')))";
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => []);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in (null)))";
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => 'A');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A')))";
        \Flexio\Tests\Check::assertString('B.3', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => ['A']);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A')))";
        \Flexio\Tests\Check::assertString('B.4', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => 'A,b');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A,b')))";
        \Flexio\Tests\Check::assertString('B.5', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => ['A,b']);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A,b')))";
        \Flexio\Tests\Check::assertString('B.6', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => 'A\'b');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A''b')))";
        \Flexio\Tests\Check::assertString('B.7', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => ['A\'b']);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A''b')))";
        \Flexio\Tests\Check::assertString('B.8', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => 'A"b');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A\"b')))";
        \Flexio\Tests\Check::assertString('B.9', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => ['A"b']);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A\"b')))";
        \Flexio\Tests\Check::assertString('B.10', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => ['A','b']);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A','b')))";
        \Flexio\Tests\Check::assertString('B.11', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by');
        $filter = array('owned_by' => ['A,',',b']);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A,',',b')))";
        \Flexio\Tests\Check::assertString('B.12', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);



        // TEST: build function; basic filters

        // BEGIN TEST
        $allowed_items = array('owned_by', 'eid_status');
        $filter = array('owned_by' => 'A', 'eid_status' => 'B');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A')) and (eid_status in ('B')))";
        \Flexio\Tests\Check::assertString('C.1', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by', 'eid_status');
        $filter = array('owned_by' => ['A'], 'eid_status' => 'B');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A')) and (eid_status in ('B')))";
        \Flexio\Tests\Check::assertString('C.2', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by', 'eid_status');
        $filter = array('owned_by' => 'A', 'eid_status' => ['B']);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A')) and (eid_status in ('B')))";
        \Flexio\Tests\Check::assertString('C.3', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by', 'eid_status');
        $filter = array('owned_by' => ['A'], 'eid_status' => ['B']);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A')) and (eid_status in ('B')))";
        \Flexio\Tests\Check::assertString('C.4', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by', 'eid_status');
        $filter = array('owned_by' => ['A','B'], 'eid_status' => 'C');
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A','B')) and (eid_status in ('C')))";
        \Flexio\Tests\Check::assertString('C.5', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);

        // BEGIN TEST
        $allowed_items = array('owned_by', 'eid_status');
        $filter = array('owned_by' => 'A', 'eid_status' => ['B','C']);
        $actual = \Filter::build($db, $filter, $allowed_items);
        $expected = "(true and (owned_by in ('A')) and (eid_status in ('B','C')))";
        \Flexio\Tests\Check::assertString('C.6', '\Flexio\Model\Filter::build(); basic filters',  $actual, $expected, $results);



        // TEST: build function; created_min/created_max formatting

        // BEGIN TEST
        $actual = '';
        try
        {
            $allowed_items = array('created_min');
            $filter = array('created_min' => 'ab');
            $file_expr = \Filter::build($db, $filter, $allowed_items);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('D.1', '\Flexio\Model\Filter::build(); throw an exception if created_min date is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $allowed_items = array('created_min');
            $filter = array('created_min' => '2010-01-02 01:02:03');
            $file_expr = \Filter::build($db, $filter, $allowed_items);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        \Flexio\Tests\Check::assertString('D.2', '\Flexio\Model\Filter::build(); throw an exception if created_min date is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $allowed_items = array('created_max');
            $filter = array('created_max' => 'ab');
            $file_expr = \Filter::build($db, $filter, $allowed_items);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('D.3', '\Flexio\Model\Filter::build(); throw an exception if created_min date is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $allowed_items = array('created_max');
            $filter = array('created_max' => '2010-01-02 01:02:03');
            $file_expr = \Filter::build($db, $filter, $allowed_items);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        \Flexio\Tests\Check::assertString('D.4', '\Flexio\Model\Filter::build(); throw an exception if created_min date is invalid',  $actual, $expected, $results);
    }
}
