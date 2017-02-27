<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-23
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
        $task = \Flexio\Object\Task::create('
        [
            {
                "type": "flexio.create",
                "params": {
                    "content": "${data}"
                }
            },
            {
                "type": "flexio.convert",
                "params": {
                    "input": {
                        "format": "${format}",
                        "delimiter": "${delimiter}",
                        "header_row": "${header}",
                        "text_qualifier": "${qualifier}"
                    }
                }
            }
        ]
        ')->get();



        // TEST: ConvertJob; basic content upload test

        // BEGIN TEST
        $data = <<<EOD
"field1", "field2"
"a1", "b1"
"a2", "b2"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);

        // note: this line uses excel's rules
        //$expected = '[["a1"," \\"b1\\""],["a2"," \\"b2\\""]]';

        // note: this line uses php's fgetcsv rules
        $expected = '[["a1","b1"],["a2","b2"]]';
        TestCheck::assertArray('A.1', 'Convert Job; basic content upload test',  $actual, $expected, $results);



        // TEST: ConvertJob; content with troublesome characters

        // BEGIN TEST
        $data = <<<EOD
"field1", "field2"
"", ","
"b,", ",b"
"c,d", ","
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);

        // note: this line uses excel's rules
        //$expected = '[[""," \\",\\""],["b,"," \\",b\\""],["c,d"," \\",\\""]]';

        // note: This line uses php's fgetcsv rules
        $expected = '[["",","],["b,",",b"],["c,d",","]]';
        TestCheck::assertArray('B.1', 'ConvertJob; troublesome characters in content',  $actual, $expected, $results);



        // TEST: CSV Upload Data; content with unicode; note: following tests try
        // various types of unicode combinations based on original problem documented
        // in issue:FLEX-21

        // BEGIN TEST
        $data = <<<EOD
vend_name
SchwÃ¤bische SoftwarelÃ¶sungen AG
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["SchwÃ¤bische SoftwarelÃ¶sungen AG"]]';
        TestCheck::assertArray('C.1', 'ConvertJob; troublesome characters in content',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "vend_name"
            "SchwÃ¤bische SoftwarelÃ¶sungen AG"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["SchwÃ¤bische SoftwarelÃ¶sungen AG"]]';
        TestCheck::assertArray('C.2', 'ConvertJob; troublesome characters in content',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"vend_name"
"SchwÃ¤bische SoftwarelÃ¶sungen AG"
EOD;
        $data = mb_convert_encoding($data, 'UTF-8', 'UTF-16');
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["SchwÃ¤bische SoftwarelÃ¶sungen AG"]]';
        TestCheck::assertArray('C.3', 'ConvertJob; troublesome characters in content',  $actual, $expected, $results);



        // TEST: ConvertJob; content with integer numbers

        // BEGIN TEST
        $data = <<<EOD
"field1"
0
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["0"]]';
        TestCheck::assertArray('D.1', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "field1"
            1
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1"]]';
        TestCheck::assertArray('D.2', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-1
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["-1"]]';
        TestCheck::assertArray('D.3', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
9999
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["9999"]]';
        TestCheck::assertArray('D.4', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-9999
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["-9999"]]';
        TestCheck::assertArray('D.5', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
999999999999999
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["999999999999999"]]';
        TestCheck::assertArray('D.6', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-999999999999999
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["-999999999999999"]]';
        TestCheck::assertArray('D.7', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
9999999999999999
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["9999999999999999"]]';
        TestCheck::assertArray('D.8', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-9999999999999999
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["-9999999999999999"]]';
        TestCheck::assertArray('D.9', 'Convert Job; numeric range check',  $actual, $expected, $results);



        // TEST: ConvertJob; content with decimal numbers

        // BEGIN TEST
        $data = <<<EOD
"field1"
0.0
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["0.0"]]';
        TestCheck::assertArray('E.1', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
0.00
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["0.00"]]';
        TestCheck::assertArray('E.2', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
0.0001
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["0.0001"]]';
        TestCheck::assertArray('E.3', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-0.0001
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["-0.0001"]]';
        TestCheck::assertArray('E.4', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
0.000000000001
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["0.000000000001"]]';
        TestCheck::assertArray('E.5', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
-0.000000000001
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["-0.000000000001"]]';
        TestCheck::assertArray('E.6', 'Convert Job; numeric range check',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
1.1
0.0001
-0.0001
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1.1000"],["0.0001"],["-0.0001"]]';
        TestCheck::assertArray('E.7', 'Convert Job; numeric range check',  $actual, $expected, $results);



        // TEST: ConvertJob; troublesome characters in a numeric field

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1.1"
"0.0001"
"  0  "
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1.1"],["0.0001"],["  0  "]]';
        TestCheck::assertArray('F.1', 'Convert Job; if non-numeric characters are present, keep field in character format',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1.1"
"0.0001"
"123,456.78"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1.1"],["0.0001"],["123,456.78"]]';
        TestCheck::assertArray('F.2', 'Convert Job; if non-numeric characters are present, keep field in character format',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"0"
"0"
"1776-07-04"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["0"],["0"],["1776-07-04"]]';
        TestCheck::assertArray('F.3', 'Convert Job; if dates are mixed in with numbers don\'t confuse them for numerics',  $actual, $expected, $results);



        // TEST: ConvertJob; content with dates, single format

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1999-12-31"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1999-12-31"]]';
        TestCheck::assertArray('G.1', 'Convert Job; valid date values should load',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"2001-01-01"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["2001-01-01"]]';
        TestCheck::assertArray('G.2', 'Convert Job; valid date values should load',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1999/12/31"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1999-12-31"]]';
        TestCheck::assertArray('G.3', 'Convert Job; valid date values should load',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"2001/01/01"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["2001-01-01"]]';
        TestCheck::assertArray('G.4', 'Convert Job; valid date values should load',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"19991231"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1999-12-31"]]';
        TestCheck::assertArray('G.5', 'Convert Job; valid date values should load',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"20010101"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["2001-01-01"]]';
        TestCheck::assertArray('G.6', 'Convert Job; valid date values should load',  $actual, $expected, $results);



        // TEST: ConvertJob; content with dates, mixed format

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1969-07-20"],["1970-01-01"]]';
        TestCheck::assertArray('H.1', 'Convert Job; valid date values should load if the format is recognized',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
"0"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1969-07-20"],["19700101"],["0"]]';
        TestCheck::assertArray('H.2', 'Convert Job; mixed date values with blank or zero values should end up as character fields',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"19690720"
19700101
0
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["19690720"],["19700101"],["0"]]';
        TestCheck::assertArray('H.3', 'Convert Job; mixed date values with zero values should end up as numeric',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
"false"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1969-07-20"],["19700101"],["false"]]';
        TestCheck::assertArray('H.4', 'Convert Job; mixed date values with invalid date values should be imported as characters',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
"January"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1969-07-20"],["19700101"],["January"]]';
        TestCheck::assertArray('H.5', 'Convert Job; mixed date values with values that can\'t be interpreted correctly should be imported as characters',  $actual, $expected, $results);

        // BEGIN TEST
        $data = <<<EOD
"field1"
"1969-07-20"
"19700101"
"1/89"
EOD;
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputRowResult($process);
        $expected = '[["1969-07-20"],["19700101"],["1/89"]]';
        TestCheck::assertArray('H.6', 'Convert Job; mixed date values with values that can\'t be interpreted correctly should be imported as characters',  $actual, $expected, $results);
    }
}
