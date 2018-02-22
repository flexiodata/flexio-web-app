<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-08
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



        // TEST: basic entry deletion tests

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = null;
            $value = '';
            $creation = $model->registry->setString($object_eid, $name, $value);
            $model->registry->deleteEntryByName($object_eid, $name);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', 'Registry\Model::deleteEntryByName(); throw an error with null input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = '';
        try
        {
            $object_eid = null;
            $name = null;
            $value = '';
            $creation = $model->registry->setString($object_eid, $name, $value);
            $model->registry->deleteEntryByName($object_eid, $name);
            $actual = \Flexio\Tests\TestError::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\TestError::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.2', 'Registry\Model::deleteEntryByName(); throw an error with null input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = $model->registry->deleteEntryByName($object_eid, $name);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', 'Registry\Model::deleteEntryByName(); return false when an entry can\'t be found', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->deleteEntryByName($object_eid, $name);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Registry\Model::deleteEntryByName(); return true when an entry is deleted', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->deleteEntryByName($object_eid, $name);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', 'Registry\Model::deleteEntryByName(); return true when an entry is deleted', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 3.14;
        $creation = $model->registry->setNumber($object_eid, $name, $value);
        $first_deletion = $model->registry->deleteEntryByName($object_eid, $name);
        $second_deletion = $model->registry->deleteEntryByName($object_eid, $name);
        $actual = $first_deletion === true && $second_deletion === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.6', 'Registry\Model::deleteEntryByName(); return true when an entry is deleted', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = true;
        $creation = $model->registry->setBoolean($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName($object_eid, $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === true && $second_exists === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.7', 'Registry\Model::deleteEntryByName(); make sure entry is deleted', $actual, $expected, $results);



        // TEST: make sure deletion is sensitive to both object_eid and name

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName('a', $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Registry\Model::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName('', $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Registry\Model::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName($object_eid.'a', $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', 'Registry\Model::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName($object_eid, $name.'a');
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Registry\Model::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);
    }
}
