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

        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $actual = 'Flexio\Object\Comment';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'Comment::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_COMMENT;
        \Flexio\Tests\Check::assertString('A.2', 'Comment::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $actual = \Flexio\Base\Eid::isValid($object->getEid());
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Comment::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $object = \Flexio\Object\Comment::load('');
        $actual = $object;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Comment::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_PIPE, null);
        $object = \Flexio\Object\Comment::load($eid);
        $actual = $object;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Comment::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_COMMENT, null);
        $object = \Flexio\Object\Comment::load($eid);
        $actual = 'Flexio\Object\Comment';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('B.3', 'Comment::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_COMMENT, null);
        $object = \Flexio\Object\Comment::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_COMMENT;
        \Flexio\Tests\Check::assertString('B.4', 'Comment::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = \Flexio\Tests\Util::getModel()->create(\Model::TYPE_COMMENT, null);
        $object = \Flexio\Object\Comment::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        \Flexio\Tests\Check::assertString('B.5', 'Comment::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->delete();
        $actual =  'Flexio\Object\Comment';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('C.1', 'Comment::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', 'Comment::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_COMMENT;
        \Flexio\Tests\Check::assertString('C.3', 'Comment::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', 'Comment::delete(); make sure the status is set to deleted',  $actual, $expected, $results);



        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->set([]);
        $actual =  'Flexio\Object\Comment';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('D.1', 'Comment::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set([])->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Comment::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', 'Comment::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->set([]);
        $actual =  $object->getType();
        $expected = \Model::TYPE_COMMENT;
        \Flexio\Tests\Check::assertString('D.4', 'Comment::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_PIPE));
        $actual =  $object->getType();
        $expected = \Model::TYPE_COMMENT;
        \Flexio\Tests\Check::assertString('D.5', 'Comment::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        \Flexio\Tests\Check::assertString('E.1', 'Comment::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $creator = \Flexio\Object\User::create();
        $object->setOwner($creator->getEid());
        $object->setCreatedBy($creator->getEid());
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "eid" : null,
            "eid_type" : null,
            "eid_status" : null,
            "comment" : null,
            "replies" : [
            ],
            "owned_by" : {
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
        \Flexio\Tests\Check::assertArrayKeys('E.2', 'Comment::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  'Flexio\Object\Comment';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('F.1', 'Comment::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_TRASH)->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', 'Comment::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $object = $object->setStatus(\Model::STATUS_TRASH);
        $actual =  $object->getType();
        $expected = \Model::TYPE_COMMENT;
        \Flexio\Tests\Check::assertString('F.3', 'Comment::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Comment::create();
            $status1 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
            $status2 = $object->setStatus('.')->getStatus();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('F.4', 'Comment::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Comment::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_TRASH)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_TRASH);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.5', 'Comment::setStatus(); make sure the status is set',  $actual, $expected, $results);
    }
}
