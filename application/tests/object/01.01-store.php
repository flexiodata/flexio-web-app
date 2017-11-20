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


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: store object loading

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Store::load(false);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        TestCheck::assertString('A.1', 'Store::load(); return false if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual = 'Flexio\Object\Object';
        $expected = get_class($object);
        TestCheck::assertString('A.2', 'Store::load(); return the an object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual = $object->getType();
        $expected = \Model::TYPE_OBJECT;
        TestCheck::assertString('A.3', 'Store::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        TestCheck::assertString('A.4', 'Store::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: store object loading of different types

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_OBJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual = \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_COMMENT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_CONNECTION, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PIPE, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PROCESS, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_PROJECT, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('B.6', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->create(\Model::TYPE_STREAM, null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('B.7', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = $username . '@flex.io';
        $properties = array('user_name' => $username, 'email' => $email);
        $eid = $model->create(\Model::TYPE_USER, $properties); // user creation requires user_name and email
        $object = \Flexio\Object\Store::load($eid);
        $actual = \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        TestCheck::assertBoolean('B.8', 'Store::load(); make sure object is created',  $actual, $expected, $results);
    }
}
