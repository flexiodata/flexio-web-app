<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-08
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
        $task = json_decode('
        [
            {
                "type": "flexio.create",
                "params": {
                    "mime_type": "'.\Flexio\Base\ContentType::MIME_TYPE_CSV.'",
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
        ',true);



        // TEST: Convert; single, valid fieldname

        // BEGIN TEST
        $data = '
            "field1"
            "a2"
            "a3"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field1"]';
        TestCheck::assertArray('A.1', 'Convert Job; valid fieldname first row should create correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "f1"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["f1"]';
        TestCheck::assertArray('A.2', 'Convert Job; valid fieldname first row should create correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            f1
            a1
            a2
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["f1"]';
        TestCheck::assertArray('A.3', 'Convert Job; valid fieldname first row should create correctly',  $actual, $expected, $results);



        // TEST: Convert; single fieldname with leading/trailing spaces

        // BEGIN TEST
        $data = '
            "  field1"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field1"]';
        TestCheck::assertArray('B.1', 'Convert Job; leading spaces in a fieldname should be trimmed',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "field1   "
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field1"]';
        TestCheck::assertArray('B.2', 'Convert Job; trailing spaces in a fieldname should be trimmed',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
               field1
            a1
            a2
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field1"]';
        TestCheck::assertArray('B.3', 'Convert Job; leading and trailing spaces in a fieldname should be trimmed',  $actual, $expected, $results);



        // TEST: Convert; single fieldname with embedded spaces

        // BEGIN TEST
        $data = '
            "field 1"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field 1"]';
        TestCheck::assertArray('C.1', 'Convert Job; embedded spaces should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            " field  1 "
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field  1"]';
        TestCheck::assertArray('C.2', 'Convert Job; embedded spaces should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            field  1
            a1
            a2
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field  1"]';
        TestCheck::assertArray('C.3', 'Convert Job; embedded spaces should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "field one   and  the   same"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field one   and  the   same"]';
        TestCheck::assertArray('C.4', 'Convert Job; embedded spaces should be preserved',  $actual, $expected, $results);



        // TEST: Convert; single fieldname with uppercase characters

        // BEGIN TEST
        $data = '
            "Field1"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field1"]';
        TestCheck::assertArray('D.1', 'Convert Job; uppercase characters should be converted to lowercase',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "FIELD1"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field1"]';
        TestCheck::assertArray('D.2', 'Convert Job; uppercase characters should be converted to lowercase',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "HTTPResponseCode"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["httpresponsecode"]';
        TestCheck::assertArray('D.3', 'Convert Job; camelcase should be converted to lowercase',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "HTTP_Response_Code"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["http_response_code"]';
        TestCheck::assertArray('D.4', 'Convert Job; camelcase should be converted to lowercase',  $actual, $expected, $results);



        // TEST: Convert; embedded symbols

        // BEGIN TEST
        $data = '
            "field_#"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field_#"]';
        TestCheck::assertArray('E.1', 'Convert Job; # should be converted to alphanumeric abbreviation',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "% Total"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["% total"]';
        TestCheck::assertArray('E.2', 'Convert Job; % should be converted to alphanumeric abbreviation',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "% (Of Total Amount)"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["% (of total amount)"]';
        TestCheck::assertArray('E.3', 'Convert Job; paranthesis, braces, and brackets should be removed',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "Amount (Total)"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["amount (total)"]';
        TestCheck::assertArray('E.4', 'Convert Job; paranthesis, braces, and brackets should be removed',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "field.name"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field.name"]';
        TestCheck::assertArray('E.5', 'Convert Job; special characters in field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "123"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["123"]';
        TestCheck::assertArray('E.6', 'Convert Job; special characters in field name',  $actual, $expected, $results);



        // TEST: Convert; keywords are allowed for now; TODO: behavior we want?

        // BEGIN TEST
        $data = '
            "select"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["select"]';
        TestCheck::assertArray('F.1', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "update"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["update"]';
        TestCheck::assertArray('F.2', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "delete"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["delete"]';
        TestCheck::assertArray('F.3', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "where"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["where"]';
        TestCheck::assertArray('F.4', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "true"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["true"]';
        TestCheck::assertArray('F.5', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "false"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["false"]';
        TestCheck::assertArray('F.6', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "null"
            "a1"
            "a2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["null"]';
        TestCheck::assertArray('F.7', 'Convert Job; check keyword',  $actual, $expected, $results);



        // TEST: Convert; multiple fieldnames

        // BEGIN TEST
        $data = '
            "field1","field2"
            "a1","b1"
            "a2","b2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["field1","field2"]';
        TestCheck::assertArray('G.1', 'Convert Job; multiple fieldnames',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "Order #","Order (Total)"
            "a1","b1"
            "a2","b2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["order #","order (total)"]';
        TestCheck::assertArray('G.2', 'Convert Job; multiple fieldnames',  $actual, $expected, $results);



        // TEST: Convert; duplicate fieldnames should be enumerated to avoid duplication

        // BEGIN TEST
        $data = '
            "id","id"
            "a1","b1"
            "a2","b2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["id","id_1"]';
        TestCheck::assertArray('H.1', 'Convert Job; duplicate fieldnames should be enumerated to avoid duplication',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "Order #","order #"
            "a1","b1"
            "a2","b2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["order #","order #_1"]';
        TestCheck::assertArray('H.2', 'Convert Job; duplicate fieldnames should be enumerated to avoid duplication',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "count","count"
            "a1","b1"
            "a2","b2"
        ';
        $params = [
            "data" => base64_encode(trim($data)),
            "format" => "delimited_text",
            "delimiter" => "{comma}",
            "header" => "true",
            "qualifier" => "{double_quote}"
        ];
        $process = \Flexio\Object\Process::create()->setTask($task)->setParams($params)->run(false);
        $actual = TestUtil::getProcessSingleOutputColumnNameResult($process);
        $expected = '["count","count_1"]';
        TestCheck::assertArray('H.3', 'Convert Job; duplicate fieldnames should be enumerated to avoid duplication',  $actual, $expected, $results);
    }
}
