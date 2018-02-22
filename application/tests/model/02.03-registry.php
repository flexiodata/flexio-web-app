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
        $model = TestUtil::getModel();



        // TEST: boolean entry creation tests

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = \Flexio\Base\Eid::generate();
            $name = null;
            $value = null;
            $model->registry->setBoolean($object_eid, $name, $value);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', 'Registry\Model::setBoolean(); throw error with null input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = '';
        $value = true;
        $actual = $model->registry->setBoolean($object_eid, $name, $value);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', 'Registry\Model::setBoolean(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            $model->registry->setBoolean($object_eid, $name, $value);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.3', 'Registry\Model::setBoolean(); throw an error with bad input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = true;
        $actual = $model->registry->setBoolean($object_eid, $name, $value);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Registry\Model::setBoolean(); don\'t require an object to be specified', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = 1;
            $name = \Flexio\Base\Util::generateHandle();
            $value = true;
            $model->registry->setBoolean($object_eid, $name, $value);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.5', 'Registry\Model::setBoolean(); if object is specified, it should be an eid', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = '';
            $name = \Flexio\Base\Util::generateHandle();
            $value = true;
            $expires = false;
            $model->registry->setBoolean($object_eid, $name, $value, $expires);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.6', 'Registry\Model::setBoolean(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = '';
            $name = \Flexio\Base\Util::generateHandle();
            $value = true;
            $expires = 1.1;
            $model->registry->setBoolean($object_eid, $name, $value, $expires);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.7', 'Registry\Model::setBoolean(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = true;
        $expires = null;
        $actual = $model->registry->setBoolean($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.8', 'Registry\Model::setBoolean(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = true;
        $expires = 3600;
        $actual = $model->registry->setBoolean($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.9', 'Registry\Model::setBoolean(); set expiration time if it\'s a positive integer', $actual, $expected, $results);



        // TEST: test for existence of created values

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = null;
            $model->registry->entryExists($object_eid, $name);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('B.1', 'Registry\Model::entryExists(); throw an error with null input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Registry\Model::entryExists(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = true;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', 'Registry\Model::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = true;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->entryExists($object_eid, 'a');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Registry\Model::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = true;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->entryExists('', $name);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Registry\Model::entryExists(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = null;
            $entry = $model->registry->getBoolean($object_eid, $name);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.1', 'Registry\Model::getBoolean(); throw an error with null input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = $model->registry->getBoolean($object_eid, $name, true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', 'Registry\Model::getBoolean(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = true;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->getBoolean($object_eid, $name, false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', 'Registry\Model::getBoolean(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = false;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->getBoolean($object_eid, $name, true);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.4', 'Registry\Model::getBoolean(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = false;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->getBoolean($object_eid, 'a', true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.5', 'Registry\Model::getBoolean(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = false;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->getBoolean('', $name, true);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.6', 'Registry\Model::getBoolean(); should be sensitive to the object', $actual, $expected, $results);
    }
}
