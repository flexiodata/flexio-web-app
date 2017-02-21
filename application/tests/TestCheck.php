<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-04-21
 *
 * @package flexio
 * @subpackage Controller
 */


namespace Flexio\Tests;


class TestCheck
{
    public static function assertNull($name, $description, $actual, $expected, &$results)
    {
        // add a TestResult onto the result array
        $test_result = new TestResult;
        $results[] = $test_result;

        $test_result->name = $name;
        $test_result->description = $description;

        if (is_null($actual))
        {
            $test_result->passed = true;
            $test_result->message = '';
            return;
        }

        $test_result->passed = false;
        $test_result->message = 'Expected null value;  Returned ' . TestCheck::stringify($actual);
    }

    public static function assertNan($name, $description, $actual, $expected, &$results)
    {
        // add a TestResult onto the result array
        $test_result = new TestResult;
        $results[] = $test_result;

        $test_result->name = $name;
        $test_result->description = $description;

        if (is_float($actual) && is_nan($actual))
        {
            $test_result->passed = true;
            $test_result->message = '';
            return;
        }

        $test_result->passed = false;
        $test_result->message = 'Expected NaN value;  Returned ' . TestCheck::stringify($actual);
    }


    public static function assertString($name, $description, $actual, $expected, &$results)
    {
        // add a TestResult onto the result array
        $test_result = new TestResult;
        $results[] = $test_result;

        $test_result->name = $name;
        $test_result->description = $description;


        if ($actual === $expected)
        {
            $test_result->passed = true;
            $test_result->message = '';
            return;
        }


        $test_result->passed = false;
        $test_result->message = 'Expected ' . TestCheck::stringify($expected) . ';  Returned ' . TestCheck::stringify($actual);
    }

    public static function assertNumber($name, $description, $actual, $expected, &$results)
    {
        // add a TestResult onto the result array
        $test_result = new TestResult;
        $results[] = $test_result;

        $test_result->name = $name;
        $test_result->description = $description;

        // comparing doubles always requires usage of epsilon tolerance
        if (is_double($actual) && is_double($expected) && TestUtil::dblcompare($actual, $expected) === 0)
        {
            $test_result->passed = true;
            $test_result->message = '';
            return;
        }

        if ($actual === $expected)
        {
            $test_result->passed = true;
            $test_result->message = '';
            return;
        }

        $test_result->passed = false;
        $test_result->message = 'Expected ' . TestCheck::stringify($expected,true) . ';  Returned ' . TestCheck::stringify($actual,true);
    }

    public static function assertDateApprox($name, $description, $actual, $expected, &$results)
    {
        // note: this returns true if the actual datetime is within a tolerance
        // of the expected; it's used to test values where there might be a slight
        // difference between dates due to execution time

        // set the tolerance to use in the comparison, expressed in seconds;
        // right now, allow a one-minute difference
        $tolerance = 60;

        // add a TestResult onto the result array
        $test_result = new TestResult;
        $results[] = $test_result;

        // the actual and expected

        $test_result->name = $name;
        $test_result->description = $description;

        if (is_string($actual) && is_string($expected))
        {
            // convert the input dates into time values we can compare
            $timestamp_actual = strtotime($actual);
            $timestamp_expected = strtotime($expected);

            if (abs($timestamp_actual - $timestamp_expected) < $tolerance)
            {
                $test_result->passed = true;
                $test_result->message = '';
                return;
            }
        }

        $test_result->passed = false;
        $test_result->message = 'Expected ' . TestCheck::stringify($expected,true) . ';  Returned ' . TestCheck::stringify($actual,true);
    }

    public static function assertBoolean($name, $description, $actual, $expected, &$results)
    {
        // add a TestResult onto the result array
        $test_result = new TestResult;
        $results[] = $test_result;

        $test_result->name = $name;
        $test_result->description = $description;

        if ($actual === false && $expected === false)
        {
            $test_result->passed = true;
            $test_result->message = '';
            return;
        }

        if ($actual === true && $expected === true)
        {
            $test_result->passed = true;
            $test_result->message = '';
            return;
        }

        $test_result->passed = false;
        $test_result->message = 'Expected ' . TestCheck::stringify($expected,true) . ';  Returned ' . TestCheck::stringify($actual,true);
    }

    public static function assertArray($name, $description, $actual, $expected, &$results)
    {
        // add a TestResult onto the result array
        $test_result = new TestResult;
        $results[] = $test_result;

        $test_result->name = $name;
        $test_result->description = $description;

        // make sure "actual" input is an arary; make sure "expected" input is
        // an array or a string; in the case of a string, we'll treat it as JSON
        // and convert the results to an array representation
        if (!is_array($actual) || (!is_array($expected) && !is_string($expected)))
        {
            $test_result->passed = false;
            $test_result->message = 'Invalid test assertion.  Expected ' . TestCheck::stringify($expected) . ';  Returned ' . TestCheck::stringify($actual);
            return;
        }

        // if we have a string, convert it into an array
        if (is_string($expected))
            $expected = json_decode($expected,true); // use true param to convert into array rather than object

        // test whether the arrays have the same keys and values in the same
        // order and with the same types
        if ($actual === $expected)
        {
            $test_result->passed = true;
            $test_result->message = '';
            return;
        }

        $test_result->passed = false;
        $test_result->message = 'Expected ' . TestCheck::stringify($expected) . ';  Returned ' . TestCheck::stringify($actual);
    }

    public static function assertInArray($name, $description, $actual, $expected, &$results)
    {
        // note: returns true if the key/value pairs in the expected array
        // are in the actual array; false otherwise

        // add a TestResult onto the result array
        $test_result = new TestResult;
        $results[] = $test_result;

        $test_result->name = $name;
        $test_result->description = $description;

        // make sure "actual" input is an array or a string; in the case of a string,
        // we'll treat it as JSON and convert the results to an array representation
        if ((!is_array($actual) && !is_string($actual)))
        {
            $test_result->passed = false;
            $test_result->message = 'Invalid test assertion';
            return;
        }

        // make sure "expected" input is an array or a string; in the case of a string,
        // we'll treat it as JSON and convert the results to an array representation
        if ((!is_array($expected) && !is_string($expected)))
        {
            $test_result->passed = false;
            $test_result->message = 'Invalid test assertion';
            return;
        }

        // if we have a string, convert it into an array
        if (is_string($actual))
            $actual = json_decode($actual,true); // use true param to convert into array rather than object

        if (!isset($actual))
        {
            $test_result->passed = false;
            $test_result->message = 'Invalid test assertion; is \'actual\' an invalid JSON string?';
            return;
        }

        // if we have a string, convert it into an array
        if (is_string($expected))
            $expected = json_decode($expected,true); // use true param to convert into array rather than object

        if (!isset($expected))
        {
            $test_result->passed = false;
            $test_result->message = 'Invalid test assertion; is \'expected\' an invalid JSON string?';
            return;
        }

        // check if the expected array is in the actual array
        $expected_in_actual = self::inArray($actual, $expected);
/*
        $keyvalue_expected = array();
        $keyvalue_actual = array();
        foreach ($expected as $key => $value)
        {
            if (isset($actual[$key]) && $actual[$key] === $value)
                continue;

            $keyvalue_expected[$key] = $value;

            if (!isset($actual[$key]))
                $keyvalue_actual[$key] = 'Not Set';
                 else
                $keyvalue_actual[$key] = $actual[$key];
        }
*/

        // if all the keys in the expected array are in the actual
        // array and the values for the keys in each array are the
        // same value and type, the test passes
        if ($expected_in_actual === true)
        {
            $test_result->passed = true;
            $test_result->message = '';
            return;
        }

        $test_result->passed = false;
        $test_result->message = $test_result->message = 'Expected ' . TestCheck::stringify($expected) . 'to be a subset of ' . TestCheck::stringify($actual);
    }

    public static function inArray($item1, $item2)
    {
        // note: tests if the key/value pairs in array2 are in array1

        // if two items are identical, then one is contained in the other
        if ($item1 === $item2)
            return true;

        // consider to nulls to be the same
        if (!isset($item1) && !isset($item2))
            return true;

        // if we have two values and they have different values, return false;
        // if we have two items where one is a value and the other is not,
        // return false
        $is_value1 = !is_array($item1) && !is_object($item1);
        $is_value2 = !is_array($item2) && !is_object($item2);

        if ($is_value1 === true && $is_value2 === true && $item1 !== $item2)
            return false;
        if ($is_value1 === false && $is_value2 === true)
            return false;
        if ($is_value1 === true && $is_value2 === false)
            return false;

        // we're working with arrays or objects; cast them to arrays for
        // easy comparison
        $array1 = (array)$item1;
        $array2 = (array)$item2;

        // check if the input arrays are sequential or associative
        $is_associative_array1 = \Util::isAssociativeArray($array1);
        $is_associative_array2 = \Util::isAssociativeArray($array2);

        // if the two arrays are not of the same type, then array2 is
        // not contained in array1
        if ($is_associative_array1 !== $is_associative_array2)
            return false;

        // the two arrays are either both sequential or associative;
        // if they're sequential, make sure that the values of array2
        // are contained in array1; if they're associative, make sure
        // that the key/value pairs of array2 are in array1

        if ($is_associative_array2 === true)
        {
            foreach ($array2 as $key2 => $value2)
            {
                if (array_key_exists($key2, $array1) === false)
                    return false;

                if (self::inArray($array1[$key2], $value2) === false)
                    return false;
            }

            return true;
        }
         else
        {
            foreach ($array2 as $key2 => $value2)
            {
                $is_included = false;
                foreach ($array1 as $key1 => $value1)
                {
                    if (self::inArray($value1, $value2) === false)
                        continue;

                    $is_included = true;
                    break;
                }

                if ($is_included === false)
                    return false;
            }

            return true;
        }

        return false;
    }

    public static function stringify($o)
    {
        $type = gettype($o);
        if ($type == 'array')
        {
            return json_encode($o);
        }
             else
        {
            if (is_float($o) && is_nan($o))
                return "($type)NaN";
                 else
                return "($type)".json_encode($o);
        }
        // return var_export($o,true);
    }
}
