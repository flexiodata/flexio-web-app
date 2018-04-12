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
        $model = \Flexio\Tests\Util::getModel();


        // TEST: store object loading

        // BEGIN TEST
        $actual = '';
        try
        {
            $object = \Flexio\Object\Store::load('');
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', 'Store::load(); throw exception if an object fails to load',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $eid = $model->pipe->create(null);
            $object = \Flexio\Object\Store::load($eid);
            $actual = get_class($object);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = 'Flexio\Object\Pipe';
        \Flexio\Tests\Check::assertString('A.2', 'Store::load(); return the an object if it\'s successfully loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $eid = $model->pipe->create(null);
            $object = \Flexio\Object\Store::load($eid);
            $actual = $object->getType();
        }
        catch (\Flexio\Base\Exception $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Model::TYPE_PIPE;
        \Flexio\Tests\Check::assertString('A.3', 'Store::load(); make sure the type is set when an object is loaded',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->pipe->create(null);
        $object = \Flexio\Object\Store::load($eid);
        $actual = $eid;
        $expected = $object->getEid();
        \Flexio\Tests\Check::assertString('A.4', 'Store::load(); make sure the eid is set when an object is loaded',  $actual, $expected, $results);



        // TEST: store object loading of different types

        // BEGIN TEST
        $eid = $model->comment->create(null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->connection->create(null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->pipe->create(null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->process->create(null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $eid = $model->stream->create(null);
        $object = \Flexio\Object\Store::load($eid);
        $actual =  \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Store::load(); make sure object is created',  $actual, $expected, $results);

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = $username . '@flex.io';
        $properties = array('username' => $username, 'email' => $email);
        $eid = $model->user->create($properties); // user creation requires username and email
        $object = \Flexio\Object\Store::load($eid);
        $actual = \Flexio\Base\Eid::isValid($eid) && $object->getEid() === $eid;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.6', 'Store::load(); make sure object is created',  $actual, $expected, $results);
    }
}
