<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-08
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


        // TEST: binary entry creation tests

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = \Flexio\Base\Eid::generate();
            $name = null;
            $value = null;
            $model->setBinary($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Registry::setBinary(); throw an error with invalid input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = '';
        $value = '';
        $actual = $model->setBinary($object_eid, $name, $value);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\Registry::setBinary(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            $model->setBinary($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Model\Registry::setBinary(); throw an error with invalid input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $actual = $model->setBinary($object_eid, $name, $value);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Model\Registry::setBinary(); don\'t require an object to be specified', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = 1;
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            $model->setBinary($object_eid, $name, $value);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Model\Registry::setBinary(); if object is specified, it should be an eid', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = '';
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            $expires = false;
            $model->setBinary($object_eid, $name, $value, $expires);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.6', '\Flexio\Model\Registry::setBinary(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = '';
            $name = \Flexio\Base\Util::generateHandle();
            $value = '';
            $expires = 1.1;
            $model->setBinary($object_eid, $name, $value, $expires);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.7', '\Flexio\Model\Registry::setBinary(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = null;
        $actual = $model->setBinary($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Model\Registry::setBinary(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = 3600;
        $actual = $model->setBinary($object_eid, $name, $value, $expires);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Model\Registry::setBinary(); set expiration time if it\'s a positive integer', $actual, $expected, $results);



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
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Model\Registry::entryExists(); throw an error with null input', $actual, $expected, $results);

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
        $result = $model->setBinary($object_eid, $name, $value, null);
        $actual = $model->entryExists($object_eid, $name);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Model\Registry::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = $model->setBinary($object_eid, $name, $value, null);
        $actual = $model->entryExists($object_eid, 'a');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Model\Registry::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = $model->setBinary($object_eid, $name, $value, null);
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
            $mime_type = '';
            $model->getBinary($object_eid, $name, $mime_type);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.1', '\Flexio\Model\Registry::getBinary(); throw an error with null input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $mime_type = '';
        $entry = $model->getBinary($object_eid, $name, $mime_type);
        $actual = !isset($entry);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Model\Registry::getBinary(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = "\e\f\n"; // various escape sequences
        $mime_type = '';
        $result = $model->setBinary($object_eid, $name, $value, null, $mime_type);
        $actual = $model->getBinary($object_eid, $name, $mime_type);
        $expected = "\e\f\n";
        \Flexio\Tests\Check::assertString('C.3', '\Flexio\Model\Registry::getBinary(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = chr(7);
        $mime_type = '';
        $result = $model->setBinary($object_eid, $name, $value, null, $mime_type);
        $actual = $model->getBinary($object_eid, $name, $mime_type);
        $expected = chr(7);
        \Flexio\Tests\Check::assertString('C.4', '\Flexio\Model\Registry::getBinary(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = chr(9);
        $mime_type = '';
        $result = $model->setBinary($object_eid, $name, $value, null, $mime_type);
        $entry = $model->getBinary($object_eid, 'a', $mime_type);
        $actual = !isset($entry);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.5', '\Flexio\Model\Registry::getBinary(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = chr(9);
        $mime_type = '';
        $result = $model->setBinary($object_eid, $name, $value, null, $mime_type);
        $entry = $model->getBinary('', $name, $mime_type);
        $actual = !isset($entry);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.6', '\Flexio\Model\Registry::getBinary(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $mime_type = 'a';
        $entry = $model->getBinary($object_eid, $name, $mime_type);
        $actual = $mime_type;
        $expected = '';
        \Flexio\Tests\Check::assertString('D.1', '\Flexio\Model\Registry::getBinary(); test default mime type', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = chr(9);
        $mime_type = 'a';
        $result = $model->setBinary($object_eid, $name, $value, null, $mime_type);
        $entry = $model->getBinary($object_eid, $name, $mime_type);
        $actual = $mime_type;
        $expected = 'a';
        \Flexio\Tests\Check::assertString('D.2', '\Flexio\Model\Registry::getBinary(); test mime type with a value', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = "a,b,c\n1,2,3";
        $mime_type = 'text/csv';
        $result = $model->setBinary($object_eid, $name, $value, null, $mime_type);
        $entry = $model->getBinary($object_eid, $name, $mime_type);
        $actual = $entry === "a,b,c\n1,2,3" && $mime_type === 'text/csv';
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', '\Flexio\Model\Registry::getBinary(); test mime type with a value', $actual, $expected, $results);
    }
}
