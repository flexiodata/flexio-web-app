<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-30
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
        $model = TestUtil::getModel();



        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $actual = 'Flexio\\Object\\Pipe';
        $expected = get_class($object);
        TestCheck::assertString('A.1', 'Pipe::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_PIPE;
        TestCheck::assertString('A.2', 'Pipe::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $actual = \Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Pipe::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::load(false);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Pipe::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Pipe::load($eid);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Pipe::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PIPE, null);
        $object = \Flexio\Object\Pipe::load($eid);
        $actual = 'Flexio\\Object\\Pipe';
        $expected = get_class($object);
        TestCheck::assertString('B.3', 'Pipe::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PIPE, null);
        $object = \Flexio\Object\Pipe::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_PIPE;
        TestCheck::assertString('B.4', 'Pipe::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PIPE, null);
        $object = \Flexio\Object\Pipe::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        TestCheck::assertString('B.5', 'Pipe::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->delete();
        $actual =  'Flexio\\Object\\Pipe';
        $expected = get_class($object);
        TestCheck::assertString('C.1', 'Pipe::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Pipe::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_PIPE;
        TestCheck::assertString('C.3', 'Pipe::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Pipe::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->set(null);
        $actual =  'Flexio\\Object\\Pipe';
        $expected = get_class($object);
        TestCheck::assertString('D.1', 'Pipe::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(null)->getEid();
        $actual =  \Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Pipe::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Pipe::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->set(null);
        $actual =  $object->getType();
        $expected = \Model::TYPE_PIPE;
        TestCheck::assertString('D.4', 'Pipe::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_OBJECT));
        $actual =  $object->getType();
        $expected = \Model::TYPE_PIPE;
        TestCheck::assertString('D.5', 'Pipe::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        TestCheck::assertString('E.1', 'Pipe::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  'Flexio\\Object\\Pipe';
        $expected = get_class($object);
        TestCheck::assertString('F.1', 'Pipe::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_TRASH)->getEid();
        $actual =  \Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('F.2', 'Pipe::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  $object->getType();
        $expected = \Model::TYPE_PIPE;
        TestCheck::assertString('F.3', 'Pipe::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $status1 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $status2 = $object->setStatus('.')->getStatus();
        $actual =  ($status1 === \Model::STATUS_TRASH && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        TestCheck::assertBoolean('F.4', 'Pipe::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        TestCheck::assertBoolean('F.5', 'Pipe::setStatus(); make sure the status is set',  $actual, $expected, $results);


        // TEST: object task step addition

        // BEGIN TEST
        $task = \Flexio\Object\Task::create();
        $object = \Flexio\Object\Pipe::create()->setTask($task);
        $actual = count($object->getTask());
        $expected = 0;
        TestCheck::assertNumber('G.1', 'Pipe::setTask(); make sure task step count is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(['type'=>\CreateJob::MIME_TYPE, 'params' => []]);
        $object = \Flexio\Object\Pipe::create()->setTask($task->get());
        $actual = count($object->getTask());
        $expected = 1;
        TestCheck::assertNumber('G.2', 'Pipe::setTask(); make sure task step count is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(\CreateJob::create())->push(\ConvertJob::create());
        $object = \Flexio\Object\Pipe::create()->setTask($task->get());
        $actual = count($object->getTask());
        $expected = 2;
        TestCheck::assertNumber('G.3', 'Pipe::setTask(); make sure task step count is valid',  $actual, $expected, $results);

        // BEGIN TEST
        $task = \Flexio\Object\Task::create()->push(\CreateJob::create())->push(\ConvertJob::create());
        $object = \Flexio\Object\Pipe::create()->setTask($task->get());
        $actual = ($object->getTask()[0]['type'] === \CreateJob::MIME_TYPE) && ($object->getTask()[1]['type'] === \ConvertJob::MIME_TYPE);
        $expected = true;
        TestCheck::assertBoolean('G.4', 'Pipe::setTask(); make sure tasks are set correctly',  $actual, $expected, $results);
    }
}
