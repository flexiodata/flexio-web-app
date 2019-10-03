<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
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
        // FUNCTION:
        //   \Flexio\Model\Model::isValidType()
        //   \Flexio\Model\Model::isValidEdge()
        //   \Flexio\Model\Model::isValidStatus()


        // TEST: valid type function

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidType(false);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Model::isValidType(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidType(true);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Model\Model::isValidType(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidType(0);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Model\Model::isValidType(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidType(1);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Model\Model::isValidType(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $result = \Model::isValidType(array());
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Model\Model::isValidType(); throw exception for bad input parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType('a');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Model\Model::isValidType(); return false for invalid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_UNDEFINED);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Model\Model::isValidType(); return false for the undefined type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_USER);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Model\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_PIPE);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Model\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_STREAM);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.10', '\Flexio\Model\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_CONNECTION);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.11', '\Flexio\Model\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_COMMENT);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.12', '\Flexio\Model\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_PROCESS);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.13', '\Flexio\Model\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_TOKEN);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.14', '\Flexio\Model\Model::isValidType(); return true for valid types',  $actual, $expected, $results);


        // TEST: valid edge function

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidEdge(false);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Model\Model::isValidEdge(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidEdge(true);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Model\Model::isValidEdge(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidEdge(0);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.3', '\Flexio\Model\Model::isValidEdge(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidEdge(1);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.4', '\Flexio\Model\Model::isValidEdge(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $result = \Model::isValidEdge(array());
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.5', '\Flexio\Model\Model::isValidEdge(); throw exception for bad input parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge('a');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Model\Model::isValidEdge(); return false for invalid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_UNDEFINED);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.7', '\Flexio\Model\Model::isValidEdge(); return false for the undefined edge',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_INVITED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.12', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_INVITED_BY);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.13', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_SHARED_WITH);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.14', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_SHARED_FROM);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.15', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_FOLLOWING);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.16', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_FOLLOWED_BY);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.17', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_LINKED_TO);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.20', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_LINKED_FROM);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.21', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_COPIED_TO);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.22', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_COPIED_FROM);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.23', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_COMMENT_ON);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.24', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_HAS_COMMENT);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.25', '\Flexio\Model\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);


        // TEST: valid status function

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidStatus(false);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.1', '\Flexio\Model\Model::isValidStatus(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidStatus(true);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.2', '\Flexio\Model\Model::isValidStatus(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidStatus(0);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.3', '\Flexio\Model\Model::isValidStatus(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            \Model::isValidStatus(1);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.4', '\Flexio\Model\Model::isValidStatus(); throw an exception for non-string input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $result = \Model::isValidStatus(array());
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.5', '\Flexio\Model\Model::isValidStatus(); throw exception for bad input parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus('a');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.6', '\Flexio\Model\Model::isValidStatus(); return false for an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(\Model::STATUS_UNDEFINED);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.7', '\Flexio\Model\Model::isValidStatus(); return false for an undefined status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(\Model::STATUS_PENDING);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.8', '\Flexio\Model\Model::isValidStatus(); return true for a valid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(\Model::STATUS_AVAILABLE);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.9', '\Flexio\Model\Model::isValidStatus(); return true for a valid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(\Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.10', '\Flexio\Model\Model::isValidStatus(); return true for a valid status',  $actual, $expected, $results);
    }
}
