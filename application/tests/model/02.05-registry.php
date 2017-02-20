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
        // TEST: basic entry deletion tests

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $value = '';
        $creation = System::getModel()->registry->setString($object_eid, $name, $value);
        $actual = System::getModel()->registry->deleteEntryByName($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'RegistryModel::deleteEntryByName(); null or blank entries aren\'t allowed', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $value = '';
        $creation = System::getModel()->registry->setString($object_eid, $name, $value);
        $actual = System::getModel()->registry->deleteEntryByName($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'RegistryModel::deleteEntryByName(); null or blank entries aren\'t allowed', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $actual = System::getModel()->registry->deleteEntryByName($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'RegistryModel::deleteEntryByName(); return false when an entry can\'t be found', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $creation = System::getModel()->registry->setString($object_eid, $name, $value);
        $actual = System::getModel()->registry->deleteEntryByName($object_eid, $name);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'RegistryModel::deleteEntryByName(); return true when an entry is deleted', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = '';
        $creation = System::getModel()->registry->setString($object_eid, $name, $value);
        $actual = System::getModel()->registry->deleteEntryByName($object_eid, $name);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'RegistryModel::deleteEntryByName(); return true when an entry is deleted', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = 3.14;
        $creation = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $first_deletion = System::getModel()->registry->deleteEntryByName($object_eid, $name);
        $second_deletion = System::getModel()->registry->deleteEntryByName($object_eid, $name);
        $actual = $first_deletion === true && $second_deletion === false;
        $expected = true;
        TestCheck::assertBoolean('A.6', 'RegistryModel::deleteEntryByName(); return true when an entry is deleted', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = true;
        $creation = System::getModel()->registry->setBoolean($object_eid, $name, $value);
        $first_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $deletion = System::getModel()->registry->deleteEntryByName($object_eid, $name);
        $second_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === true && $second_exists === false;
        $expected = true;
        TestCheck::assertBoolean('A.7', 'RegistryModel::deleteEntryByName(); make sure entry is deleted', $actual, $expected, $results);



        // TEST: make sure deletion is sensitive to both object_eid and name

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = 'a';
        $creation = System::getModel()->registry->setString($object_eid, $name, $value);
        $first_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $deletion = System::getModel()->registry->deleteEntryByName('a', $name);
        $second_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('B.1', 'RegistryModel::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = 'a';
        $creation = System::getModel()->registry->setString($object_eid, $name, $value);
        $first_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $deletion = System::getModel()->registry->deleteEntryByName('', $name);
        $second_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('B.2', 'RegistryModel::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = 'a';
        $creation = System::getModel()->registry->setString($object_eid, $name, $value);
        $first_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $deletion = System::getModel()->registry->deleteEntryByName($object_eid.'a', $name);
        $second_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('B.3', 'RegistryModel::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = 'a';
        $creation = System::getModel()->registry->setString($object_eid, $name, $value);
        $first_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $deletion = System::getModel()->registry->deleteEntryByName($object_eid, $name.'a');
        $second_exists = System::getModel()->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $deletion === false && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('B.4', 'RegistryModel::deleteEntryByName(); make sure entry deletion is sensitive to both object_eid and name', $actual, $expected, $results);
    }
}
