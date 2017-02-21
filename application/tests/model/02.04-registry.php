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


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: binary entry creation tests

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = null;
        $value = null;
        $actual = $model->registry->setBinary($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'RegistryModel::setBinary(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = '';
        $value = '';
        $actual = $model->registry->setBinary($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'RegistryModel::setBinary(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = null;
        $name = Util::generateHandle();
        $value = '';
        $actual = $model->registry->setBinary($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'RegistryModel::setBinary(); if object isn\'t specified, it should be an empty string', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $actual = $model->registry->setBinary($object_eid, $name, $value);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'RegistryModel::setBinary(); don\'t require an object to be specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = 1;
        $name = Util::generateHandle();
        $value = '';
        $actual = $model->registry->setBinary($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.5', 'RegistryModel::setBinary(); if object is specified, it should be an eid', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = false;
        $actual = $model->registry->setBinary($object_eid, $name, $value, $expires);
        $expected = false;
        TestCheck::assertBoolean('A.6', 'RegistryModel::setBinary(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = 1.1;
        $actual = $model->registry->setBinary($object_eid, $name, $value, $expires);
        $expected = false;
        TestCheck::assertBoolean('A.7', 'RegistryModel::setBinary(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = null;
        $actual = $model->registry->setBinary($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.8', 'RegistryModel::setBinary(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = 3600;
        $actual = $model->registry->setBinary($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.9', 'RegistryModel::setBinary(); set expiration time if it\'s a positive integer', $actual, $expected, $results);



        // TEST: test for existence of created values

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('B.1', 'RegistryModel::entryExists(); handle null input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('B.2', 'RegistryModel::entryExists(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $result = $model->registry->setBinary($object_eid, $name, $value, null);
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'RegistryModel::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = '';
        $result = $model->registry->setBinary($object_eid, $name, $value, null);
        $actual = $model->registry->entryExists($object_eid, 'a');
        $expected = false;
        TestCheck::assertBoolean('B.4', 'RegistryModel::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = '';
        $result = $model->registry->setBinary($object_eid, $name, $value, null);
        $actual = $model->registry->entryExists('', $name);
        $expected = false;
        TestCheck::assertBoolean('B.5', 'RegistryModel::entryExists(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $mime_type = '';
        $entry = $model->registry->getBinary($object_eid, $name, $mime_type);
        $actual = !isset($entry);
        $expected = true;
        TestCheck::assertBoolean('C.1', 'RegistryModel::getBinary(); handle null input; default output is null', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $mime_type = '';
        $entry = $model->registry->getBinary($object_eid, $name, $mime_type);
        $actual = !isset($entry);
        $expected = true;
        TestCheck::assertBoolean('C.2', 'RegistryModel::getBinary(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = "\e\f\n"; // various escape sequences
        $mime_type = '';
        $result = $model->registry->setBinary($object_eid, $name, $value, null, $mime_type);
        $actual = $model->registry->getBinary($object_eid, $name, $mime_type);
        $expected = "\e\f\n";
        TestCheck::assertString('C.3', 'RegistryModel::getBinary(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = chr(7);
        $mime_type = '';
        $result = $model->registry->setBinary($object_eid, $name, $value, null, $mime_type);
        $actual = $model->registry->getBinary($object_eid, $name, $mime_type);
        $expected = chr(7);
        TestCheck::assertString('C.4', 'RegistryModel::getBinary(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = chr(9);
        $mime_type = '';
        $result = $model->registry->setBinary($object_eid, $name, $value, null, $mime_type);
        $entry = $model->registry->getBinary($object_eid, 'a', $mime_type);
        $actual = !isset($entry);
        $expected = true;
        TestCheck::assertBoolean('C.5', 'RegistryModel::getBinary(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = false;
        $mime_type = '';
        $result = $model->registry->setBinary($object_eid, $name, $value, null, $mime_type);
        $entry = $model->registry->getBinary('', $name, $mime_type);
        $actual = !isset($entry);
        $expected = true;
        TestCheck::assertBoolean('C.6', 'RegistryModel::getBinary(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $mime_type = 'a';
        $entry = $model->registry->getBinary($object_eid, $name, $mime_type);
        $actual = $mime_type;
        $expected = '';
        TestCheck::assertString('D.1', 'RegistryModel::getBinary(); test default mime type', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = 1.23;
        $mime_type = 'a';
        $result = $model->registry->setBinary($object_eid, $name, $value, null, $mime_type);
        $entry = $model->registry->getBinary($object_eid, $name, $mime_type);
        $actual = $mime_type;
        $expected = 'a';
        TestCheck::assertString('D.2', 'RegistryModel::getBinary(); test mime type with a value', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = "a,b,c\n1,2,3";
        $mime_type = 'text/csv';
        $result = $model->registry->setBinary($object_eid, $name, $value, null, $mime_type);
        $entry = $model->registry->getBinary($object_eid, $name, $mime_type);
        $actual = $entry === "a,b,c\n1,2,3" && $mime_type === 'text/csv';
        $expected = true;
        TestCheck::assertBoolean('D.3', 'RegistryModel::getBinary(); test mime type with a value', $actual, $expected, $results);
    }
}
