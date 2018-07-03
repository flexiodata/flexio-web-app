<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
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
        $model = \Flexio\Tests\Util::getModel()->registry;


        // TEST: string entry creation tests

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = \Flexio\Base\Eid::generate();
            $name = null;
            $value = null;
            $result = $model->setString($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Registry::setString(); throw error when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = '';
        $value = '';
        $actual = $model->setString($object_eid, $name, $value);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\Registry::setString(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            $result = $model->setString($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Model\Registry::setString(); throw an error if the object_eid isn\'t a string', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = 'a';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $actual = $model->setString($object_eid, $name, $value);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Model\Registry::setString(); if object is specified, it should be an eid', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $actual = $model->setString($object_eid, $name, $value);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Model\Registry::setString(); don\'t require object to be specified', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = '';
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            $expires = 'a';
            $model->setString($object_eid, $name, $value, $expires);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.6', '\Flexio\Model\Registry::setString(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = -1;
        $actual = $model->setString($object_eid, $name, $value, $expires);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Model\Registry::setString(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = null;
        $actual = $model->setString($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Model\Registry::setString(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = 1000;
        $actual = $model->setString($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Model\Registry::setString(); set expiration time if it\'s a positive integer', $actual, $expected, $results);


        // TEST: test for existence of created values

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = null;
            $model->entryExists($object_eid, $name);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Model\Registry::entryExists(); throw an error with null object input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = $model->entryExists($object_eid, $name);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Model\Registry::entryExists(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = $model->setString($object_eid, $name, $value);
        $actual = $model->entryExists($object_eid, $name);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Model\Registry::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = $model->setString($object_eid, $name, $value);
        $actual = $model->entryExists($object_eid, 'a');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Model\Registry::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = $model->setString($object_eid, $name, $value);
        $actual = $model->entryExists('', $name);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Model\Registry::entryExists(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = null;
            $entry = $model->getString($object_eid, $name);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.1', '\Flexio\Model\Registry::getString(); handle null input; default output is null', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = $model->getString($object_eid, $name, 'value');
        $expected = 'value';
        \Flexio\Tests\Check::assertString('C.2', '\Flexio\Model\Registry::getString(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = \Flexio\Base\Util::generateHandle();
        $result = $model->setString($object_eid, $name, $value);
        $actual = $model->getString($object_eid, $name, 'default');
        $expected = $value;
        \Flexio\Tests\Check::assertString('C.3', '\Flexio\Model\Registry::getString(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = \Flexio\Base\Util::generateHandle();
        $result = $model->setString($object_eid, $name, $value);
        $actual = $model->getString($object_eid, 'a', 'default');
        $expected = 'default';
        \Flexio\Tests\Check::assertString('C.4', '\Flexio\Model\Registry::getString(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = \Flexio\Base\Util::generateHandle();
        $result = $model->setString($object_eid, $name, $value);
        $actual = $model->getString('', $name, 'default');
        $expected = 'default';
        \Flexio\Tests\Check::assertString('C.5', '\Flexio\Model\Registry::getString(); should be sensitive to the object', $actual, $expected, $results);
    }
}
