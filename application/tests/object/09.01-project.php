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
        $object = \Flexio\Object\Project::create();
        $actual = 'Flexio\\Object\\Project';
        $expected = get_class($object);
        TestCheck::assertString('A.1', 'Project::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_PROJECT;
        TestCheck::assertString('A.2', 'Project::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Project::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $object = \Flexio\Object\Project::load(false);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Project::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Project::load($eid);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Project::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PROJECT, null);
        $object = \Flexio\Object\Project::load($eid);
        $actual = 'Flexio\\Object\\Project';
        $expected = get_class($object);
        TestCheck::assertString('B.3', 'Project::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PROJECT, null);
        $object = \Flexio\Object\Project::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_PROJECT;
        TestCheck::assertString('B.4', 'Project::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PROJECT, null);
        $object = \Flexio\Object\Project::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        TestCheck::assertString('B.5', 'Project::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $object = $object->delete();
        $actual =  'Flexio\\Object\\Project';
        $expected = get_class($object);
        TestCheck::assertString('C.1', 'Project::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Flexio\System\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Project::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_PROJECT;
        TestCheck::assertString('C.3', 'Project::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Project::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $object = $object->set(null);
        $actual =  'Flexio\\Object\\Project';
        $expected = get_class($object);
        TestCheck::assertString('D.1', 'Project::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(null)->getEid();
        $actual =  \Flexio\System\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Project::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Flexio\System\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Project::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $object = $object->set(null);
        $actual =  $object->getType();
        $expected = \Model::TYPE_PROJECT;
        TestCheck::assertString('D.4', 'Project::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_OBJECT));
        $actual =  $object->getType();
        $expected = \Model::TYPE_PROJECT;
        TestCheck::assertString('D.5', 'Project::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        TestCheck::assertString('E.1', 'Project::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  'Flexio\\Object\\Project';
        $expected = get_class($object);
        TestCheck::assertString('F.1', 'Project::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_TRASH)->getEid();
        $actual =  \Flexio\System\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('F.2', 'Project::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  $object->getType();
        $expected = \Model::TYPE_PROJECT;
        TestCheck::assertString('F.3', 'Project::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $status1 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $status2 = $object->setStatus('.')->getStatus();
        $actual =  ($status1 === \Model::STATUS_TRASH && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        TestCheck::assertBoolean('F.4', 'Project::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Project::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        TestCheck::assertBoolean('F.5', 'Project::setStatus(); make sure the status is set',  $actual, $expected, $results);
    }
}
