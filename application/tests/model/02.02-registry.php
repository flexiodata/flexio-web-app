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
        // TEST: number entry creation tests

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = \Flexio\Base\Eid::generate();
            $name = null;
            $value = null;
            \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', 'Registry\Model::setNumber(); throw an error with null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = \Flexio\Base\Eid::generate();
            $name = '';
            $value = '';
            \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', 'Registry\Model::setNumber(); throw an error with an invalid input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.3', 'Registry\Model::setNumber(); throw an error with invalid input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = true;
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.4', 'Registry\Model::setNumber(); throw an error with invalid input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = '';
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.5', 'Registry\Model::setNumber(); throw an error with invalid input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = '';
            $name = \Flexio\Base\Util::generateHandle();
            $value = 1;
            $expires = false;
            \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value, $expires);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.6', 'Registry\Model::setNumber(); throw an error with invalid input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = 1.1;
        $expires = 0;
        $actual = \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertString('A.7', 'Registry\Model::setNumber(); set expiration time with non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = 1.1;
        $expires = null;
        $actual = \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.8', 'Registry\Model::setNumber(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = 1.1;
        $expires = 3600;
        $actual = \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.9', 'Registry\Model::setNumber(); set expiration time if it\'s a positive integer', $actual, $expected, $results);



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
        \Flexio\Tests\Check::assertString('B.1', 'Registry\Model::entryExists(); throw error with invalid input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = \Flexio\Tests\Util::getModel()->registry->entryExists($object_eid, $name);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Registry\Model::entryExists(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = 0;
        $result = \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->entryExists($object_eid, $name);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', 'Registry\Model::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 1.1;
        $result = \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->entryExists($object_eid, 'a');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Registry\Model::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 1.1;
        $result = \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
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
            $entry = \Flexio\Tests\Util::getModel()->registry->getNumber($object_eid, $name);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.1', 'Registry\Model::getNumber(); handle null input; default output is null', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = \Flexio\Tests\Util::getModel()->registry->getNumber($object_eid, $name, 0);
        $expected = (double)0;
        \Flexio\Tests\Check::assertNumber('C.2', 'Registry\Model::getNumber(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = 3.14;
        $result = \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->getNumber($object_eid, $name, 1);
        $expected = 3.14;
        \Flexio\Tests\Check::assertNumber('C.3', 'Registry\Model::getNumber(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 1.1;
        $result = \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->getNumber($object_eid, 'a', -1);
        $expected = (double)-1;
        \Flexio\Tests\Check::assertNumber('C.4', 'Registry\Model::getNumber(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 0;
        $result = \Flexio\Tests\Util::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = \Flexio\Tests\Util::getModel()->registry->getNumber('', $name, 2.71);
        $expected = 2.71;
        \Flexio\Tests\Check::assertNumber('C.5', 'Registry\Model::getNumber(); should be sensitive to the object', $actual, $expected, $results);
    }
}
