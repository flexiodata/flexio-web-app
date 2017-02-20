<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-30
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: make sure there are no errors in the job schemas

        // BEGIN TEST
        $schema = CalcFieldJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.1', 'CalcFieldJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = ConvertJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.2', 'ConvertJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = CopyJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.4', 'CopyJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = CreateJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.5', 'CreateJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = DistinctJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.6', 'DistinctJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = DuplicateJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.7', 'DuplicateJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = EmailSendJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.8', 'EmailSendJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = FilterJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.9', 'FilterJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = FindReplaceJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.10', 'FindReplaceJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = GrepJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.11', 'GrepJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = GroupJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.12', 'GroupJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = InputJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.13', 'InputJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = LimitJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.14', 'LimitJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = MergeJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.15', 'MergeJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = NopJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.16', 'NopJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = OutputJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.17', 'OutputJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = ExecuteJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.18', 'ExecuteJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = PromptJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.19', 'PromptJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = RenameColumnJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.20', 'RenameColumnJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = RenameFileJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.21', 'RenameFileJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = SearchJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.22', 'SearchJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = SelectColumnJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.23', 'SelectColumnJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = SleepJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.24', 'SleepJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = SortJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.25', 'SortJob schema format',  $actual, $expected, $results);

        // BEGIN TEST
        $schema = TransformJob::SCHEMA;
        $actual = ValidatorSchema::checkSchema($schema)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.26', 'TransformJob schema format',  $actual, $expected, $results);
    }
}
