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
        $task = \Flexio\Jobs\Input::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Input::MIME_TYPE;
        TestCheck::assertString('A.13', '\Flexio\Jobs\Input::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Limit::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Limit::MIME_TYPE;
        TestCheck::assertString('A.14', '\Flexio\Jobs\Limit::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Merge::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Merge::MIME_TYPE;
        TestCheck::assertString('A.15', '\Flexio\Jobs\Merge::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Nop::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Nop::MIME_TYPE;
        TestCheck::assertString('A.16', '\Flexio\Jobs\Nop::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Output::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Output::MIME_TYPE;
        TestCheck::assertString('A.17', '\Flexio\Jobs\Output::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Prompt::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Prompt::MIME_TYPE;
        TestCheck::assertString('A.18', '\Flexio\Jobs\Prompt::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Execute::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Execute::MIME_TYPE;
        TestCheck::assertString('A.19', '\Flexio\Jobs\Execute::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\RenameColumn::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\RenameColumn::MIME_TYPE;
        TestCheck::assertString('A.20', '\Flexio\Jobs\RenameColumn::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\RenameFile::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\RenameFile::MIME_TYPE;
        TestCheck::assertString('A.21', '\Flexio\Jobs\RenameFile::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Search::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Search::MIME_TYPE;
        TestCheck::assertString('A.22', '\Flexio\Jobs\Search::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\SelectColumn::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\SelectColumn::MIME_TYPE;
        TestCheck::assertString('A.23', '\Flexio\Jobs\SelectColumn::create()',  $actual, $expected, $results);

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
