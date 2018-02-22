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
        // TEST: string entry creation tests

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = \Flexio\Base\Eid::generate();
            $name = null;
            $value = null;
            $result = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', 'Registry\Model::setString(); throw error when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = '';
        $value = '';
        $actual = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', 'Registry\Model::setString(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            $result = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.3', 'Registry\Model::setString(); throw an error if the object_eid isn\'t a string', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = 'a';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $actual = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Registry\Model::setString(); if object is specified, it should be an eid', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $actual = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', 'Registry\Model::setString(); don\'t require object to be specified', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = '';
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            $expires = 'a';
            \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value, $expires);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.6', 'Registry\Model::setString(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = -1;
        $actual = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value, $expires);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.7', 'Registry\Model::setString(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = null;
        $actual = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.8', 'Registry\Model::setString(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = 1000;
        $actual = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.9', 'Registry\Model::setString(); set expiration time if it\'s a positive integer', $actual, $expected, $results);


        // TEST: test for existence of created values

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = null;
            \Flexio\Tests\Util::getModel()->registry->entryExists($object_eid, $name);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.1', 'Registry\Model::entryExists(); throw an error with null object input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = \Flexio\Tests\Util::getModel()->registry->entryExists($object_eid, $name);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Registry\Model::entryExists(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->entryExists($object_eid, $name);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', 'Registry\Model::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->entryExists($object_eid, 'a');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Registry\Model::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->entryExists('', $name);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Registry\Model::entryExists(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = null;
            $entry = \Flexio\Tests\Util::getModel()->registry->getString($object_eid, $name);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.1', 'Registry\Model::getString(); handle null input; default output is null', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = \Flexio\Tests\Util::getModel()->registry->getString($object_eid, $name, 'value');
        $expected = 'value';
        \Flexio\Tests\Check::assertString('C.2', 'Registry\Model::getString(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = \Flexio\Base\Util::generateHandle();
        $result = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->getString($object_eid, $name, 'default');
        $expected = $value;
        \Flexio\Tests\Check::assertString('C.3', 'Registry\Model::getString(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = \Flexio\Base\Util::generateHandle();
        $result = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->getString($object_eid, 'a', 'default');
        $expected = 'default';
        \Flexio\Tests\Check::assertString('C.4', 'Registry\Model::getString(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = \Flexio\Base\Util::generateHandle();
        $result = \Flexio\Tests\Util::getModel()->registry->setString($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->getString('', $name, 'default');
        $expected = 'default';
        \Flexio\Tests\Check::assertString('C.5', 'Registry\Model::getString(); should be sensitive to the object', $actual, $expected, $results);
    }
}
