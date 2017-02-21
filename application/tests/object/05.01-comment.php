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


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $actual = 'Flexio\\Object\\Comment';
        $expected = get_class($object);
        TestCheck::assertString('A.1', 'Comment::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_COMMENT;
        TestCheck::assertString('A.2', 'Comment::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $actual = \Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Comment::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $object = \Flexio\Object\Comment::load(false);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Comment::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Comment::load($eid);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Comment::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_COMMENT, null);
        $object = \Flexio\Object\Comment::load($eid);
        $actual = 'Flexio\\Object\\Comment';
        $expected = get_class($object);
        TestCheck::assertString('B.3', 'Comment::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_COMMENT, null);
        $object = \Flexio\Object\Comment::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_COMMENT;
        TestCheck::assertString('B.4', 'Comment::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_COMMENT, null);
        $object = \Flexio\Object\Comment::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        TestCheck::assertString('B.5', 'Comment::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->delete();
        $actual =  'Flexio\\Object\\Comment';
        $expected = get_class($object);
        TestCheck::assertString('C.1', 'Comment::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Comment::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_COMMENT;
        TestCheck::assertString('C.3', 'Comment::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Comment::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->set(null);
        $actual =  'Flexio\\Object\\Comment';
        $expected = get_class($object);
        TestCheck::assertString('D.1', 'Comment::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(null)->getEid();
        $actual =  \Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Comment::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Comment::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->set(null);
        $actual =  $object->getType();
        $expected = \Model::TYPE_COMMENT;
        TestCheck::assertString('D.4', 'Comment::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_OBJECT));
        $actual =  $object->getType();
        $expected = \Model::TYPE_COMMENT;
        TestCheck::assertString('D.5', 'Comment::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        TestCheck::assertString('E.1', 'Comment::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  'Flexio\\Object\\Comment';
        $expected = get_class($object);
        TestCheck::assertString('F.1', 'Comment::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_TRASH)->getEid();
        $actual =  \Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('F.2', 'Comment::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  $object->getType();
        $expected = \Model::TYPE_COMMENT;
        TestCheck::assertString('F.3', 'Comment::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $status1 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $status2 = $object->setStatus('.')->getStatus();
        $actual =  ($status1 === \Model::STATUS_TRASH && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        TestCheck::assertBoolean('F.4', 'Comment::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        TestCheck::assertBoolean('F.5', 'Comment::setStatus(); make sure the status is set',  $actual, $expected, $results);
    }
}
