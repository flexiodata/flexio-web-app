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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    private static function buildTask(string $data) : array
    {
        $task = \Flexio\Tests\Task::create([
            [
                "op" => "create",
                "name" => "csv",
                "content_type" => \Flexio\Base\ContentType::CSV,
                "content" => base64_encode(trim($data))
            ],
            [
                "op" => "convert",
                "input" => [
                    "format" => "delimited_text",
                    "delimiter" => "{comma}",
                    "header_row" => true,
                    "text_qualifier" => "{double_quote}"
                ]
            ]
        ]);
        return $task;
    }

    public function run(&$results)
    {
        // TEST: Convert; single, valid fieldname

        // BEGIN TEST
        $data = '
            "field1"
            "a2"
            "a3"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field1"]';
        \Flexio\Tests\Check::assertArray('A.1', 'Convert Job; valid fieldname first row should create correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "f1"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1"]';
        \Flexio\Tests\Check::assertArray('A.2', 'Convert Job; valid fieldname first row should create correctly',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            f1
            a1
            a2
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["f1"]';
        \Flexio\Tests\Check::assertArray('A.3', 'Convert Job; valid fieldname first row should create correctly',  $actual, $expected, $results);



        // TEST: Convert; single fieldname with leading/trailing spaces

        // BEGIN TEST
        $data = '
            "  field1"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field1"]';
        \Flexio\Tests\Check::assertArray('B.1', 'Convert Job; leading spaces in a fieldname should be trimmed',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "field1   "
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field1"]';
        \Flexio\Tests\Check::assertArray('B.2', 'Convert Job; trailing spaces in a fieldname should be trimmed',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
               field1
            a1
            a2
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field1"]';
        \Flexio\Tests\Check::assertArray('B.3', 'Convert Job; leading and trailing spaces in a fieldname should be trimmed',  $actual, $expected, $results);



        // TEST: Convert; single fieldname with embedded spaces

        // BEGIN TEST
        $data = '
            "field 1"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field 1"]';
        \Flexio\Tests\Check::assertArray('C.1', 'Convert Job; embedded spaces should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            " field  1 "
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field  1"]';
        \Flexio\Tests\Check::assertArray('C.2', 'Convert Job; embedded spaces should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            field  1
            a1
            a2
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field  1"]';
        \Flexio\Tests\Check::assertArray('C.3', 'Convert Job; embedded spaces should be preserved',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "field one   and  the   same"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field one   and  the   same"]';
        \Flexio\Tests\Check::assertArray('C.4', 'Convert Job; embedded spaces should be preserved',  $actual, $expected, $results);



        // TEST: Convert; single fieldname with uppercase characters

        // BEGIN TEST
        $data = '
            "Field1"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field1"]';
        \Flexio\Tests\Check::assertArray('D.1', 'Convert Job; uppercase characters should be converted to lowercase',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "FIELD1"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field1"]';
        \Flexio\Tests\Check::assertArray('D.2', 'Convert Job; uppercase characters should be converted to lowercase',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "HTTPResponseCode"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["httpresponsecode"]';
        \Flexio\Tests\Check::assertArray('D.3', 'Convert Job; camelcase should be converted to lowercase',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "HTTP_Response_Code"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["http_response_code"]';
        \Flexio\Tests\Check::assertArray('D.4', 'Convert Job; camelcase should be converted to lowercase',  $actual, $expected, $results);



        // TEST: Convert; embedded symbols

        // BEGIN TEST
        $data = '
            "field_#"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field_#"]';
        \Flexio\Tests\Check::assertArray('E.1', 'Convert Job; # should be converted to alphanumeric abbreviation',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "% Total"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["% total"]';
        \Flexio\Tests\Check::assertArray('E.2', 'Convert Job; % should be converted to alphanumeric abbreviation',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "% (Of Total Amount)"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["% (of total amount)"]';
        \Flexio\Tests\Check::assertArray('E.3', 'Convert Job; paranthesis, braces, and brackets should be removed',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "Amount (Total)"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["amount (total)"]';
        \Flexio\Tests\Check::assertArray('E.4', 'Convert Job; paranthesis, braces, and brackets should be removed',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "field.name"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field.name"]';
        \Flexio\Tests\Check::assertArray('E.5', 'Convert Job; special characters in field name',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "123"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["123"]';
        \Flexio\Tests\Check::assertArray('E.6', 'Convert Job; special characters in field name',  $actual, $expected, $results);



        // TEST: Convert; keywords are allowed for now; TODO: behavior we want?

        // BEGIN TEST
        $data = '
            "select"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["select"]';
        \Flexio\Tests\Check::assertArray('F.1', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "update"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["update"]';
        \Flexio\Tests\Check::assertArray('F.2', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "delete"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["delete"]';
        \Flexio\Tests\Check::assertArray('F.3', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "where"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["where"]';
        \Flexio\Tests\Check::assertArray('F.4', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "true"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["true"]';
        \Flexio\Tests\Check::assertArray('F.5', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "false"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["false"]';
        \Flexio\Tests\Check::assertArray('F.6', 'Convert Job; check keyword',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "null"
            "a1"
            "a2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["null"]';
        \Flexio\Tests\Check::assertArray('F.7', 'Convert Job; check keyword',  $actual, $expected, $results);



        // TEST: Convert; multiple fieldnames

        // BEGIN TEST
        $data = '
            "field1","field2"
            "a1","b1"
            "a2","b2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["field1","field2"]';
        \Flexio\Tests\Check::assertArray('G.1', 'Convert Job; multiple fieldnames',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "Order #","Order (Total)"
            "a1","b1"
            "a2","b2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["order #","order (total)"]';
        \Flexio\Tests\Check::assertArray('G.2', 'Convert Job; multiple fieldnames',  $actual, $expected, $results);



        // TEST: Convert; duplicate fieldnames should be enumerated to avoid duplication

        // BEGIN TEST
        $data = '
            "id","id"
            "a1","b1"
            "a2","b2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["id","id_1"]';
        \Flexio\Tests\Check::assertArray('H.1', 'Convert Job; duplicate fieldnames should be enumerated to avoid duplication',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "Order #","order #"
            "a1","b1"
            "a2","b2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["order #","order #_1"]';
        \Flexio\Tests\Check::assertArray('H.2', 'Convert Job; duplicate fieldnames should be enumerated to avoid duplication',  $actual, $expected, $results);

        // BEGIN TEST
        $data = '
            "count","count"
            "a1","b1"
            "a2","b2"
        ';
        $process = \Flexio\Jobs\Process::create()->execute(self::buildTask($data));
        $actual = $process->getStdout()->getStructure()->getNames();
        $expected = '["count","count_1"]';
        \Flexio\Tests\Check::assertArray('H.3', 'Convert Job; duplicate fieldnames should be enumerated to avoid duplication',  $actual, $expected, $results);
    }
}
