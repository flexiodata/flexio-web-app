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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: task creation

        // BEGIN TEST
        $task = \Flexio\Jobs\CalcField::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\CalcField::MIME_TYPE;
        TestCheck::assertString('A.1', '\Flexio\Jobs\CalcField::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Convert::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Convert::MIME_TYPE;
        TestCheck::assertString('A.2', '\Flexio\Jobs\Convert::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Copy::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Copy::MIME_TYPE;
        TestCheck::assertString('A.4', '\Flexio\Jobs\Copy::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Create::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Create::MIME_TYPE;
        TestCheck::assertString('A.5', '\Flexio\Jobs\Create::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Distinct::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Distinct::MIME_TYPE;
        TestCheck::assertString('A.6', '\Flexio\Jobs\Distinct::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Duplicate::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Duplicate::MIME_TYPE;
        TestCheck::assertString('A.7', '\Flexio\Jobs\Duplicate::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\EmailSend::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\EmailSend::MIME_TYPE;
        TestCheck::assertString('A.8', '\Flexio\Jobs\EmailSend::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Filter::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Filter::MIME_TYPE;
        TestCheck::assertString('A.9', '\Flexio\Jobs\Filter::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\FindReplace::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\FindReplace::MIME_TYPE;
        TestCheck::assertString('A.10', '\Flexio\Jobs\FindReplace::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Grep::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Grep::MIME_TYPE;
        TestCheck::assertString('A.11', '\Flexio\Jobs\Grep::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Group::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Group::MIME_TYPE;
        TestCheck::assertString('A.12', '\Flexio\Jobs\Group::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\InputJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\InputJob::MIME_TYPE;
        TestCheck::assertString('A.13', '\Flexio\Jobs\InputJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\LimitJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\LimitJob::MIME_TYPE;
        TestCheck::assertString('A.14', '\Flexio\Jobs\LimitJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\MergeJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\MergeJob::MIME_TYPE;
        TestCheck::assertString('A.15', '\Flexio\Jobs\MergeJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\NopJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\NopJob::MIME_TYPE;
        TestCheck::assertString('A.16', '\Flexio\Jobs\NopJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\OutputJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\OutputJob::MIME_TYPE;
        TestCheck::assertString('A.17', '\Flexio\Jobs\OutputJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\PromptJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\PromptJob::MIME_TYPE;
        TestCheck::assertString('A.18', '\Flexio\Jobs\PromptJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Execute::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Execute::MIME_TYPE;
        TestCheck::assertString('A.19', '\Flexio\Jobs\Execute::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\RenameColumnJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\RenameColumnJob::MIME_TYPE;
        TestCheck::assertString('A.20', '\Flexio\Jobs\RenameColumnJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\RenameFileJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\RenameFileJob::MIME_TYPE;
        TestCheck::assertString('A.21', '\Flexio\Jobs\RenameFileJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\SearchJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\SearchJob::MIME_TYPE;
        TestCheck::assertString('A.22', '\Flexio\Jobs\SearchJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\SelectColumnJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\SelectColumnJob::MIME_TYPE;
        TestCheck::assertString('A.23', '\Flexio\Jobs\SelectColumnJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\SleepJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\SleepJob::MIME_TYPE;
        TestCheck::assertString('A.24', '\Flexio\Jobs\SleepJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\SortJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\SortJob::MIME_TYPE;
        TestCheck::assertString('A.25', '\Flexio\Jobs\SortJob::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\TransformJob::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\TransformJob::MIME_TYPE;
        TestCheck::assertString('A.26', '\Flexio\Jobs\TransformJob::create()',  $actual, $expected, $results);
    }
}
