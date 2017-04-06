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
        $model = TestUtil::getModel();


        // TEST: Task::create(); task creation

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('A.1', 'Task::create(); create an empty task array if no input is specified', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $task = \Flexio\Object\Task::create(true);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('A.2', 'Task::create(); if input properties are specified, make sure they are valid', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $task = \Flexio\Object\Task::create([1]);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        TestCheck::assertString('A.3', 'Task::create(); if input properties are specified, make sure they are valid', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([array("a"=>"b")]);
        $actual = is_string($task->get()[0]["eid"]);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Task::create(); make sure eid is added to a task step', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([array("a"=>"b"),array("c"=>"d")]);
        $actual = is_string($task->get()[1]["eid"]);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Task::create(); make sure eid is added to a task step', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ]);
        $actual = $task->get()[0]['type'] === \Flexio\Jobs\Create::MIME_TYPE && $task->get()[1]['type'] === \Flexio\Jobs\Convert::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('A.6', 'Task::create(); add valid steps; make sure parameters are set', $actual, $expected, $results);



        // TEST: Task::push(); tests for adding a step

        // BEGIN TEST
        $actual = '';
        try
        {
            $task = \Flexio\Object\Task::create()->push(null);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.1', 'Task::push(); if input properties are specified, make sure they are valid', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $task = \Flexio\Object\Task::create()->push(true);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('B.2', 'Task::push(); if input properties are specified, make sure they are valid', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push([]);
        $actual = is_string($task->get()[0]["eid"]);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Task::push(); make sure eid is added to a task step', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push([1]);
        $actual = $task->get()[0]['type'] ?? false;
        $expected = false;
        TestCheck::assertBoolean('B.4', 'Task::push(); add valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get()[0]['type'];
        $expected = \Flexio\Jobs\Create::MIME_TYPE;
        TestCheck::assertString('B.5', 'Task::push(); add valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                                ->push(["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]])
                                ->push(["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get()[0]['type'] === \Flexio\Jobs\Create::MIME_TYPE && $task->get()[1]['type'] === \Flexio\Jobs\Convert::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('B.6', 'Task::push(); add valid steps', $actual, $expected, $results);



        // TEST: Task::pop(); tests for removing the last step

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->pop();
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('C.1', 'Task::pop(); don\'t fail if there are no elements to remove', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push(["type" => \Flexio\Jobs\Create::MIME_TYPE])
                        ->push(["type" => \Flexio\Jobs\Convert::MIME_TYPE])
                        ->push(["type" => \Flexio\Jobs\Filter::MIME_TYPE]);
        $task_list = $task->pop()->get();
        $actual = end($task_list)['type'];
        $expected = \Flexio\Jobs\Convert::MIME_TYPE;
        TestCheck::assertString('C.2', 'Task::pop(); remove the last element', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push(["type" => \Flexio\Jobs\Create::MIME_TYPE])
                        ->push(["type" => \Flexio\Jobs\Convert::MIME_TYPE])
                        ->push(["type" => \Flexio\Jobs\Filter::MIME_TYPE]);
        $task_list = $task->pop()->pop()->get();
        $actual = end($task_list)['type'];
        $expected = \Flexio\Jobs\Create::MIME_TYPE;
        TestCheck::assertString('C.3', 'Task::pop(); remove the last element', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push(["type" => \Flexio\Jobs\Create::MIME_TYPE])
                        ->push(["type" => \Flexio\Jobs\Convert::MIME_TYPE])
                        ->push(["type" => \Flexio\Jobs\Filter::MIME_TYPE]);
        $task->pop()->pop()->pop();
        $actual = $task->get();
        $expected = array();
        TestCheck::assertArray('C.4', 'Task::pop(); remove the last element', $actual, $expected, $results);



        // TEST: Task::addTaskStep(); tests for adding a step

        // BEGIN TEST
        $actual = '';
        try
        {
            $task = \Flexio\Object\Task::create();
            $task->addTaskStep(null);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('D.1', 'Task::addTaskStep(); if input properties are specified, make sure they are valid', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $task = \Flexio\Object\Task::create();
            $task->addTaskStep('abc');
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('D.2', 'Task::addTaskStep(); if input properties are specified, make sure they are valid', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.3', 'Task::addTaskStep(); add steps that are in the correct format', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]);
        $result = $task->get();
        $actual = count($result) === 1 && isset($result[0]['eid']) && \Flexio\Base\Eid::isValid($result[0]['eid']);
        $expected = true;
        TestCheck::assertBoolean('D.4', 'Task::addTaskStep(); when a step is added, make sure the eid is set', $actual, $expected, $results);

        // BEGIN TEST
        $eid1 = \Flexio\Base\Eid::generate();
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["eid" => $eid1, "type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]);
        $result = $task->get();
        $actual = count($result) === 1 && isset($result[0]['eid']) && \Flexio\Base\Eid::isValid($result[0]['eid']) && $result[0]['eid'] === $eid1;
        $expected = true;
        TestCheck::assertBoolean('D.5', 'Task::addTaskStep(); add steps that are in the correct format; valid eids that aren\'t objects in the application are allowed to be set', $actual, $expected, $results);

        // BEGIN TEST
        $eid1 = 'xyz'; // bad eid
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["eid" => $eid1, "type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]);
        $result = $task->get();
        $actual = count($result) === 1 && isset($result[0]['eid']) && \Flexio\Base\Eid::isValid($result[0]['eid']) && $result[0]['eid'] !== $eid1;
        $expected = true;
        TestCheck::assertBoolean('D.6', 'Task::addTaskStep(); add steps that are in the correct format; if an eid is supplied, make sure it\'s valid and isn\'t an object in the application, or else generate a new one', $actual, $expected, $results);

        // BEGIN TEST
        $eid1 = \Flexio\Object\Comment::create()->getEid(); // eid corresponding to an object
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["eid" => $eid1, "type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]);
        $result = $task->get();
        $actual = count($result) === 1 && isset($result[0]['eid']) && \Flexio\Base\Eid::isValid($eid1) && \Flexio\Base\Eid::isValid($result[0]['eid']) && $result[0]['eid'] !== $eid1;
        $expected = true;
        TestCheck::assertBoolean('D.7', 'Task::addTaskStep(); add steps that are in the correct format; if an eid is supplied, make sure it\'s valid and isn\'t an object in the application, or else generate a new one', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]);
        $task->addTaskStep(["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.8', 'Task::addTaskStep(); make sure that steps without any supplied index are added to the end of the task list', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]);
        $task->addTaskStep(["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.9', 'Task::addTaskStep(); make sure that steps without any supplied index are added to the end of the task list', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.10', 'Task::addTaskStep(); make sure that steps without any supplied index are added to the end of the task list', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], null);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.11', 'Task::addTaskStep(); make sure that steps with unsupplied indexes are inserted at the end', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $steps = [
                    ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                    ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ];
            $task = \Flexio\Object\Task::create($steps);
            $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], 'a');
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('D.12', 'Task::addTaskStep(); make sure that steps with bad indexes insert steps at the end', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], -1);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.13', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], 0);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.14', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], 1);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.15', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]], 0);
        $task->addTaskStep(["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]], 0);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.16', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]], 0);
        $task->addTaskStep(["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]], 1);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.17', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], -1);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.18', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], 0);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.19', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], 1);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.20', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], 2);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.21', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]], 3);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.22', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);



        // TEST: Task::deleteTaskStep(); tests for deleting a step

        // BEGIN TEST
        $actual = '';
        try
        {
            $steps = [
                    ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                    ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ];
            $task = \Flexio\Object\Task::create($steps);
            $task->deleteTaskStep(null);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('E.1', 'Task::deleteTaskStep(); make sure deleteTaskStep has a valid eid', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $steps = [
                    ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                    ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ];
            $task = \Flexio\Object\Task::create($steps);
            $task->deleteTaskStep(true);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('E.2', 'Task::deleteTaskStep(); make sure deleteTaskStep has a valid eid', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->deleteTaskStep('abc');
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.3', 'Task::deleteTaskStep(); don\'t delete the step if the eid doesn\'t match', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->deleteTaskStep($task_list[0]['eid']);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.4', 'Task::deleteTaskStep(); delete the step if the specified eid matches one of the task step eids', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->deleteTaskStep($task_list[1]['eid']);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.5', 'Task::deleteTaskStep(); delete the step if the specified eid matches one of the task step eids', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->deleteTaskStep(\Flexio\Base\Eid::generate());
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.6', 'Task::deleteTaskStep(); don\'t delete the step if the specified eid doesn\'t match any of the task step eids', $actual, $expected, $results);



        // TEST: Task::setTaskStep(); tests for changing a step

        // BEGIN TEST
        $actual = '';
        try
        {
            $steps = [
                    ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                    ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ];
            $task = \Flexio\Object\Task::create($steps);
            $task->setTaskStep(null, ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.1', 'Task::setTaskStep(); make sure the task identifier is a string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $steps = [
                    ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                    ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ];
            $task = \Flexio\Object\Task::create($steps);
            $task->setTaskStep(1, ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.2', 'Task::setTaskStep(); make sure the task identifier is a string', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $steps = [
                    ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                    ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ];
            $task = \Flexio\Object\Task::create($steps);
            $task->setTaskStep($eid1, null);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.3', 'Task::setTaskStep(); make sure the task is an array', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $steps = [
                    ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                    ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ];
            $task = \Flexio\Object\Task::create($steps);
            $task->setTaskStep($eid1, 'abc');
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.4', 'Task::setTaskStep(); make sure the task is an array', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->setTaskStep($task_list[0]['eid'], ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.5', 'Task::setTaskStep(); set the specified task if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->setTaskStep($task_list[1]['eid'], ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.6', 'Task::setTaskStep(); set the specified task if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->setTaskStep(\Flexio\Base\Eid::generate(), ["type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.7', 'Task::setTaskStep(); set the specified task if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $eid1 = \Flexio\Base\Eid::generate();
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->setTaskStep($task_list[1]['eid'], ["eid" => $eid1, "type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["eid" => $task_list[1]['eid'], "type" => \Flexio\Jobs\Nop::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.8', 'Task::setTaskStep(); make sure eid can\'t be set when changing the step', $actual, $expected, $results);


        // TEST: Task::getTaskStep(); tests for getting a step

        // BEGIN TEST
        try
        {
            $steps = [
                    ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                    ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ];
            $task = \Flexio\Object\Task::create($steps);
            $task->getTaskStep(null);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('G.1', 'Task::getTaskStep(); make sure the task identifier is a string', $actual, $expected, $results);

        try
        {
            $steps = [
                    ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                    ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
            ];
            $task = \Flexio\Object\Task::create($steps);
            $task->getTaskStep(true);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('G.2', 'Task::getTaskStep(); make sure the task identifier is a string', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $actual = $task->getTaskStep('abc');
        $expected = false;
        TestCheck::assertBoolean('G.3', 'Task::getTaskStep(); don\'t get the step if the eid is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $actual = $task->getTaskStep($task_list[0]['eid']);
        $expected = ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]];
        TestCheck::assertInArray('G.4', 'Task::getTaskStep(); get a specified step if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $actual = $task->getTaskStep($task_list[1]['eid']);
        $expected = ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]];
        TestCheck::assertInArray('G.5', 'Task::getTaskStep(); get a specified step if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => \Flexio\Jobs\Create::MIME_TYPE, "params" => (object)[]],
                ["type" => \Flexio\Jobs\Convert::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $actual = $task->getTaskStep(\Flexio\Base\Eid::generate());
        $expected = false;
        TestCheck::assertBoolean('G.6', 'Task::getTaskStep(); don\'t get the step if the specified eid doesn\'t match any of the task step eids', $actual, $expected, $results);
    }
}
