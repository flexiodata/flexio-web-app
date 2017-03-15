<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserveA.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-09-20
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // BEGIN TEST
        $task = '
        [{
            "params": "${v1}"
        }]
        ';
        $variables = [
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": ""
        }]
        ';
        TestCheck::assertInArray('A.2', 'Task::setParams(); variables that aren\'t set should be replaced with a space', $actual, $expected, $results);




return;



        // TEST: Task::setParams(); variable serialization with single variable

        // BEGIN TEST
        $task = '
        [{
            "params": ""
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": ""
        }]
        ';
        TestCheck::assertInArray('A.1', 'Task::setParams(); handle case with no replacement', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${v1}"
        }]
        ';
        $variables = [
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": ""
        }]
        ';
        TestCheck::assertInArray('A.2', 'Task::setParams(); variables that aren\'t set should be replaced with a space', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${v1}"
        }]
        ';
        $variables = [
            "v2" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": ""
        }]
        ';
        TestCheck::assertInArray('A.3', 'Task::setParams(); variables that aren\'t set should be replaced with a space', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${v1}"
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "field1"
        }]
        ';
        TestCheck::assertInArray('A.4', 'Task::setParams(); basic replacement of single variable', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${v1}"
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "field1"
        }]
        ';
        TestCheck::assertInArray('A.5', 'Task::setParams(); basic replacement of single variable; case-sensitive check', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${V1}"
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": ""
        }]
        ';
        TestCheck::assertInArray('A.6', 'Task::setParams(); basic replacement of single variable; case-sensitive check', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "$ {v1}"
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "$ {v1}"
        }]
        ';
        TestCheck::assertInArray('A.7', 'Task::setParams(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${ v1}"
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "${ v1}"
        }]
        ';
        TestCheck::assertInArray('A.8', 'Task::setParams(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${v1 }"
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "${v1 }"
        }]
        ';
        TestCheck::assertInArray('A.9', 'Task::setParams(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "  ${v1}  "
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "  field1  "
        }]
        ';
        TestCheck::assertInArray('A.10', 'Task::setParams(); basic replacement of single variable; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "{v1}"
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "{v1}"
        }]
        ';
        TestCheck::assertInArray('A.11', 'Task::setParams(); variables need to follow format of ${}; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "$v1}"
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "$v1}"
        }]
        ';
        TestCheck::assertInArray('A.12', 'Task::setParams(); basic replacement of single variable; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${v1"
        }]
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "${v1"
        }]
        ';
        TestCheck::assertInArray('A.13', 'Task::setParams(); basic replacement of single variable; check for bad variable syntax', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${1}"
        }]
        ';
        $variables = [
            "1" => "test@flex.io"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "test@flex.io"
        }]
        ';
        TestCheck::assertInArray('A.14', 'Task::setParams(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${_1}"
        }]
        ';
        $variables = [
            "_1" => "test@flex.io"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "test@flex.io"
        }]
        ';
        TestCheck::assertInArray('A.15', 'Task::setParams(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${email-address-1}"
        }]
        ';
        $variables = [
            "email-address-1" => "test@flex.io"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "test@flex.io"
        }]
        ';
        TestCheck::assertInArray('A.16', 'Task::setParams(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${email_address_1}"
        }]
        ';
        $variables = [
            "email_address_1" => "test@flex.io"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "test@flex.io"
        }]
        ';
        TestCheck::assertInArray('A.17', 'Task::setParams(); basic replacement of single variable; variable names can be combinations of letters, numbers, underscores and hyphens', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${variable}"
        }]
        ';
        $variables = [
            "variable" => "'"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "\'"
        }]
        ';
        TestCheck::assertInArray('A.18', 'Task::setParams(); basic replacement of single variable; variable values with symbols', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${variable}"
        }]
        ';
        $variables = [
            'variable' => '${}'
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "${}"
        }]
        ';
        TestCheck::assertInArray('A.19', 'Task::setParams(); basic replacement of single variable; variable values with symbols', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${variable}"
        }]
        ';
        $variables = [
            'variable' => "\\"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "\\\\"
        }]
        ';
        TestCheck::assertInArray('A.20', 'Task::setParams(); basic replacement of single variable; variable values with symbols', $actual, $expected, $results);



        // TEST: Task::setParams(); variable serialization with variables of different types

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "different_value" => null
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": null
        }]
        ';
        TestCheck::assertInArray('B.1', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "value" => null
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": null
        }]
        ';
        TestCheck::assertInArray('B.2', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "different_value" => false
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": null
        }]
        ';
        TestCheck::assertInArray('B.3', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "value" => false
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": false
        }]
        ';
        TestCheck::assertInArray('B.4', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "value" => true
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": true
        }]
        ';
        TestCheck::assertInArray('B.5', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "different_value" => 10
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": null
        }]
        ';
        TestCheck::assertInArray('B.6', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "value" => 10
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": 10
        }]
        ';
        TestCheck::assertInArray('B.7', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "value" => -2.1
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": -2.1
        }]
        ';
        TestCheck::assertInArray('B.8', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "different_value" => [1,2,3]
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": null
        }]
        ';
        TestCheck::assertInArray('B.9', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "value" => [1,2,3]
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": [1,2,3]
        }]
        ';
        TestCheck::assertInArray('B.10', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "different_value" => ["a" => "b"]
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": null
        }]
        ';
        TestCheck::assertInArray('B.11', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}"
        }]
        ';
        $variables = [
            "value" => ["a" => "b"]
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": {"a": "b"}
        }]
        ';
        TestCheck::assertInArray('B.12', 'Task::setParams(); basic replacement of single variable; preserve type if variable is whole value', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "The value is: ${value} units."
        }]
        ';
        $variables = [
            "different_value" => 10
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "The value is:  units."
        }]
        ';
        TestCheck::assertInArray('B.13', 'Task::setParams(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "The value is: ${value} units."
        }]
        ';
        $variables = [
            "value" => 10
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "The value is: 10 units."
        }]
        ';
        TestCheck::assertInArray('B.14', 'Task::setParams(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "The statement is ${value}."
        }]
        ';
        $variables = [
            "different_value" => true
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "The statement is ."
        }]
        ';
        TestCheck::assertInArray('B.15', 'Task::setParams(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "The statement is ${value}."
        }]
        ';
        $variables = [
            "value" => true
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "The statement is true."
        }]
        ';
        TestCheck::assertInArray('B.16', 'Task::setParams(); basic replacement of single variable; convert variable value to a string if it\'s part of a string', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "The value is ${value}."
        }]
        ';
        $variables = [
            "value" => null
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "The value is ."
        }]
        ';
        TestCheck::assertInArray('B.17', 'Task::setParams(); basic replacement of single variable; convert variable value to a string if it\'s part of a string; nulls become empty', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${value}/${value} is 1"
        }]
        ';
        $variables = [
            "value" => 10
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "10/10 is 1"
        }]
        ';
        TestCheck::assertInArray('B.18', 'Task::setParams(); basic replacement of single variable; replace multiple values in a string', $actual, $expected, $results);



        // TEST: Task::setParams(); variable serialization with multiple variables

        // BEGIN TEST
        $task = '
        [{
            "params": [ "${v1}", "${v2}" ]
        }]
        ';
        $variables = [
            "v1" => "field1",
            "v2" => "field2"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": [ "field1", "field2" ]
        }]
        ';
        TestCheck::assertInArray('C.1', 'Task::setParams(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": [ "${v2}", "${v1}" ]
        }]
        ';
        $variables = [
            "v1" => "field1",
            "v2" => "field2"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": [ "field2", "field1" ]
        }]
        ';
        TestCheck::assertInArray('C.2', 'Task::setParams(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": [ "${v2}", "${v1}", "${v2}", "${v1}" ]
        }]
        ';
        $variables = [
            "v1" => "field1",
            "v2" => "field2"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": [ "field2", "field1", "field2", "field1" ]
        }]
        ';
        TestCheck::assertInArray('C.3', 'Task::setParams(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": [ "${v2}", "${v1}", "${v2}", "${v1}" ]
        }]
        ';
        $variables = [
            "v2" => "field2",
            "v3" => "field3"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": [ "field2", "${v1}", "field2", "${v1}" ]
        }]
        ';
        TestCheck::assertInArray('C.4', 'Task::setParams(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": {"${key}": "${value}"}
        }]
        ';
        $variables = [
            "key" => "a",
            "value" => "b"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": {"a": "b"}
        }]
        ';
        TestCheck::assertInArray('C.5', 'Task::setParams(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "The ${_1} is ${_2}."
        }]
        ';
        $variables = [
            "_1" => "key",
            "_2" => "value"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "The key is value."
        }]
        ';
        TestCheck::assertInArray('C.6', 'Task::setParams(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": "${_2} is ${_1}."
        }]
        ';
        $variables = [
            "_1" => "key",
            "_2" => "Value"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": "Value is key."
        }]
        ';
        TestCheck::assertInArray('C.7', 'Task::setParams(); basic replacement of two variables', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
        [{
            "params": {"a": "${var_01}", "b": "${var_02}"}
        }]
        ';
        $variables = [
            "var_01" => "A",
            "var_02" => "B"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
        $expected = '
        [{
            "params": {"a": "A", "b": "B"}
        }]
        ';
        TestCheck::assertInArray('C.8', 'Task::setParams(); basic replacement of two variables', $actual, $expected, $results);



        // TEST: Task::setParams(); variable serialization with multiple variables at varying object depths

        // BEGIN TEST
        $task = '
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
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
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
        TestCheck::assertInArray('D.1', 'Task::setParams(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
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
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
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
        TestCheck::assertInArray('D.2', 'Task::setParams(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
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
        ';
        $variables = [
            "v1" => "field1"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
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
        TestCheck::assertInArray('D.3', 'Task::setParams(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
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
        ';
        $variables = [
            "name" => "f1",
            "type" => "numeric",
            "width" => "14",
            "scale" => "4"
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
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
        TestCheck::assertInArray('D.4', 'Task::setParams(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
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
        ';
        $variables = [
            "name" => "f1",
            "type" => "text",
            "width" => null,
            "scale" => null
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
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
        TestCheck::assertInArray('D.5', 'Task::setParams(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);

        // BEGIN TEST
        $task = '
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
        ';
        $variables = [
            "name" => "f1",
            "type" => "numeric",
            "width" => 14,
            "scale" => 4
        ];
        $actual = \Flexio\Object\Task::create($task)->setParams($variables)->get();
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
        TestCheck::assertInArray('D.6', 'Task::setParams(); replace variables in params only based on case-sensitive, whole-variable-word match', $actual, $expected, $results);
    }
}
