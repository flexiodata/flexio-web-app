<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-20
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
        $object = \Flexio\Object\Stream::create();
        $actual = 'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'Stream::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('A.2', 'Stream::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = \Flexio\Base\Eid::isValid($object->getEid());
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Stream::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $actual = $object->getOwner();
        $expected = '';
        \Flexio\Tests\Check::assertString('A.5', 'Stream::create(); objects are created with no owner by default',  $actual, $expected, $results);

        // BEGIN TEST
        $object1 = \Flexio\Object\User::create();
        $object2 = \Flexio\Object\Stream::create(array('owned_by' => $object1->getEid()));
        $actual = $object2->getOwner();
        $expected = $object1->getEid();
        \Flexio\Tests\Check::assertString('A.6', 'Stream::create(); make sure the owner can be set properly',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Stream::load('');
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.1', 'Stream::load(); throw exception if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $eid = $model->pipe->create(array()); // make sure eid of other types can't be loaded
            $object = \Flexio\Object\Stream::load($eid);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.2', 'Stream::load(); throw exception if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->stream->create(array());
        $object = \Flexio\Object\Stream::load($eid);
        $actual = 'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('B.3', 'Stream::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->stream->create(array());
        $object = \Flexio\Object\Stream::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('B.4', 'Stream::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->stream->create(array());
        $object = \Flexio\Object\Stream::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        \Flexio\Tests\Check::assertString('B.5', 'Stream::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->delete();
        $actual =  'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('C.1', 'Stream::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', 'Stream::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('C.3', 'Stream::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', 'Stream::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->set([]);
        $actual =  'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('D.1', 'Stream::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set([])->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Stream::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', 'Stream::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->set([]);
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('D.4', 'Stream::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_PIPE));
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('D.5', 'Stream::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        \Flexio\Tests\Check::assertString('E.1', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "eid" : null,
            "eid_type" : null,
            "eid_status" : null,
            "stream_type" : null,
            "parent_eid" : null,
            "connection_eid" : null,
            "name" : null,
            "path" : null,
            "size" : null,
            "hash" : null,
            "mime_type" : null,
            "structure" : {
            },
            "file_created" : null,
            "file_modified" : null,
            "expires" : null,
            "owned_by" : {
                "eid" : null,
                "eid_type" : null
            },
            "created_by" : {
                "eid" : null,
                "eid_type" : null
            },
            "created" : null,
            "updated" : null
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('E.2', 'Stream::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->setStatus(\Model::STATUS_PENDING);
        $actual =  'Flexio\Object\Stream';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('F.1', 'Stream::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_PENDING)->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', 'Stream::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $object = $object->setStatus(\Model::STATUS_PENDING);
        $actual =  $object->getType();
        $expected = \Model::TYPE_STREAM;
        \Flexio\Tests\Check::assertString('F.3', 'Stream::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Stream::create();
            $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
            $status2 = $object->setStatus('.')->getStatus();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('F.4', 'Stream::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Stream::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_DELETED)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.5', 'Stream::setStatus(); make sure the status is set',  $actual, $expected, $results);



        // TEST: object owner change

        // BEGIN TEST
        $random_eid = \Flexio\Base\Eid::generate();
        $object = \Flexio\Object\Stream::create();
        $object = $object->setOwner($random_eid);
        $actual = $object->getOwner();
        $expected = $random_eid;
        \Flexio\Tests\Check::assertString('G.1', 'Stream::setOwner(); make sure the owner is set',  $actual, $expected, $results);
    }
}
