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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = \Flexio\Tests\Util::getModel();




        // BEGIN TEST
        $eid = $model->pipe->create(array());
        $object = \Flexio\Object\Pipe::load($eid);
        $other = $object->get();
        $other = $object->get();
        $other = $object->get();
        $other = $object->get();
        $other = $object->get();
        $actual = $eid;
        $expected = $object->getEid();
        \Flexio\Tests\Check::assertString('B.5', 'Pipe::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);


return;


        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $actual = 'Flexio\Object\Pipe';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'Pipe::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_PIPE;
        \Flexio\Tests\Check::assertString('A.2', 'Pipe::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $actual = \Flexio\Base\Eid::isValid($object->getEid());
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Pipe::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $actual = $object->getOwner();
        $expected = '';
        \Flexio\Tests\Check::assertString('A.5', 'Pipe::create(); objects are created with no owner by default',  $actual, $expected, $results);

        // BEGIN TEST
        $object1 = \Flexio\Object\User::create();
        $object2 = \Flexio\Object\Pipe::create(array('owned_by' => $object1->getEid()));
        $actual = $object2->getOwner();
        $expected = $object1->getEid();
        \Flexio\Tests\Check::assertString('A.6', 'Pipe::create(); make sure the owner can be set properly',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Pipe::load('');
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.1', 'Pipe::load(); throw exception if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $eid = $model->connection->create(array()); // make sure eid of other types can't be loaded
            $object = \Flexio\Object\Pipe::load($eid);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.2', 'Pipe::load(); throw exception if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->pipe->create(array());
        $object = \Flexio\Object\Pipe::load($eid);
        $actual = 'Flexio\Object\Pipe';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('B.3', 'Pipe::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->pipe->create(array());
        $object = \Flexio\Object\Pipe::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_PIPE;
        \Flexio\Tests\Check::assertString('B.4', 'Pipe::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->pipe->create(array());
        $object = \Flexio\Object\Pipe::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        \Flexio\Tests\Check::assertString('B.5', 'Pipe::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->delete();
        $actual =  'Flexio\Object\Pipe';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('C.1', 'Pipe::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', 'Pipe::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_PIPE;
        \Flexio\Tests\Check::assertString('C.3', 'Pipe::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', 'Pipe::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->set([]);
        $actual =  'Flexio\Object\Pipe';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('D.1', 'Pipe::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set([])->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Pipe::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', 'Pipe::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->set([]);
        $actual =  $object->getType();
        $expected = \Model::TYPE_PIPE;
        \Flexio\Tests\Check::assertString('D.4', 'Pipe::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_CONNECTION));
        $actual =  $object->getType();
        $expected = \Model::TYPE_PIPE;
        \Flexio\Tests\Check::assertString('D.5', 'Pipe::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        \Flexio\Tests\Check::assertString('E.1', 'Pipe::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $creator = \Flexio\Object\User::create();
        $object->setOwner($creator->getEid());
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "eid" : null,
            "eid_type" : null,
            "eid_status" : null,
            "alias" : null,
            "name" : null,
            "description" : null,
            "task" : {
            },
            "schedule" : null,
            "schedule_status" : null,
            "owned_by" : {
                "eid" : null,
                "eid_type" : null
            },
            "created" : null,
            "updated" : null
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('E.2', 'Pipe::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->setStatus(\Model::STATUS_PENDING);
        $actual =  'Flexio\Object\Pipe';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('F.1', 'Pipe::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_PENDING)->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', 'Pipe::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $object = $object->setStatus(\Model::STATUS_PENDING);
        $actual =  $object->getType();
        $expected = \Model::TYPE_PIPE;
        \Flexio\Tests\Check::assertString('F.3', 'Pipe::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Pipe::create();
            $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
            $status2 = $object->setStatus('.')->getStatus();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('F.4', 'Pipe::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Pipe::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_DELETED)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.5', 'Pipe::setStatus(); make sure the status is set',  $actual, $expected, $results);



        // TEST: object owner change

        // BEGIN TEST
        $random_eid = \Flexio\Base\Eid::generate();
        $object = \Flexio\Object\Pipe::create();
        $object = $object->setOwner($random_eid);
        $actual = $object->getOwner();
        $expected = $random_eid;
        \Flexio\Tests\Check::assertString('G.1', 'Pipe::setOwner(); make sure the owner is set',  $actual, $expected, $results);
    }
}
