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
        $model = TestUtil::getModel();



        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $actual = 'Flexio\Object\Connection';
        $expected = get_class($object);
        TestCheck::assertString('A.1', 'Connection::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_CONNECTION;
        TestCheck::assertString('A.2', 'Connection::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $actual = \Flexio\Base\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Connection::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $object = \Flexio\Object\Connection::load('');
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Connection::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Connection::load($eid);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Connection::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_CONNECTION, null);
        $object = \Flexio\Object\Connection::load($eid);
        $actual = 'Flexio\Object\Connection';
        $expected = get_class($object);
        TestCheck::assertString('B.3', 'Connection::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_CONNECTION, null);
        $object = \Flexio\Object\Connection::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_CONNECTION;
        TestCheck::assertString('B.4', 'Connection::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_CONNECTION, null);
        $object = \Flexio\Object\Connection::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        TestCheck::assertString('B.5', 'Connection::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $object = $object->delete();
        $actual =  'Flexio\Object\Connection';
        $expected = get_class($object);
        TestCheck::assertString('C.1', 'Connection::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Connection::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_CONNECTION;
        TestCheck::assertString('C.3', 'Connection::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Connection::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $object = $object->set([]);
        $actual =  'Flexio\Object\Connection';
        $expected = get_class($object);
        TestCheck::assertString('D.1', 'Connection::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set([])->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Connection::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Connection::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $object = $object->set([]);
        $actual =  $object->getType();
        $expected = \Model::TYPE_CONNECTION;
        TestCheck::assertString('D.4', 'Connection::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_OBJECT));
        $actual =  $object->getType();
        $expected = \Model::TYPE_CONNECTION;
        TestCheck::assertString('D.5', 'Connection::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        TestCheck::assertString('E.1', 'Connection::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $creator = \Flexio\Object\User::create();
        $project = \Flexio\Object\Project::create();
        $object->setOwner($creator->getEid());
        $object->setCreatedBy($creator->getEid());
        $project->addMember($object);
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "eid" : null,
            "eid_type" : null,
            "eid_status" : null,
            "ename" : null,
            "name" : null,
            "description" : null,
            "host" : null,
            "port" : null,
            "username" : null,
            "database" : null,
            "password" : null,
            "token" : null,
            "refresh_token" : null,
            "token_expires" : null,
            "connection_type" : null,
            "connection_status" : null,
            "rights" : {
                "owner" : {
                    "read": null,
                    "write": null,
                    "delete": null,
                    "execute": null
                },
                "member" : {
                    "read": null,
                    "write": null,
                    "delete": null,
                    "execute": null
                },
                "public" : {
                    "read": null,
                    "write": null,
                    "delete": null,
                    "execute": null
                }
            },
            "project" : {
                "eid" : null,
                "eid_type" : null,
                "name" : null,
                "description" : null
            },
            "owned_by" : {
                "eid" : null,
                "eid_type" : null,
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "created_by" : {
                "eid" : null,
                "eid_type" : null,
                "user_name" : null,
                "first_name" : null,
                "last_name" : null,
                "email_hash" : null
            },
            "created" : null,
            "updated" : null
        }
        ',true);
        TestCheck::assertArrayKeys('E.2', 'Connection::get(); return the properties as an array',  $actual, $expected, $results);


        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  'Flexio\Object\Connection';
        $expected = get_class($object);
        TestCheck::assertString('F.1', 'Connection::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_TRASH)->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        TestCheck::assertBoolean('F.2', 'Connection::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  $object->getType();
        $expected = \Model::TYPE_CONNECTION;
        TestCheck::assertString('F.3', 'Connection::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Connection::create();
            $status1 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
            $status2 = $object->setStatus('.')->getStatus();
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('F.4', 'Connection::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Connection::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        TestCheck::assertBoolean('F.5', 'Connection::setStatus(); make sure the status is set',  $actual, $expected, $results);
    }
}
