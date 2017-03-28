<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-12-22
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



        // TEST: Model valid type function

        // BEGIN TEST
        $actual = \Model::isValidType(false);
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Model::isValidType(); return false for invalid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(true);
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Model::isValidType(); return false for invalid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(0);
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::isValidType(); return false for invalid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(1);
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Model::isValidType(); return false for invalid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $result = \Model::isValidType(array());
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('A.5', '\Model::isValidType(); throw exception for bad input parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType('a');
        $expected = false;
        TestCheck::assertBoolean('A.6', '\Model::isValidType(); return false for invalid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_UNDEFINED);
        $expected = false;
        TestCheck::assertBoolean('A.7', '\Model::isValidType(); return false for the undefined type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_OBJECT);
        $expected = true;
        TestCheck::assertBoolean('A.8', '\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_USER);
        $expected = true;
        TestCheck::assertBoolean('A.9', '\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_PROJECT);
        $expected = true;
        TestCheck::assertBoolean('A.10', '\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_PIPE);
        $expected = true;
        TestCheck::assertBoolean('A.11', '\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_STREAM);
        $expected = true;
        TestCheck::assertBoolean('A.12', '\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_CONNECTION);
        $expected = true;
        TestCheck::assertBoolean('A.13', '\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_COMMENT);
        $expected = true;
        TestCheck::assertBoolean('A.14', '\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_PROCESS);
        $expected = true;
        TestCheck::assertBoolean('A.15', '\Model::isValidType(); return true for valid types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidType(\Model::TYPE_TOKEN);
        $expected = true;
        TestCheck::assertBoolean('A.16', '\Model::isValidType(); return true for valid types',  $actual, $expected, $results);



        // TEST: Model valid edge function

        // BEGIN TEST
        $actual = \Model::isValidEdge(false);
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Model::isValidEdge(); return false for invalid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(true);
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Model::isValidEdge(); return false for invalid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(0);
        $expected = false;
        TestCheck::assertBoolean('B.3', '\Model::isValidEdge(); return false for invalid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(1);
        $expected = false;
        TestCheck::assertBoolean('B.4', '\Model::isValidEdge(); return false for invalid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $result = \Model::isValidEdge(array());
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.5', '\Model::isValidEdge(); throw exception for bad input parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge('a');
        $expected = false;
        TestCheck::assertBoolean('B.6', '\Model::isValidEdge(); return false for invalid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_UNDEFINED);
        $expected = false;
        TestCheck::assertBoolean('B.7', '\Model::isValidEdge(); return false for the undefined edge',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_CREATED);
        $expected = true;
        TestCheck::assertBoolean('B.8', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_CREATED_BY);
        $expected = true;
        TestCheck::assertBoolean('B.9', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_OWNS);
        $expected = true;
        TestCheck::assertBoolean('B.10', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_OWNED_BY);
        $expected = true;
        TestCheck::assertBoolean('B.11', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_INVITED);
        $expected = true;
        TestCheck::assertBoolean('B.12', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_INVITED_BY);
        $expected = true;
        TestCheck::assertBoolean('B.13', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_SHARED_WITH);
        $expected = true;
        TestCheck::assertBoolean('B.14', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_SHARED_FROM);
        $expected = true;
        TestCheck::assertBoolean('B.15', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_FOLLOWING);
        $expected = true;
        TestCheck::assertBoolean('B.16', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_FOLLOWED_BY);
        $expected = true;
        TestCheck::assertBoolean('B.17', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_MEMBER_OF);
        $expected = true;
        TestCheck::assertBoolean('B.18', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_HAS_MEMBER);
        $expected = true;
        TestCheck::assertBoolean('B.19', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_LINKED_TO);
        $expected = true;
        TestCheck::assertBoolean('B.20', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_LINKED_FROM);
        $expected = true;
        TestCheck::assertBoolean('B.21', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_COPIED_TO);
        $expected = true;
        TestCheck::assertBoolean('B.22', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_COPIED_FROM);
        $expected = true;
        TestCheck::assertBoolean('B.23', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_COMMENT_ON);
        $expected = true;
        TestCheck::assertBoolean('B.24', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_HAS_COMMENT);
        $expected = true;
        TestCheck::assertBoolean('B.25', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_PROCESS_OF);
        $expected = true;
        TestCheck::assertBoolean('B.26', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidEdge(\Model::EDGE_HAS_PROCESS);
        $expected = true;
        TestCheck::assertBoolean('B.27', '\Model::isValidEdge(); return true for valid edges',  $actual, $expected, $results);



        // TEST: Model valid status FUNCTION

        // BEGIN TEST
        $actual = \Model::isValidStatus(false);
        $expected = false;
        TestCheck::assertBoolean('C.1', '\Model::isValidStatus(); return false for an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(true);
        $expected = false;
        TestCheck::assertBoolean('C.2', '\Model::isValidStatus(); return false for an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(0);
        $expected = false;
        TestCheck::assertBoolean('C.3', '\Model::isValidStatus(); return false for an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(1);
        $expected = false;
        TestCheck::assertBoolean('C.4', '\Model::isValidStatus(); return false for an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $result = \Model::isValidStatus(array());
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('C.5', '\Model::isValidStatus(); throw exception for bad input parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus('a');
        $expected = false;
        TestCheck::assertBoolean('C.6', '\Model::isValidStatus(); return false for an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(\Model::STATUS_UNDEFINED);
        $expected = false;
        TestCheck::assertBoolean('C.7', '\Model::isValidStatus(); return false for an undefined status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(\Model::STATUS_PENDING);
        $expected = true;
        TestCheck::assertBoolean('C.8', '\Model::isValidStatus(); return true for a valid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(\Model::STATUS_AVAILABLE);
        $expected = true;
        TestCheck::assertBoolean('C.9', '\Model::isValidStatus(); return true for a valid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(\Model::STATUS_TRASH);
        $expected = true;
        TestCheck::assertBoolean('C.10', '\Model::isValidStatus(); return true for a valid status',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::isValidStatus(\Model::STATUS_DELETED);
        $expected = true;
        TestCheck::assertBoolean('C.11', '\Model::isValidStatus(); return true for a valid status',  $actual, $expected, $results);
    }
}
