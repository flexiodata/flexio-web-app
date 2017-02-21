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


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: basic entry deletion tests

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $value = '';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->deleteEntryByName($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Registry\Model::deleteEntryByName(); null or blank entries aren\'t allowed', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $value = '';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->deleteEntryByName($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Registry\Model::deleteEntryByName(); null or blank entries aren\'t allowed', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $actual = $model->registry->deleteEntryByName($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Registry\Model::deleteEntryByName(); return false when an entry can\'t be found', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->deleteEntryByName($object_eid, $name);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'Registry\Model::deleteEntryByName(); return true when an entry is deleted', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = '';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->deleteEntryByName($object_eid, $name);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Registry\Model::deleteEntryByName(); return true when an entry is deleted', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = 3.14;
        $creation = $model->registry->setNumber($object_eid, $name, $value);
        $first_deletion = $model->registry->deleteEntryByName($object_eid, $name);
        $second_deletion = $model->registry->deleteEntryByName($object_eid, $name);
        $actual = $first_deletion === true && $second_deletion === false;
        $expected = true;
        TestCheck::assertBoolean('A.6', 'Registry\Model::deleteEntryByName(); return true when an entry is deleted', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = true;
        $creation = $model->registry->setBoolean($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName($object_eid, $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === true && $second_exists === false;
        $expected = true;
        TestCheck::assertBoolean('A.7', 'Registry\Model::deleteEntryByName(); make sure entry is deleted', $actual, $expected, $results);



        // TEST: make sure deletion is sensitive to both object_eid and name

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName('a', $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Registry\Model::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName('', $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Registry\Model::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName($object_eid.'a', $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Registry\Model::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $deletion = $model->registry->deleteEntryByName($object_eid, $name.'a');
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Registry\Model::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);
    }
}
