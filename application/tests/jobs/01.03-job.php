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


declare(strict_types=1);
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
        $task = \Flexio\Jobs\Comment::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Comment::MIME_TYPE;
        TestCheck::assertString('A.2', '\Flexio\Jobs\Comment::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Convert::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Convert::MIME_TYPE;
        TestCheck::assertString('A.3', '\Flexio\Jobs\Convert::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Create::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Create::MIME_TYPE;
        TestCheck::assertString('A.4', '\Flexio\Jobs\Create::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Distinct::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Distinct::MIME_TYPE;
        TestCheck::assertString('A.5', '\Flexio\Jobs\Distinct::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Duplicate::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Duplicate::MIME_TYPE;
        TestCheck::assertString('A.6', '\Flexio\Jobs\Duplicate::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\EmailSend::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\EmailSend::MIME_TYPE;
        TestCheck::assertString('A.7', '\Flexio\Jobs\EmailSend::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Fail::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Fail::MIME_TYPE;
        TestCheck::assertString('A.8', '\Flexio\Jobs\Fail::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Filter::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Filter::MIME_TYPE;
        TestCheck::assertString('A.9', '\Flexio\Jobs\Filter::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\HttpRequest::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\HttpRequest::MIME_TYPE;
        TestCheck::assertString('A.10', '\Flexio\Jobs\HttpRequest::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Replace::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Replace::MIME_TYPE;
        TestCheck::assertString('A.11', '\Flexio\Jobs\Replace::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Grep::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Grep::MIME_TYPE;
        TestCheck::assertString('A.12', '\Flexio\Jobs\Grep::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Group::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Group::MIME_TYPE;
        TestCheck::assertString('A.13', '\Flexio\Jobs\Group::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Input::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Input::MIME_TYPE;
        TestCheck::assertString('A.14', '\Flexio\Jobs\Input::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Limit::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Limit::MIME_TYPE;
        TestCheck::assertString('A.15', '\Flexio\Jobs\Limit::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Merge::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Merge::MIME_TYPE;
        TestCheck::assertString('A.16', '\Flexio\Jobs\Merge::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Nop::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Nop::MIME_TYPE;
        TestCheck::assertString('A.17', '\Flexio\Jobs\Nop::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Output::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Output::MIME_TYPE;
        TestCheck::assertString('A.18', '\Flexio\Jobs\Output::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Prompt::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Prompt::MIME_TYPE;
        TestCheck::assertString('A.19', '\Flexio\Jobs\Prompt::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Execute::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Execute::MIME_TYPE;
        TestCheck::assertString('A.20', '\Flexio\Jobs\Execute::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Rename::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Rename::MIME_TYPE;
        TestCheck::assertString('A.21', '\Flexio\Jobs\Rename::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Search::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Search::MIME_TYPE;
        TestCheck::assertString('A.22', '\Flexio\Jobs\Search::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Select::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Select::MIME_TYPE;
        TestCheck::assertString('A.23', '\Flexio\Jobs\Select::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\SetType::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\SetType::MIME_TYPE;
        TestCheck::assertString('A.24', '\Flexio\Jobs\SetType::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Sleep::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Sleep::MIME_TYPE;
        TestCheck::assertString('A.25', '\Flexio\Jobs\Sleep::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Sort::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Sort::MIME_TYPE;
        TestCheck::assertString('A.26', '\Flexio\Jobs\Sort::create()',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Jobs\Transform::create();
        $actual = ($task !== false ? $task->getType() : false);
        $expected = \Flexio\Jobs\Transform::MIME_TYPE;
        TestCheck::assertString('A.27', '\Flexio\Jobs\Transform::create()',  $actual, $expected, $results);
    }
}
