<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-08-20
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
        // TODO: fill out tests for:
        // TestCheck::assertNumber()
        // TestCheck::assertDateApprox()
        // TestCheck::assertArray()



        // TEST: TestCheck::assertNull()

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNull('', '', null, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNull() working' : 'TestCheck::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.1', 'TestCheck::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNull('', '', NAN, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNull() working' : 'TestCheck::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.2', 'TestCheck::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNull('', '', false, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNull() working' : 'TestCheck::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.3', 'TestCheck::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNull('', '', '', true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNull() working' : 'TestCheck::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.4', 'TestCheck::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNull('', '', 0, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNull() working' : 'TestCheck::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.5', 'TestCheck::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);



        // TEST: TestCheck::assertNan()

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNan('', '', null, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNan() working' : 'TestCheck::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.1', 'TestCheck::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNan('', '', NAN, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNan() working' : 'TestCheck::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.2', 'TestCheck::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNan('', '', false, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNan() working' : 'TestCheck::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.3', 'TestCheck::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNan('', '', '', true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNan() working' : 'TestCheck::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.4', 'TestCheck::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertNan('', '', 0, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertNan() working' : 'TestCheck::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.5', 'TestCheck::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);



        // TEST: TestCheck::assertBoolean()

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertBoolean('', '', null, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertBoolean() working' : 'TestCheck::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.1', 'TestCheck::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertBoolean('', '', 'a', true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertBoolean() working' : 'TestCheck::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.2', 'TestCheck::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertBoolean('', '', 1, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertBoolean() working' : 'TestCheck::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.3', 'TestCheck::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertBoolean('', '', true, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertBoolean() working' : 'TestCheck::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.4', 'TestCheck::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertBoolean('', '', true, false, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertBoolean() working' : 'TestCheck::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.5', 'TestCheck::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertBoolean('', '', false, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertBoolean() working' : 'TestCheck::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.6', 'TestCheck::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertBoolean('', '', false, false, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertBoolean() working' : 'TestCheck::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.7', 'TestCheck::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);



        // TEST: TestCheck::assertString()

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertString('', '', null, '', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertString() working' : 'TestCheck::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.1', 'TestCheck::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertString('', '', 'a', '', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertString() working' : 'TestCheck::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.2', 'TestCheck::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertString('', '', 1, '1', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertString() working' : 'TestCheck::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.3', 'TestCheck::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertString('', '', '', '', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertString() working' : 'TestCheck::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.4', 'TestCheck::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertString('', '', 'a', 'a', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertString() working' : 'TestCheck::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.5', 'TestCheck::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertString('', '', 'a', 'A', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertString() working' : 'TestCheck::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.6', 'TestCheck::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        TestCheck::assertString('', '', 'test', 'test', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertString() working' : 'TestCheck::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.7', 'TestCheck::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);



        // TEST: TestCheck::assertNumber()

        // BEGIN TEST
        // E



        // TEST: TestCheck::assertDateApprox()

        // BEGIN TEST
        // G


        // TEST: TestCheck::assertArrayKeys()

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
        }
        ';
        $expected_array = '
        {
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.1', 'TestCheck::assertArrayKeys(); succeed with two empty objects', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1"
        }
        ';
        $expected_array = '
        {
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.2', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
        }
        ';
        $expected_array = '
        {
            "key1": "value1"
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.3', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1"
        }
        ';
        $expected_array = '
        {
            "key1": "value2"
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.4', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": "value2"
        }
        ';
        $expected_array = '
        {
            "key1": "value2"
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.5', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1"
        }
        ';
        $expected_array = '
        {
            "key1": "value1",
            "key2": "value2"
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.6', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": "value2"
        }
        ';
        $expected_array = '
        {
            "key1": "value3",
            "key2": "value4"
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.6', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
            }
        }
        ';
        $expected_array = '
        {
            "key1": "value3",
            "key2": "value4"
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.7', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": "value2"
        }
        ';
        $expected_array = '
        {
            "key1": "value3",
            "key2": {
            }
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.8', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
            }
        }
        ';
        $expected_array = '
        {
            "key1": "value3",
            "key2": {
            }
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.9', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
                "key3": "value3"
            }
        }
        ';
        $expected_array = '
        {
            "key1": "value2",
            "key2": {
            }
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.10', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
            }
        }
        ';
        $expected_array = '
        {
            "key1": "value2",
            "key2": {
                "key3": "value3"
            }
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.11', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
                "key3": "value2"
            }
        }
        ';
        $expected_array = '
        {
            "key1": "value2",
            "key2": {
                "key3": "value3"
            }
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.12', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
                "key3": "value2"
            },
            "key3": ""
        }
        ';
        $expected_array = '
        {
            "key1": "value2",
            "key2": {
                "key3": "value3"
            }
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.13', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
                "key3": "value2"
            }
        }
        ';
        $expected_array = '
        {
            "key1": "value2",
            "key2": {
                "key3": "value3"
            },
            "key3": ""
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.14', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
                "key3": "value2"
            },
            "key3": "value4"
        }
        ';
        $expected_array = '
        {
            "key1": "value2",
            "key2": {
                "key3": "value3"
            },
            "key3": ""
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.15', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
                "key3": "value2",
                "key4": "value3"
            },
            "key5": "value4"
        }
        ';
        $expected_array = '
        {
            "key1": "value5",
            "key2": {
                "key3": "value6",
                "key4": "value7"
            },
            "key5": "value8"
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.16', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
                "key3": "value2",
                "key4": "value3",
                "key5": ""
            },
            "key6": "value4"
        }
        ';
        $expected_array = '
        {
            "key1": "value5",
            "key2": {
                "key3": "value6",
                "key4": "value7"
            },
            "key6": "value8"
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.17', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key1": "value1",
            "key2": {
                "key3": "value2",
                "key4": "value3",
            },
            "key6": "value4"
        }
        ';
        $expected_array = '
        {
            "key1": "value5",
            "key2": {
                "key3": "value6",
                "key4": "value7",
                "key5": ""
            },
            "key6": "value8"
        }
        ';
        TestCheck::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertArrayKeys() working' : 'TestCheck::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.18', 'TestCheck::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);



        // TEST: TestCheck::assertArray()

        // BEGIN TEST
        // I


        // TEST: TestCheck::assertInArray()

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        ';
        $expected_array = '
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.1', 'TestCheck::assertInArray(); fail when there\'s a bad string input', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        [
        ]
        ';
        $expected_array = '
        [
        ]
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.2', 'TestCheck::assertInArray(); succeed with two empty arrays', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
        }
        ';
        $expected_array = '
        {
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.3', 'TestCheck::assertInArray(); succeed with two empty objects', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        [
            "a"
        ]
        ';
        $expected_array = '
        [
            "a"
        ]
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.4', 'TestCheck::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        [
            1
        ]
        ';
        $expected_array = '
        [
            1
        ]
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.5', 'TestCheck::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        [
        ]
        ';
        $expected_array = '
        [
            1
        ]
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === false ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.6', 'TestCheck::assertInArray(); fail if expected array values are not contained in the actual array values', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        [
            3,
            2,
            1
        ]
        ';
        $expected_array = '
        [
            1
        ]
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.7', 'TestCheck::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        [
            1,
            2,
            3
        ]
        ';
        $expected_array = '
        [
            2
        ]
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.8', 'TestCheck::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        [
            1,
            2,
            3
        ]
        ';
        $expected_array = '
        [
            1,
            3
        ]
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.9', 'TestCheck::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        [
            1,
            2,
            3
        ]
        ';
        $expected_array = '
        [
            1,
            3,
            4
        ]
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === false ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.10', 'TestCheck::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key": "value"
        }
        ';
        $expected_array = '
        {
            "key": "value"
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.11', 'TestCheck::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);


        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "key": "value"
        }
        ';
        $expected_array = '
        {
            "key": "null"
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.12', 'TestCheck::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1": "v1",
            "k2": "v2"
        }
        ';
        $expected_array = '
        {
            "k1": "v1",
            "k2": "v2"
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.13', 'TestCheck::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1": "v1"
        }
        ';
        $expected_array = '
        {
            "k1": ""
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.14', 'TestCheck::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1": ""
        }
        ';
        $expected_array = '
        {
            "k1": "v1"
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.15', 'TestCheck::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1": "v1"
        }
        ';
        $expected_array = '
        {
            "k2": "v1"
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.16', 'TestCheck::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k2": "v1"
        }
        ';
        $expected_array = '
        {
            "k1": "v1"
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.17', 'TestCheck::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1": "v1",
            "k2": "v2"
        }
        ';
        $expected_array = '
        {
            "k1": "v1",
            "k2": ""
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.18', 'TestCheck::assertInArray(); fail if an expected key is in the actual array and the values are different', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1": "v1",
            "k2": "v2"
        }
        ';
        $expected_array = '
        {
            "k1": "v1"
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.19', 'TestCheck::assertInArray(); succeed if all keys in the expected array are in the actual array and the associated values are equivalent', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1": "v1",
            "k2": "v2"
        }
        ';
        $expected_array = '
        {
            "k1": "v1",
            "k3": "v3"
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.20', 'TestCheck::assertInArray(); fail if the expected array has a key that the actual array doesn\'t', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": "v12"
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": "v12"
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.21', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": "v12"
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k12": "v12"
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.22', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": "v12"
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k11": ""
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.23', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": "v12"
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k11": "v11",
                "k13": "v13"
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.24', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": [1,2,3]
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k12": [2,3]
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.25', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": [1,2,3]
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k12": [2,3,4]
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.26', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": ["a",true,null]
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k12": ["a",true]
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.27', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": ["a",true,null]
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k12": [null]
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.28', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": "v11",
                "k12": ["a",true,null]
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k12": ["a",3]
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.29', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        {
            "k1" : {
                "k11": {
                }
                "k12": ["a",true,null]
            }
        }
        ';
        $expected_array = '
        {
            "k1" : {
                "k11": null
                "k12": ["a"]
            }
        }
        ';
        TestCheck::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? 'TestCheck::assertInArray() working' : 'TestCheck::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.30', 'TestCheck::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);
    }
}

