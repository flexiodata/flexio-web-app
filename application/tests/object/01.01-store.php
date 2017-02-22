<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-27
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



        // TEST: store object creation

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(false, null);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Store::create(); return false if an object fails to create',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_OBJECT, null);
        $actual = 'Flexio\\Object\\Object';
        $expected = get_class($object);
        TestCheck::assertString('A.2', 'Store::create(); return the object if it\'s successfully created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_OBJECT, null);
        $actual = $object->getType();
        $expected = \Model::TYPE_OBJECT;
        TestCheck::assertString('A.3', 'Store::create(); make sure the type is set when an object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_OBJECT, null);
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Store::create(); make sure the eid is set when an object is created',  $actual, $expected, $results);



        // TEST: store object loading

        // BEGIN TEST
        $object = \Flexio\Object\Store::load(false);
        $actual = $object;
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Store::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual = 'Flexio\\Object\\Object';
        $expected = get_class($object);
        TestCheck::assertString('B.2', 'Store::load(); return the an object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_OBJECT;
        TestCheck::assertString('B.3', 'Store::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        TestCheck::assertString('B.4', 'Store::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: store object creation of different types

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_OBJECT, null);
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Store::create(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_COMMENT, null);
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('C.2', 'Store::create(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_CONNECTION, null);
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('C.3', 'Store::create(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_PIPE, null);
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Store::create(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_PROCESS, null);
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('C.5', 'Store::create(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_PROJECT, null);
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('C.6', 'Store::create(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_STREAM, null);
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('C.7', 'Store::create(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $object = \Flexio\Object\Store::create(\Model::TYPE_USER, null);
        $actual = \Flexio\System\Eid::isValid($object->getEid());
        $expected = true;
        TestCheck::assertBoolean('C.8', 'Store::create(); make sure object is created',  $actual, $expected, $results);



        // TEST: store object loading of different types

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual = \Flexio\System\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('D.1', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_COMMENT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\System\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_CONNECTION, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\System\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PIPE, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\System\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('D.4', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PROCESS, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\System\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('D.5', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PROJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\System\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('D.6', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_STREAM, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\System\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('D.7', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $username = \Flexio\System\Util::generateHandle();
        $email = $username . '@flex.io';
        $properties = array('user_name' => $username, 'email' => $email);
        $eid = $model->create(\Model::TYPE_USER, $properties); // user creation requires user_name and email
        $object = \Flexio\Object\Store::load($eid);
        $actual = \Flexio\System\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('D.8', 'Store::load(); make sure object is created',  $actual, $expected, $results);
    }
}
