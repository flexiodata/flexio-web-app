<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserveA.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-10-25
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
        $model = TestUtil::getModel();


        // TEST: Task::setParams(); variable serialization with single variable

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": ""
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": ""
        }
        ';
        TestCheck::assertInArray('A.1', '\Flexio\Jobs\Base::replaceParameterTokens(); handle case with no replacement', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${v1}"
        }
        ',true);
        $variables = [
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": ""
        }
        ';
        TestCheck::assertInArray('A.2', '\Flexio\Jobs\Base::replaceParameterTokens(); variables that aren\'t set should be replaced with a space', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${v1}"
        }
        ',true);
        $variables = [
            "v2" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": ""
        }
        ';
        TestCheck::assertInArray('A.3', '\Flexio\Jobs\Base::replaceParameterTokens(); variables that aren\'t set should be replaced with a space', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${v1}"
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "field1"
        }
        ';
        TestCheck::assertInArray('A.4', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${v1}"
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "field1"
        }
        ';
        TestCheck::assertInArray('A.5', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; case-sensitive check', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${V1}"
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": ""
        }
        ';
        TestCheck::assertInArray('A.6', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; case-sensitive check', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "$ {v1}"
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "$ {v1}"
        }
        ';
        TestCheck::assertInArray('A.7', '\Flexio\Jobs\Base::replaceParameterTokens(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${ v1}"
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "${ v1}"
        }
        ';
        TestCheck::assertInArray('A.8', '\Flexio\Jobs\Base::replaceParameterTokens(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${v1 }"
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "${v1 }"
        }
        ';
        TestCheck::assertInArray('A.9', '\Flexio\Jobs\Base::replaceParameterTokens(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "  ${v1}  "
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "  field1  "
        }
        ';
        TestCheck::assertInArray('A.10', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "{v1}"
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "{v1}"
        }
        ';
        TestCheck::assertInArray('A.11', '\Flexio\Jobs\Base::replaceParameterTokens(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "$v1}"
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "$v1}"
        }
        ';
        TestCheck::assertInArray('A.12', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${v1"
        }
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "${v1"
        }
        ';
        TestCheck::assertInArray('A.13', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${1}"
        }
        ',true);
        $variables = [
            "1" => "test@flex.io"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "test@flex.io"
        }
        ';
        TestCheck::assertInArray('A.14', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${_1}"
        }
        ',true);
        $variables = [
            "_1" => "test@flex.io"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "test@flex.io"
        }
        ';
        TestCheck::assertInArray('A.15', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${email-address-1}"
        }
        ',true);
        $variables = [
            "email-address-1" => "test@flex.io"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "test@flex.io"
        }
        ';
        TestCheck::assertInArray('A.16', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${email_address_1}"
        }
        ',true);
        $variables = [
            "email_address_1" => "test@flex.io"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "test@flex.io"
        }
        ';
        TestCheck::assertInArray('A.17', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${variable}"
        }
        ',true);
        $variables = [
            "variable" => "'"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "\'"
        }
        ';
        TestCheck::assertInArray('A.18', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable values with symbols', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${variable}"
        }
        ',true);
        $variables = [
            'variable' => '${}'
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "${}"
        }
        ';
        TestCheck::assertInArray('A.19', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable values with symbols', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${variable}"
        }
        ',true);
        $variables = [
            'variable' => "\\"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "\\\\"
        }
        ';
        TestCheck::assertInArray('A.20', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable values with symbols', $actual, $expected, $results);



        // TEST: Task::setParams(); variable serialization with variables of different types

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "different_value" => null
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": null
        }
        ';
        TestCheck::assertInArray('B.1', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => null
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": null
        }
        ';
        TestCheck::assertInArray('B.2', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "different_value" => false
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": null
        }
        ';
        TestCheck::assertInArray('B.3', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => false
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": false
        }
        ';
        TestCheck::assertInArray('B.4', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => true
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": true
        }
        ';
        TestCheck::assertInArray('B.5', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "different_value" => 10
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": null
        }
        ';
        TestCheck::assertInArray('B.6', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => 10
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": 10
        }
        ';
        TestCheck::assertInArray('B.7', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => -2.1
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": -2.1
        }
        ';
        TestCheck::assertInArray('B.8', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "different_value" => [1,2,3]
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": null
        }
        ';
        TestCheck::assertInArray('B.9', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => [1,2,3]
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": [1,2,3]
        }
        ';
        TestCheck::assertInArray('B.10', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "different_value" => ["a" => "b"]
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": null
        }
        ';
        TestCheck::assertInArray('B.11', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => ["a" => "b"]
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": {"a": "b"}
        }
        ';
        TestCheck::assertInArray('B.12', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "The value is: ${value} units."
        }
        ',true);
        $variables = [
            "different_value" => 10
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "The value is:  units."
        }
        ';
        TestCheck::assertInArray('B.13', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "The value is: ${value} units."
        }
        ',true);
        $variables = [
            "value" => 10
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "The value is: 10 units."
        }
        ';
        TestCheck::assertInArray('B.14', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "The statement is ${value}."
        }
        ',true);
        $variables = [
            "different_value" => true
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "The statement is ."
        }
        ';
        TestCheck::assertInArray('B.15', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "The statement is ${value}."
        }
        ',true);
        $variables = [
            "value" => true
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "The statement is true."
        }
        ';
        TestCheck::assertInArray('B.16', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "The value is ${value}."
        }
        ',true);
        $variables = [
            "value" => null
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "The value is ."
        }
        ';
        TestCheck::assertInArray('B.17', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string; nulls become empty', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${value}/${value} is 1"
        }
        ',true);
        $variables = [
            "value" => 10
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "10/10 is 1"
        }
        ';
        TestCheck::assertInArray('B.18', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; replace multiple values in a string', $actual, $expected, $results);



        // TEST: Task::setParams(); variable serialization with multiple variables

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": [ "${v1}", "${v2}" ]
        }
        ',true);
        $variables = [
            "v1" => "field1",
            "v2" => "field2"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": [ "field1", "field2" ]
        }
        ';
        TestCheck::assertInArray('C.1', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": [ "${v2}", "${v1}" ]
        }
        ',true);
        $variables = [
            "v1" => "field1",
            "v2" => "field2"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": [ "field2", "field1" ]
        }
        ';
        TestCheck::assertInArray('C.2', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": [ "${v2}", "${v1}", "${v2}", "${v1}" ]
        }
        ',true);
        $variables = [
            "v1" => "field1",
            "v2" => "field2"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": [ "field2", "field1", "field2", "field1" ]
        }
        ';
        TestCheck::assertInArray('C.3', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": [ "${v2}", "${v1}", "${v2}", "${v1}" ]
        }
        ',true);
        $variables = [
            "v2" => "field2",
            "v3" => "field3"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": [ "field2", "${v1}", "field2", "${v1}" ]
        }
        ';
        TestCheck::assertInArray('C.4', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": {"${key}": "${value}"}
        }
        ',true);
        $variables = [
            "key" => "a",
            "value" => "b"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": {"a": "b"}
        }
        ';
        TestCheck::assertInArray('C.5', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "The ${_1} is ${_2}."
        }
        ',true);
        $variables = [
            "_1" => "key",
            "_2" => "value"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "The key is value."
        }
        ';
        TestCheck::assertInArray('C.6', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": "${_2} is ${_1}."
        }
        ',true);
        $variables = [
            "_1" => "key",
            "_2" => "Value"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "Value is key."
        }
        ';
        TestCheck::assertInArray('C.7', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "params": {"a": "${var_01}", "b": "${var_02}"}
        }
        ',true);
        $variables = [
            "var_01" => "A",
            "var_02" => "B"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": {"a": "A", "b": "B"}
        }
        ';
        TestCheck::assertInArray('C.8', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);



        // TEST: Task::setParams(); variable serialization with multiple variables at varying object depths

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "type": "flexio.nop",
                "custom": "${v1}",
                "params": [
                    "${}",
                    "${v1}",
                    "${V1}",
                    "  ${v11} ",
                    "lpad(\'${v1}\',10,\'0\')"
                ]
            }
        ]
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        [
            {
                "type": "flexio.nop",
                "custom": "${v1}",
                "params": [
                    "",
                    "field1",
                    "",
                    "   ",
                    "lpad(\'field1\',10,\'0\')"
                ]
            }
        ]
        ';
        TestCheck::assertInArray('D.1', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "type": "flexio.nop",
                "custom": "${v1}",
                "params": {
                    "p1": "${}",
                    "p2": "${v1}",
                    "p3": "${V1}",
                    "p4": "  ${v11} ",
                    "p5": "lpad(\'${v1}\',10,\'0\')"

                }
            }
        ]
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        [
            {
                "type": "flexio.nop",
                "custom": "${v1}",
                "params": {
                    "p1": "",
                    "p2": "field1",
                    "p3": "",
                    "p4": "   ",
                    "p5": "lpad(\'field1\',10,\'0\')"
                }
            }
        ]
        ';
        TestCheck::assertInArray('D.2', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "type": "flexio.nop",
                "custom": "${v1}",
                "params": [
                    {"p1": "${}"},
                    {"p2": "${v1}"},
                    {"p3": "${V1}"},
                    {"p4": "  ${v11} "},
                    {"p5": "lpad(\'${v1}\',10,\'0\')"}
                ]
            }
        ]
        ',true);
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        [
            {
                "type": "flexio.nop",
                "custom": "${v1}",
                "params": [
                    {"p1": ""},
                    {"p2": "field1"},
                    {"p3": ""},
                    {"p4": "   "},
                    {"p5": "lpad(\'field1\',10,\'0\')"}
                ]
            }
        ]
        ';
        TestCheck::assertInArray('D.3', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results, TestCheck::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        {
                            "name": "${name}",
                            "type": "${type}",
                            "width": "${width}",
                            "scale": "${scale}"
                        }
                    ]
                }
            }
        ]
        ',true);
        $variables = [
            "name" => "f1",
            "type" => "numeric",
            "width" => "14",
            "scale" => "4"
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        {
                            "name": "f1",
                            "type": "numeric",
                            "width": "14",
                            "scale": "4"
                        }
                    ]
                }
            }
        ]
        ';
        TestCheck::assertInArray('D.4', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        {
                            "name": "f1",
                            "type": "${type}",
                            "width": "${width}",
                            "scale": "${scale}"
                        },
                        {
                            "name": "f2",
                            "type": "${type}",
                            "width": "${width}",
                            "scale": "${scale}"
                        }
                    ]
                }
            }
        ]
        ',true);
        $variables = [
            "name" => "f1",
            "type" => "text",
            "width" => null,
            "scale" => null
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        {
                            "name": "f1",
                            "type": "text"
                        },
                        {
                            "name": "f2",
                            "type": "text"
                        }
                    ]
                }
            }
        ]
        ';
        TestCheck::assertInArray('D.5', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        {
                            "name": "f1",
                            "type": "${type}",
                            "width": "${width}",
                            "scale": "${scale}"
                        },
                        {
                            "name": "f2",
                            "type": "${type}",
                            "width": "${width}",
                            "scale": "${scale}"
                        }
                    ]
                }
            }
        ]
        ',true);
        $variables = [
            "name" => "f1",
            "type" => "numeric",
            "width" => 14,
            "scale" => 4
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        [
            {
                "type": "flexio.create",
                "params": {
                    "columns": [
                        {
                            "name": "f1",
                            "type": "numeric",
                            "width": 14,
                            "scale": 4
                        },
                        {
                            "name": "f2",
                            "type": "numeric",
                            "width": 14,
                            "scale": 4
                        }
                    ]
                }
            }
        ]
        ';
        TestCheck::assertInArray('D.6', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

    }
}
