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


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: boolean entry creation tests

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = null;
        $value = null;
        $actual = $model->registry->setBoolean($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'RegistryModel::setBoolean(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = '';
        $value = '';
        $actual = $model->registry->setBoolean($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'RegistryModel::setBoolean(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = null;
        $name = Util::generateHandle();
        $value = '';
        $actual = $model->registry->setBoolean($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'RegistryModel::setBoolean(); if object isn\'t specified, it should be an empty string', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $actual = $model->registry->setBoolean($object_eid, $name, $value);
        $expected = true;
        TestCheck::assertBoolean('A.4', 'RegistryModel::setBoolean(); don\'t require an object to be specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = 1;
        $name = Util::generateHandle();
        $value = '';
        $actual = $model->registry->setBoolean($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.5', 'RegistryModel::setBoolean(); if object is specified, it should be an eid', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = false;
        $actual = $model->registry->setBoolean($object_eid, $name, $value, $expires);
        $expected = false;
        TestCheck::assertBoolean('A.6', 'RegistryModel::setBoolean(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = 1.1;
        $actual = $model->registry->setBoolean($object_eid, $name, $value, $expires);
        $expected = false;
        TestCheck::assertBoolean('A.7', 'RegistryModel::setBoolean(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = null;
        $actual = $model->registry->setBoolean($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.8', 'RegistryModel::setBoolean(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = 3600;
        $actual = $model->registry->setBoolean($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.9', 'RegistryModel::setBoolean(); set expiration time if it\'s a positive integer', $actual, $expected, $results);



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
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'RegistryModel::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = '';
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->entryExists($object_eid, 'a');
        $expected = false;
        TestCheck::assertBoolean('B.4', 'RegistryModel::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = '';
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->entryExists('', $name);
        $expected = false;
        TestCheck::assertBoolean('B.5', 'RegistryModel::entryExists(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $entry = $model->registry->getBoolean($object_eid, $name);
        $actual = !isset($entry);
        $expected = true;
        TestCheck::assertBoolean('C.1', 'RegistryModel::getBoolean(); handle null input; default output is null', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $actual = $model->registry->getBoolean($object_eid, $name, true);
        $expected = true;
        TestCheck::assertBoolean('C.2', 'RegistryModel::getBoolean(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = true;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->getBoolean($object_eid, $name, false);
        $expected = true;
        TestCheck::assertBoolean('C.3', 'RegistryModel::getBoolean(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = false;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->getBoolean($object_eid, $name, true);
        $expected = false;
        TestCheck::assertBoolean('C.4', 'RegistryModel::getBoolean(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = false;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->getBoolean($object_eid, 'a', true);
        $expected = true;
        TestCheck::assertBoolean('C.5', 'RegistryModel::getBoolean(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = false;
        $result = $model->registry->setBoolean($object_eid, $name, $value);
        $actual = $model->registry->getBoolean('', $name, true);
        $expected = true;
        TestCheck::assertBoolean('C.6', 'RegistryModel::getBoolean(); should be sensitive to the object', $actual, $expected, $results);
    }
}
