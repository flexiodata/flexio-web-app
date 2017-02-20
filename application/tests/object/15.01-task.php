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
        // TEST: Task::create(); task creation

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('A.1', 'Task::create(); create an empty task array if no input is specified', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create(true);
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('A.2', 'Task::create(); if input properties are specified, ignore them unless they\'re an array', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([1]);
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('A.3', 'Task::create(); if input properties are specified, only add valid task steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([true]);
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('A.4', 'Task::create(); if input properties are specified, only add valid task steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create(["a"]);
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('A.5', 'Task::create(); if input properties are specified, only add valid task steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([[]]);
        $actual = is_string($task->get()[0]["eid"]);
        $expected = true;
        TestCheck::assertBoolean('A.6', 'Task::create(); make sure eid is added to a task step', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([
                \CreateJob::create()
            ]);
        $actual = $task->get()[0]['type'];
        $expected = CreateJob::MIME_TYPE;
        TestCheck::assertString('A.7', 'Task::create(); add valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([
                \ConvertJob::create()
            ]);
        $actual = $task->get()[0]['type'];
        $expected = ConvertJob::MIME_TYPE;
        TestCheck::assertString('A.8', 'Task::create(); add valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]]
            ]);
        $actual = $task->get()[0]['type'];
        $expected = CreateJob::MIME_TYPE;
        TestCheck::assertString('A.9', 'Task::create(); add as valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([
                '{"type": "flexio.create", "params": {}}'
            ]);
        $actual = $task->get()[0]['type'];
        $expected = CreateJob::MIME_TYPE;
        TestCheck::assertString('A.10', 'Task::create(); add as valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([
                \CreateJob::create(),
                \ConvertJob::create(),
            ]);
        $actual = $task->get()[0]['type'] === CreateJob::MIME_TYPE && $task->get()[1]['type'] === ConvertJob::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('A.11', 'Task::create(); add valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                \ConvertJob::create(),
            ]);
        $actual = $task->get()[0]['type'] === CreateJob::MIME_TYPE && $task->get()[1]['type'] === ConvertJob::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('A.12', 'Task::create(); add valid steps; allow mixed input types', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                '{"type": "flexio.convert", "params": {}}'
            ]);
        $actual = $task->get()[0]['type'] === CreateJob::MIME_TYPE && $task->get()[1]['type'] === ConvertJob::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('A.13', 'Task::create(); add valid steps; allow mixed input types', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create([
                "A",
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                true,
                '{"type": "flexio.convert", "params": {}}',
                null
            ]);
        $actual = $task->get()[0]['type'] === CreateJob::MIME_TYPE && $task->get()[1]['type'] === ConvertJob::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('A.14', 'Task::create(); add valid steps; allow mixed input types; ingore bad input', $actual, $expected, $results);



        // TEST: Task::push(); tests for adding a step

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(null);
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('B.1', 'Task::push(); create an empty task array if no input is specified', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(false);
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('B.2', 'Task::push(); if input properties are specified, ignore them unless they\'re an array', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(true);
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('B.3', 'Task::push(); if input properties are specified, only add valid task steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(1);
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('B.4', 'Task::push(); if input properties are specified, only add valid task steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push('a');
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('B.5', 'Task::push(); if input properties are specified, only add valid task steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push([]);
        $actual = is_string($task->get()[0]["eid"]);
        $expected = true;
        TestCheck::assertBoolean('B.6', 'Task::push(); make sure eid is added to a task step', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(\CreateJob::create());
        $actual = $task->get()[0]['type'];
        $expected = CreateJob::MIME_TYPE;
        TestCheck::assertString('B.7', 'Task::push(); add valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(\ConvertJob::create());
        $actual = $task->get()[0]['type'];
        $expected = ConvertJob::MIME_TYPE;
        TestCheck::assertString('B.8', 'Task::push(); add valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(["type" => CreateJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get()[0]['type'];
        $expected = CreateJob::MIME_TYPE;
        TestCheck::assertString('B.9', 'Task::push(); add as valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push('{"type": "flexio.create", "params": {}}');
        $actual = $task->get()[0]['type'];
        $expected = CreateJob::MIME_TYPE;
        TestCheck::assertString('B.10', 'Task::push(); add as valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push(\CreateJob::create())
                        ->push(\ConvertJob::create());
        $actual = $task->get()[0]['type'] === CreateJob::MIME_TYPE && $task->get()[1]['type'] === ConvertJob::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('B.11', 'Task::push(); add valid steps', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push(["type" => CreateJob::MIME_TYPE, "params" => (object)[]])
                        ->push(\ConvertJob::create());
        $actual = $task->get()[0]['type'] === CreateJob::MIME_TYPE && $task->get()[1]['type'] === ConvertJob::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('B.12', 'Task::push(); add valid steps; allow mixed input types', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push(["type" => CreateJob::MIME_TYPE, "params" => (object)[]])
                        ->push('{"type": "flexio.convert", "params": {}}');
        $actual = $task->get()[0]['type'] === CreateJob::MIME_TYPE && $task->get()[1]['type'] === ConvertJob::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('B.13', 'Task::push(); add valid steps; allow mixed input types', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push("A")
                        ->push(["type" => CreateJob::MIME_TYPE, "params" => (object)[]])
                        ->push(true)
                        ->push('{"type": "flexio.convert", "params": {}}')
                        ->push(null);
        $actual = $task->get()[0]['type'] === CreateJob::MIME_TYPE && $task->get()[1]['type'] === ConvertJob::MIME_TYPE;
        $expected = true;
        TestCheck::assertBoolean('B.14', 'Task::push(); add valid steps; allow mixed input types; ingore bad input', $actual, $expected, $results);



        // TEST: Task::pop(); tests for removing the last step

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->pop();
        $actual = $task->get();
        $expected = [];
        TestCheck::assertArray('C.1', 'Task::pop(); don\'t fail if there are no elements to remove', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push(\CreateJob::create())
                        ->push(\ConvertJob::create())
                        ->push(\FilterJob::create());
        $task_list = $task->pop()->get();
        $actual = end($task_list)['type'];
        $expected = \ConvertJob::MIME_TYPE;
        TestCheck::assertString('C.2', 'Task::pop(); remove the last element', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push(\CreateJob::create())
                        ->push(\ConvertJob::create())
                        ->push(\FilterJob::create());
        $task_list = $task->pop()->pop()->get();
        $actual = end($task_list)['type'];
        $expected = \CreateJob::MIME_TYPE;
        TestCheck::assertString('C.3', 'Task::pop(); remove the last element', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()
                        ->push(\CreateJob::create())
                        ->push(\ConvertJob::create())
                        ->push(\FilterJob::create());
        $task->pop()->pop()->pop();
        $actual = $task->get();
        $expected = array();
        TestCheck::assertArray('C.4', 'Task::pop(); remove the last element', $actual, $expected, $results);



        // TEST: Task::addTaskStep(); tests for adding a step

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(null);
        $actual = $task->get();
        $expected = [
        ];
        TestCheck::assertInArray('D.1', 'Task::addTaskStep(); don\'t add a step if the task is bad', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(false);
        $actual = $task->get();
        $expected = [
        ];
        TestCheck::assertInArray('D.2', 'Task::addTaskStep(); don\'t add a step if the task is bad', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(1);
        $actual = $task->get();
        $expected = [
        ];
        TestCheck::assertInArray('D.3', 'Task::addTaskStep(); don\'t add a step if the task is bad', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep('abc');
        $actual = $task->get();
        $expected = [
        ];
        TestCheck::assertInArray('D.4', 'Task::addTaskStep(); don\'t add a step if the task is bad', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => CreateJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.5', 'Task::addTaskStep(); add steps that are in the correct format', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep('{"type": "flexio.create", "params": {}}');
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.6', 'Task::addTaskStep(); add steps that are in the correct format', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep('{"type": "flexio.create", "params": {}}');
        $result = $task->get();
        $actual = count($result) === 1 && isset($result[0]['eid']) && \Eid::isValid($result[0]['eid']);
        $expected = true;
        TestCheck::assertBoolean('D.7', 'Task::addTaskStep(); when a step is added, make sure the eid is set', $actual, $expected, $results);

        // BEGIN TEST
        $eid1 = \Eid::generate();
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["eid" => $eid1, "type" => CreateJob::MIME_TYPE, "params" => (object)[]]);
        $result = $task->get();
        $actual = count($result) === 1 && isset($result[0]['eid']) && \Eid::isValid($result[0]['eid']) && $result[0]['eid'] === $eid1;
        $expected = true;
        TestCheck::assertBoolean('D.8', 'Task::addTaskStep(); add steps that are in the correct format; valid eids that aren\'t objects in the application are allowed to be set', $actual, $expected, $results);

        // BEGIN TEST
        $eid1 = 'xyz'; // bad eid
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["eid" => $eid1, "type" => CreateJob::MIME_TYPE, "params" => (object)[]]);
        $result = $task->get();
        $actual = count($result) === 1 && isset($result[0]['eid']) && \Eid::isValid($result[0]['eid']) && $result[0]['eid'] !== $eid1;
        $expected = true;
        TestCheck::assertBoolean('D.9', 'Task::addTaskStep(); add steps that are in the correct format; if an eid is supplied, make sure it\'s valid and isn\'t an object in the application, or else generate a new one', $actual, $expected, $results);

        // BEGIN TEST
        $eid1 = \Flexio\Object\Comment::create()->getEid(); // eid corresponding to an object
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["eid" => $eid1, "type" => CreateJob::MIME_TYPE, "params" => (object)[]]);
        $result = $task->get();
        $actual = count($result) === 1 && isset($result[0]['eid']) && \Eid::isValid($eid1) && \Eid::isValid($result[0]['eid']) && $result[0]['eid'] !== $eid1;
        $expected = true;
        TestCheck::assertBoolean('D.10', 'Task::addTaskStep(); add steps that are in the correct format; if an eid is supplied, make sure it\'s valid and isn\'t an object in the application, or else generate a new one', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => CreateJob::MIME_TYPE, "params" => (object)[]]);
        $task->addTaskStep(["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.11', 'Task::addTaskStep(); make sure that steps without any supplied index are added to the end of the task list', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]);
        $task->addTaskStep(["type" => CreateJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.12', 'Task::addTaskStep(); make sure that steps without any supplied index are added to the end of the task list', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.13', 'Task::addTaskStep(); make sure that steps without any supplied index are added to the end of the task list', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], null);
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.14', 'Task::addTaskStep(); make sure that steps with bad indexes insert steps at the end', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], false);
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.15', 'Task::addTaskStep(); make sure that steps with bad indexes insert steps at the end', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], 'abc');
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.16', 'Task::addTaskStep(); make sure that steps with bad indexes insert steps at the end', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], -1);
        $actual = $task->get();
        $expected = [
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.17', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], 0);
        $actual = $task->get();
        $expected = [
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.18', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], 1);
        $actual = $task->get();
        $expected = [
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.19', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => CreateJob::MIME_TYPE, "params" => (object)[]], 0);
        $task->addTaskStep(["type" => ConvertJob::MIME_TYPE, "params" => (object)[]], 0);
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.20', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $task->addTaskStep(["type" => CreateJob::MIME_TYPE, "params" => (object)[]], 0);
        $task->addTaskStep(["type" => ConvertJob::MIME_TYPE, "params" => (object)[]], 1);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.21', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], -1);
        $actual = $task->get();
        $expected = [
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.22', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], 0);
        $actual = $task->get();
        $expected = [
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.23', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], 1);
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.24', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], 2);
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.25', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->addTaskStep(["type" => NopJob::MIME_TYPE, "params" => (object)[]], 3);
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]],
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('D.26', 'Task::addTaskStep(); make sure that steps with indexes are inserted at the appropriate position', $actual, $expected, $results);



        // TEST: Task::deleteTaskStep(); tests for deleting a step

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->deleteTaskStep(null);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.1', 'Task::deleteTaskStep(); don\'t delete the step if the eid is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->deleteTaskStep(false);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.2', 'Task::deleteTaskStep(); don\'t delete the step if the eid is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->deleteTaskStep('abc');
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.3', 'Task::deleteTaskStep(); don\'t delete the step if the eid is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->deleteTaskStep($task_list[0]['eid']);
        $actual = $task->get();
        $expected = [
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.4', 'Task::deleteTaskStep(); delete the step if the specified eid matches one of the task step eids', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->deleteTaskStep($task_list[1]['eid']);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.5', 'Task::deleteTaskStep(); delete the step if the specified eid matches one of the task step eids', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->deleteTaskStep(\Eid::generate());
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('E.6', 'Task::deleteTaskStep(); don\'t delete the step if the specified eid doesn\'t match any of the task step eids', $actual, $expected, $results);



        // TEST: Task::setTaskStep(); tests for changing a step

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->setTaskStep(null, ["type" => NopJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.1', 'Task::setTaskStep(); don\'t replace the step if the eid is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->setTaskStep(false, ["type" => NopJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.2', 'Task::setTaskStep(); don\'t replace the step if the eid is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->setTaskStep($eid1, null);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.3', 'Task::setTaskStep(); don\'t replace the step if the input is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->setTaskStep($eid1, false);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.4', 'Task::setTaskStep(); don\'t replace the step if the input is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->setTaskStep($eid1, 1);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.5', 'Task::setTaskStep(); don\'t replace the step if the input is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->setTaskStep($eid1, "a");
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.6', 'Task::setTaskStep(); don\'t replace the step if the input is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->setTaskStep($task_list[0]['eid'], ["type" => NopJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.7', 'Task::setTaskStep(); set the specified task if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->setTaskStep($task_list[1]['eid'], ["type" => NopJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.8', 'Task::setTaskStep(); set the specified task if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task->setTaskStep(\Eid::generate(), ["type" => NopJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.9', 'Task::setTaskStep(); set the specified task if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $eid1 = \Eid::generate();
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $task->setTaskStep($task_list[1]['eid'], ["eid" => $eid1, "type" => NopJob::MIME_TYPE, "params" => (object)[]]);
        $actual = $task->get();
        $expected = [
                ["eid" => $task_list[1]['eid'], "type" => NopJob::MIME_TYPE, "params" => (object)[]]
        ];
        TestCheck::assertInArray('F.10', 'Task::setTaskStep(); make sure eid can\'t be set when changing the step', $actual, $expected, $results);



        // TEST: Task::getTaskStep(); tests for getting a step

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $actual = $task->getTaskStep(null);
        $expected = false;
        TestCheck::assertBoolean('G.1', 'Task::getTaskStep(); don\'t get the step if the eid is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $actual = $task->getTaskStep(false);
        $expected = false;
        TestCheck::assertBoolean('G.2', 'Task::getTaskStep(); don\'t get the step if the eid is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $actual = $task->getTaskStep('abc');
        $expected = false;
        TestCheck::assertBoolean('G.3', 'Task::getTaskStep(); don\'t get the step if the eid is bad', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $actual = $task->getTaskStep($task_list[0]['eid']);
        $expected = ["type" => CreateJob::MIME_TYPE, "params" => (object)[]];
        TestCheck::assertInArray('G.4', 'Task::getTaskStep(); get a specified step if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $task_list = $task->get();
        $actual = $task->getTaskStep($task_list[1]['eid']);
        $expected = ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]];
        TestCheck::assertInArray('G.5', 'Task::getTaskStep(); get a specified step if it exists', $actual, $expected, $results);

        // BEGIN TEST
        $steps = [
                ["type" => CreateJob::MIME_TYPE, "params" => (object)[]],
                ["type" => ConvertJob::MIME_TYPE, "params" => (object)[]]
        ];
        $task = \Flexio\Object\Task::create($steps);
        $actual = $task->getTaskStep(\Eid::generate());
        $expected = false;
        TestCheck::assertBoolean('G.6', 'Task::getTaskStep(); don\'t get the step if the specified eid doesn\'t match any of the task step eids', $actual, $expected, $results);
    }
}
