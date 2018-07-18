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
        // TEST: \Flexio\Jobs\Base::replaceParameterTokens(); variable serialization with single variable

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Jobs\Base::replaceParameterTokens(); handle case with no replacement', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.2', '\Flexio\Jobs\Base::replaceParameterTokens(); variables that aren\'t set should be replaced with a space', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.3', '\Flexio\Jobs\Base::replaceParameterTokens(); variables that aren\'t set should be replaced with a space', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.4', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.5', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; case-sensitive check', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.6', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; case-sensitive check', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.7', '\Flexio\Jobs\Base::replaceParameterTokens(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
            "params": "${ v1}"
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
        \Flexio\Tests\Check::assertInArray('A.8', '\Flexio\Jobs\Base::replaceParameterTokens(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
            "params": "${v1 }"
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
        \Flexio\Tests\Check::assertInArray('A.9', '\Flexio\Jobs\Base::replaceParameterTokens(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.10', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.11', '\Flexio\Jobs\Base::replaceParameterTokens(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.12', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.13', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.14', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.15', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.16', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.17', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.18', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable values with symbols', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.19', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable values with symbols', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('A.20', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; variable values with symbols', $actual, $expected, $results);



        // TEST: \Flexio\Jobs\Base::replaceParameterTokens(); variable serialization with variables of different types

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.1', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => null
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": ""
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.2', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.3', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => false
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "false"
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.4', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => true
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "true"
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.5', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.6', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => 10
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "10"
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.7', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => -2.1
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "-2.1"
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.8', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.9', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => [1,2,3]
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "[1,2,3]"
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.10', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.11', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
            "params": "${value}"
        }
        ',true);
        $variables = [
            "value" => ["a" => "b"]
        ];
        $actual = \Flexio\Jobs\Base::create($properties)->replaceParameterTokens($variables)->getProperties();
        $expected = '
        {
            "params": "{\"a\":\"b\"}"
        }
        ';
        \Flexio\Tests\Check::assertInArray('B.12', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.13', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.14', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.15', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.16', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.17', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; convert variable value to a string if it\'s part of a string; nulls become empty', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('B.18', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of single variable; replace multiple values in a string', $actual, $expected, $results);



        // \Flexio\Jobs\Base::replaceParameterTokens(); variable serialization with multiple variables

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('C.1', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('C.2', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('C.3', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
            "params": [ "field2", "", "field2", "" ]
        }
        ';
        \Flexio\Tests\Check::assertInArray('C.4', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
            "params": {"${key}": "b"}
        }
        ';
        \Flexio\Tests\Check::assertInArray('C.5', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables; key-side remains unaffected', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('C.6', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('C.7', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        {
            "op": "",
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
        \Flexio\Tests\Check::assertInArray('C.8', '\Flexio\Jobs\Base::replaceParameterTokens(); basic replacement of two variables', $actual, $expected, $results);



        // \Flexio\Jobs\Base::replaceParameterTokens(); variable serialization with multiple variables at varying object depths

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "op": "nop",
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
                "op": "nop",
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
        \Flexio\Tests\Check::assertInArray('D.1', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "op": "nop",
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
                "op": "nop",
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
        \Flexio\Tests\Check::assertInArray('D.2', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "op": "nop",
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
                "op": "nop",
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
        \Flexio\Tests\Check::assertInArray('D.3', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "op": "create",
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
                "op": "create",
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
        \Flexio\Tests\Check::assertInArray('D.4', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "op": "create",
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
                "op": "create",
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
        \Flexio\Tests\Check::assertInArray('D.5', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

        // BEGIN TEST
        $properties = json_decode('
        [
            {
                "op": "create",
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
                "op": "create",
                "params": {
                    "columns": [
                        {
                            "name": "f1",
                            "type": "numeric",
                            "width": "14",
                            "scale": "4"
                        },
                        {
                            "name": "f2",
                            "type": "numeric",
                            "width": "14",
                            "scale": "4"
                        }
                    ]
                }
            }
        ]
        ';
        \Flexio\Tests\Check::assertInArray('D.6', '\Flexio\Jobs\Base::replaceParameterTokens(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

    }
}
