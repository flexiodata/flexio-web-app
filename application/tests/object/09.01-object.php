<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-29
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
        // SETUP
        $model = \Flexio\Tests\Util::getModel();



        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $actual = 'Flexio\Object\Object1';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'Object::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_OBJECT;
        \Flexio\Tests\Check::assertString('A.2', 'Object::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $actual = \Flexio\Base\Eid::isValid($object->getEid());
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Object::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $object = \Flexio\Object\Object1::load('');
        $actual = $object;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Object::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_COMMENT, null);
        $object = \Flexio\Object\Object1::load($eid);
        $actual = $object;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Object::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Object1::load($eid);
        $actual = 'Flexio\Object\Object1';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('B.3', 'Object::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Object1::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_OBJECT;
        \Flexio\Tests\Check::assertString('B.4', 'Object::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Object1::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        \Flexio\Tests\Check::assertString('B.5', 'Object::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $object = $object->delete();
        $actual =  'Flexio\Object\Object1';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('C.1', 'Object::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', 'Object::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_OBJECT;
        \Flexio\Tests\Check::assertString('C.3', 'Object::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', 'Object::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Object1::create();
            $object = $object->set(null);
            $actual = \Flexio\Tests\\Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\\Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\\Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('D.1', 'Object::set(); if no input is set, throw an exeption',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Object::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_COMMENT));
        $actual =  $object->getType();
        $expected = \Model::TYPE_OBJECT;
        \Flexio\Tests\Check::assertString('D.3', 'Object::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        \Flexio\Tests\Check::assertString('E.1', 'Object::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "eid" : "",
            "eid_type" : "",
            "eid_status" : "",
            "created" : "",
            "updated" : ""
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('E.2', 'Object::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  'Flexio\Object\Object1';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('F.1', 'Object::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_TRASH)->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', 'Object::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  $object->getType();
        $expected = \Model::TYPE_OBJECT;
        \Flexio\Tests\Check::assertString('F.3', 'Object::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Object1::create();
            $status1 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
            $status2 = $object->setStatus('.')->getStatus();
            $actual = \Flexio\Tests\\Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\\Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\\Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('F.4', 'Object::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Object1::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.5', 'Object::setStatus(); make sure the status is set',  $actual, $expected, $results);
    }
}
