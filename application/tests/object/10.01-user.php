<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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


        // TEST: object creation

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $actual = 'Flexio\Object\User';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('A.1', 'User::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $actual = $object->getType();
        $expected = \Model::TYPE_USER;
        \Flexio\Tests\Check::assertString('A.2', 'User::create(); make sure the correct type is set',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $actual = \Flexio\Base\Eid::isValid($object->getEid());
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', 'User::create(); make sure a valid eid is set when an object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $object_info = $object->get();
        $actual = $object_info['username'];
        $expected = '';
        \Flexio\Tests\Check::assertString('A.4', 'User::create(); objects are created with no username by default',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $object_info = $object->get();
        $actual = $object_info['email'];
        $expected = '';
        \Flexio\Tests\Check::assertString('A.5', 'User::create(); objects are created with no email by default',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $actual = $object->getVerifyCode();
        $expected = '';
        \Flexio\Tests\Check::assertString('A.6', 'User::create(); objects are created with no verification code by default',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $actual = $object->getOwner();
        $expected = $object->getEid();
        \Flexio\Tests\Check::assertString('A.7', 'User::create(); objects are created with the user as the owner',  $actual, $expected, $results);

        // BEGIN TEST
        $object1 = \Flexio\Object\User::create();
        $object2 = \Flexio\Object\User::create(array('owned_by' => $object1->getEid()));
        $actual = $object2->getOwner();
        $expected = $object2->getEid();
        \Flexio\Tests\Check::assertString('A.8', 'User::create(); don\'t allow the user owner to be set to something else',  $actual, $expected, $results);



        // TEST: object loading

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\User::load('');
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.1', 'User::load(); throw exception if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $eid = $model->pipe->create(array()); // make sure eid of other types can't be loaded
            $object = \Flexio\Object\User::load($eid);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.2', 'User::load(); throw exception if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $username = \Flexio\Base\Identifier::generate();
        $email = $username . '@flex.io';
        $properties = array('username' => $username, 'email' => $email);
        $eid = $model->user->create($properties);
        $object = \Flexio\Object\User::load($eid);
        $actual = 'Flexio\Object\User';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('B.3', 'User::load(); return the object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $username = \Flexio\Base\Identifier::generate();
        $email = $username . '@flex.io';
        $properties = array('username' => $username, 'email' => $email);
        $eid = $model->user->create($properties);
        $object = \Flexio\Object\User::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_USER;
        \Flexio\Tests\Check::assertString('B.4', 'User::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $username = \Flexio\Base\Identifier::generate();
        $email = $username . '@flex.io';
        $properties = array('username' => $username, 'email' => $email);
        $eid = $model->user->create($properties);
        $object = \Flexio\Object\User::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        \Flexio\Tests\Check::assertString('B.5', 'User::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: object deletion

        $actual = '';
        try
        {
            $object = \Flexio\Object\User::create();
            $eid1 = $object->getEid();
            $eid2 = $object->delete()->getEid();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.1', '\Flexio\Object\User::delete(); throw an error when attempting to delete a user with normal delete() method',  $actual, $expected, $results);

/*
        // note: keep following tests in case user deletion via status flag of STATUS_DELETED
        // is reactivated; right now, users can't be deleted by setting the status flag
        // and the only way to delete them is via purging

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $object = $object->delete();
        $actual =  'Flexio\Object\User';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('C.1', 'User::delete(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $eid1 = $object->getEid();
        $eid2 = $object->delete()->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', 'User::delete(); deleting an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $object = $object->delete();
        $actual =  $object->getType();
        $expected = \Model::TYPE_USER;
        \Flexio\Tests\Check::assertString('C.3', 'User::delete(); deleting an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $status1 = $object->getStatus();
        $status2 = $object->delete()->getStatus();
        $actual =  ($status1 !== \Model::STATUS_DELETED && $status2 === \Model::STATUS_DELETED);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.4', 'User::delete(); make sure the status is set to deleted',  $actual, $expected, $results);
*/


        // TEST: object property setting

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $object = $object->set([]);
        $actual =  'Flexio\Object\User';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('D.1', 'User::set(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set([])->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', 'User::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $eid1 = $object->getEid();
        $eid2 = $object->set(array('eid'=>'xxxxxxxxxxxx'))->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', 'User::set(); don\'t allow the eid to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $object = $object->set([]);
        $actual =  $object->getType();
        $expected = \Model::TYPE_USER;
        \Flexio\Tests\Check::assertString('D.4', 'User::set(); don\'t allow the type to be changed',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $object = $object->set(array('eid_type'=>\Model::TYPE_PIPE));
        $actual =  $object->getType();
        $expected = \Model::TYPE_USER;
        \Flexio\Tests\Check::assertString('D.5', 'User::set(); don\'t allow the type to be changed',  $actual, $expected, $results);



        // TEST: object property retrieval

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $properties = $object->get();
        $actual =  is_array($properties);
        $expected = true;
        \Flexio\Tests\Check::assertString('E.1', 'User::get(); return the properties as an array',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $creator = \Flexio\Object\User::create();
        $properties = $object->get();
        $actual =  $properties;
        $expected = json_decode('
        {
            "eid" : null,
            "eid_type" : null,
            "eid_status" : null,
            "username" : null,
            "first_name" : null,
            "last_name" : null,
            "email" : null,
            "email_hash" : null,
            "phone" : null,
            "location_city" : null,
            "location_state" : null,
            "location_country" : null,
            "company_name" : null,
            "company_url" : null,
            "locale_language" : null,
            "locale_decimal" : null,
            "locale_thousands" : null,
            "locale_dateformat" : null,
            "timezone" : null,
            "usage_tier" : null,
            "referrer" : null,
            "config" : {
            },
            "owned_by" : {
                "eid": null,
                "eid_type": null
            },
            "created" : null,
            "updated" : null
        }
        ',true);
        \Flexio\Tests\Check::assertArrayKeys('E.2', 'User::get(); return the properties as an array',  $actual, $expected, $results);



        // TEST: object status change

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $object = $object->setStatus(\Model::STATUS_PENDING);
        $actual =  'Flexio\Object\User';
        $expected = get_class($object);
        \Flexio\Tests\Check::assertString('F.1', 'User::setStatus(); return the object',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $eid1 = $object->getEid();
        $eid2 = $object->setStatus(\Model::STATUS_PENDING)->getEid();
        $actual =  \Flexio\Base\Eid::isValid($eid1) && $eid1 === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', 'User::setStatus(); setting status of an object shouldn\'t change its eid',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $object = $object->setStatus(\Model::STATUS_PENDING);
        $actual =  $object->getType();
        $expected = \Model::TYPE_USER;
        \Flexio\Tests\Check::assertString('F.3', 'User::setStatus(); setting status of an object shouldn\'t change its type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\User::create();
            $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
            $status2 = $object->setStatus('.')->getStatus();
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('F.4', 'User::setStatus(); don\'t allow an invalid status',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\User::create();
        $status1 = $object->setStatus(\Model::STATUS_PENDING)->getStatus();
        $status2 = $object->setStatus(\Model::STATUS_AVAILABLE)->getStatus();
        $actual =  ($status1 === \Model::STATUS_PENDING && $status2 === \Model::STATUS_AVAILABLE);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.5', 'User::setStatus(); make sure the status is set',  $actual, $expected, $results);



        // TEST: object owner change

        // BEGIN TEST
        try
        {
            $random_eid = \Flexio\Base\Eid::generate();
            $object = \Flexio\Object\User::create();
            $object = $object->setOwner($random_eid);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('G.1', 'User::setOwner(); the user owner is themselves and can\'t be changed; throw an exception when attempting to set the user owner',  $actual, $expected, $results);
    }
}
