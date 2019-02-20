<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
        // \Flexio\Tests\Check::assertNumber()
        // \Flexio\Tests\Check::assertDateApprox()
        // \Flexio\Tests\Check::assertArray()



        // TEST: \Flexio\Tests\Check::assertNull()

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNull('', '', null, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNull() working' : '\Flexio\Tests\Check::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.1', '\Flexio\Tests\Check::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNull('', '', NAN, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNull() working' : '\Flexio\Tests\Check::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.2', '\Flexio\Tests\Check::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNull('', '', false, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNull() working' : '\Flexio\Tests\Check::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.3', '\Flexio\Tests\Check::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNull('', '', '', true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNull() working' : '\Flexio\Tests\Check::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.4', '\Flexio\Tests\Check::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNull('', '', 0, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNull() working' : '\Flexio\Tests\Check::assertNull() not working');
        $results[] = new \Flexio\Tests\Result('A.5', '\Flexio\Tests\Check::assertNull(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);



        // TEST: \Flexio\Tests\Check::assertNan()

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNan('', '', null, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNan() working' : '\Flexio\Tests\Check::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.1', '\Flexio\Tests\Check::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNan('', '', NAN, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNan() working' : '\Flexio\Tests\Check::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.2', '\Flexio\Tests\Check::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNan('', '', false, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNan() working' : '\Flexio\Tests\Check::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.3', '\Flexio\Tests\Check::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNan('', '', '', true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNan() working' : '\Flexio\Tests\Check::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.4', '\Flexio\Tests\Check::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertNan('', '', 0, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertNan() working' : '\Flexio\Tests\Check::assertNan() not working');
        $results[] = new \Flexio\Tests\Result('B.5', '\Flexio\Tests\Check::assertNan(); fail if \'actual\' parameter is anything besides null', $test_result, $test_message);



        // TEST: \Flexio\Tests\Check::assertBoolean()

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertBoolean('', '', null, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertBoolean() working' : '\Flexio\Tests\Check::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.1', '\Flexio\Tests\Check::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertBoolean('', '', 'a', true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertBoolean() working' : '\Flexio\Tests\Check::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.2', '\Flexio\Tests\Check::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertBoolean('', '', 1, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertBoolean() working' : '\Flexio\Tests\Check::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.3', '\Flexio\Tests\Check::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertBoolean('', '', true, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertBoolean() working' : '\Flexio\Tests\Check::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.4', '\Flexio\Tests\Check::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertBoolean('', '', true, false, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertBoolean() working' : '\Flexio\Tests\Check::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.5', '\Flexio\Tests\Check::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertBoolean('', '', false, true, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertBoolean() working' : '\Flexio\Tests\Check::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.6', '\Flexio\Tests\Check::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertBoolean('', '', false, false, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertBoolean() working' : '\Flexio\Tests\Check::assertBoolean() not working');
        $results[] = new \Flexio\Tests\Result('C.7', '\Flexio\Tests\Check::assertBoolean(); succeed if inputs are boolean and are equal', $test_result, $test_message);



        // TEST: \Flexio\Tests\Check::assertString()

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertString('', '', null, '', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertString() working' : '\Flexio\Tests\Check::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.1', '\Flexio\Tests\Check::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertString('', '', 'a', '', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertString() working' : '\Flexio\Tests\Check::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.2', '\Flexio\Tests\Check::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertString('', '', 1, '1', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertString() working' : '\Flexio\Tests\Check::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.3', '\Flexio\Tests\Check::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertString('', '', '', '', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertString() working' : '\Flexio\Tests\Check::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.4', '\Flexio\Tests\Check::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertString('', '', 'a', 'a', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertString() working' : '\Flexio\Tests\Check::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.5', '\Flexio\Tests\Check::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertString('', '', 'a', 'A', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertString() working' : '\Flexio\Tests\Check::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.6', '\Flexio\Tests\Check::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);

        // BEGIN TEST
        $assert_result = array();
        \Flexio\Tests\Check::assertString('', '', 'test', 'test', $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertString() working' : '\Flexio\Tests\Check::assertString() not working');
        $results[] = new \Flexio\Tests\Result('D.7', '\Flexio\Tests\Check::assertString(); succeed if inputs are strings and are equal', $test_result, $test_message);



        // TEST: \Flexio\Tests\Check::assertNumber()

        // BEGIN TEST
        // E



        // TEST: \Flexio\Tests\Check::assertDateApprox()

        // BEGIN TEST
        // G


        // TEST: \Flexio\Tests\Check::assertArrayKeys()

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.1', '\Flexio\Tests\Check::assertArrayKeys(); succeed with two empty objects', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.2', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.3', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.4', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.5', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.6', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.6', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.7', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.8', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.9', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.10', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.11', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.12', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.13', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.14', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.15', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.16', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.17', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertArrayKeys('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertArrayKeys() working' : '\Flexio\Tests\Check::assertArrayKeys() not working');
        $results[] = new \Flexio\Tests\Result('H.18', '\Flexio\Tests\Check::assertArrayKeys(); succeed if the key\'s are the same; fail otherwise', $test_result, $test_message);



        // TEST: \Flexio\Tests\Check::assertArray()

        // BEGIN TEST
        // I


        // TEST: \Flexio\Tests\Check::assertInArray()

        // BEGIN TEST
        $assert_result = array();
        $actual_array = '
        ';
        $expected_array = '
        ';
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.1', '\Flexio\Tests\Check::assertInArray(); fail when there\'s a bad string input', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.2', '\Flexio\Tests\Check::assertInArray(); succeed with two empty arrays', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.3', '\Flexio\Tests\Check::assertInArray(); succeed with two empty objects', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.4', '\Flexio\Tests\Check::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.5', '\Flexio\Tests\Check::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === false ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.6', '\Flexio\Tests\Check::assertInArray(); fail if expected array values are not contained in the actual array values', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.7', '\Flexio\Tests\Check::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.8', '\Flexio\Tests\Check::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.9', '\Flexio\Tests\Check::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === false ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.10', '\Flexio\Tests\Check::assertInArray(); succeed if expected array values are contained in the actual array values', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.11', '\Flexio\Tests\Check::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);


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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.12', '\Flexio\Tests\Check::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.13', '\Flexio\Tests\Check::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.14', '\Flexio\Tests\Check::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.15', '\Flexio\Tests\Check::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.16', '\Flexio\Tests\Check::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.17', '\Flexio\Tests\Check::assertInArray(); succeed with two objects with the same key/value', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.18', '\Flexio\Tests\Check::assertInArray(); fail if an expected key is in the actual array and the values are different', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.19', '\Flexio\Tests\Check::assertInArray(); succeed if all keys in the expected array are in the actual array and the associated values are equivalent', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.20', '\Flexio\Tests\Check::assertInArray(); fail if the expected array has a key that the actual array doesn\'t', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.21', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.22', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.23', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.24', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.25', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.26', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.27', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = true;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.28', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.29', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);

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
        \Flexio\Tests\Check::assertInArray('', '', $actual_array, $expected_array, $assert_result);
        $actual = $assert_result[0]->passed;
        $expected = false;
        $test_result = ($actual === $expected);
        $test_message = ($test_result === true ? '\Flexio\Tests\Check::assertInArray() working' : '\Flexio\Tests\Check::assertInArray() not working');
        $results[] = new \Flexio\Tests\Result('J.30', '\Flexio\Tests\Check::assertInArray(); make sure key/values are checked recursively', $test_result, $test_message);
    }
}

