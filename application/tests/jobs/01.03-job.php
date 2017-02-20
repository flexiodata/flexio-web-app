<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-08-15
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TEST: task creation

        // BEGIN TEST
        $task = CalcFieldJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = CalcFieldJob::MIME_TYPE;
        TestCheck::assertString('A.1', 'CalcFieldJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = ConvertJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = ConvertJob::MIME_TYPE;
        TestCheck::assertString('A.2', 'ConvertJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = CopyJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = CopyJob::MIME_TYPE;
        TestCheck::assertString('A.4', 'CopyJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = CreateJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = CreateJob::MIME_TYPE;
        TestCheck::assertString('A.5', 'CreateJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = DistinctJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = DistinctJob::MIME_TYPE;
        TestCheck::assertString('A.6', 'DistinctJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = DuplicateJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = DuplicateJob::MIME_TYPE;
        TestCheck::assertString('A.7', 'DuplicateJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = EmailSendJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = EmailSendJob::MIME_TYPE;
        TestCheck::assertString('A.8', 'EmailSendJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = FilterJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = FilterJob::MIME_TYPE;
        TestCheck::assertString('A.9', 'FilterJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = FindReplaceJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = FindReplaceJob::MIME_TYPE;
        TestCheck::assertString('A.10', 'FindReplaceJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = GrepJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = GrepJob::MIME_TYPE;
        TestCheck::assertString('A.11', 'GrepJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = GroupJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = GroupJob::MIME_TYPE;
        TestCheck::assertString('A.12', 'GroupJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = InputJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = InputJob::MIME_TYPE;
        TestCheck::assertString('A.13', 'InputJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = LimitJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = LimitJob::MIME_TYPE;
        TestCheck::assertString('A.14', 'LimitJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = MergeJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = MergeJob::MIME_TYPE;
        TestCheck::assertString('A.15', 'MergeJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = NopJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = NopJob::MIME_TYPE;
        TestCheck::assertString('A.16', 'NopJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = OutputJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = OutputJob::MIME_TYPE;
        TestCheck::assertString('A.17', 'OutputJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = PromptJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = PromptJob::MIME_TYPE;
        TestCheck::assertString('A.18', 'ExecuteJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = ExecuteJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = ExecuteJob::MIME_TYPE;
        TestCheck::assertString('A.19', 'ExecuteJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = RenameColumnJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = RenameColumnJob::MIME_TYPE;
        TestCheck::assertString('A.20', 'RenameColumnJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = RenameFileJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = RenameFileJob::MIME_TYPE;
        TestCheck::assertString('A.21', 'RenameFileJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = SearchJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = SearchJob::MIME_TYPE;
        TestCheck::assertString('A.22', 'SearchJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = SelectColumnJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = SelectColumnJob::MIME_TYPE;
        TestCheck::assertString('A.23', 'SelectColumnJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = SleepJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = SleepJob::MIME_TYPE;
        TestCheck::assertString('A.24', 'SleepJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = SortJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = SortJob::MIME_TYPE;
        TestCheck::assertString('A.25', 'SortJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = TransformJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = TransformJob::MIME_TYPE;
        TestCheck::assertString('A.26', 'TransformJob::create()',  $actual, $expected, $results);
    }
}
