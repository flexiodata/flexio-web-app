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
        // TEST: number entry creation tests

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = null;
        $value = null;
        $actual = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'RegistryModel::setNumber(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = '';
        $value = '';
        $actual = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'RegistryModel::setNumber(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = null;
        $name = Util::generateHandle();
        $value = '';
        $actual = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'RegistryModel::setNumber(); if object isn\'t specified, it should be an empty string', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = true;
        $name = Util::generateHandle();
        $value = '';
        $actual = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.4', 'RegistryModel::setNumber(); if object is specified, it should be an eid', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $actual = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'RegistryModel::setNumber(); don\'t require an object to be specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = false;
        $actual = System::getModel()->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = false;
        TestCheck::assertBoolean('A.6', 'RegistryModel::setNumber(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = 0;
        $actual = System::getModel()->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.7', 'RegistryModel::setNumber(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = null;
        $actual = System::getModel()->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.8', 'RegistryModel::setNumber(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $expires = 3600;
        $actual = System::getModel()->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.9', 'RegistryModel::setNumber(); set expiration time if it\'s a positive integer', $actual, $expected, $results);



        // TEST: test for existence of created values

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $actual = System::getModel()->registry->entryExists($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('B.1', 'RegistryModel::entryExists(); handle null input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $actual = System::getModel()->registry->entryExists($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('B.2', 'RegistryModel::entryExists(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = '';
        $result = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = System::getModel()->registry->entryExists($object_eid, $name);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'RegistryModel::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = '';
        $result = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = System::getModel()->registry->entryExists($object_eid, 'a');
        $expected = false;
        TestCheck::assertBoolean('B.4', 'RegistryModel::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = '';
        $result = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = System::getModel()->registry->entryExists('', $name);
        $expected = false;
        TestCheck::assertBoolean('B.5', 'RegistryModel::entryExists(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $entry = System::getModel()->registry->getNumber($object_eid, $name);
        $actual = !isset($entry);
        $expected = true;
        TestCheck::assertBoolean('C.1', 'RegistryModel::getNumber(); handle null input; default output is null', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $actual = System::getModel()->registry->getNumber($object_eid, $name, 0);
        $expected = 0;
        TestCheck::assertNumber('C.2', 'RegistryModel::getNumber(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = Util::generateHandle();
        $value = 3.14;
        $result = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = System::getModel()->registry->getNumber($object_eid, $name, 1);
        $expected = 3.14;
        TestCheck::assertNumber('C.3', 'RegistryModel::getNumber(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = Util::generateHandle();
        $result = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = System::getModel()->registry->getNumber($object_eid, 'a', -1);
        $expected = -1;
        TestCheck::assertNumber('C.4', 'RegistryModel::getNumber(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = Eid::generate();
        $name = Util::generateHandle();
        $value = Util::generateHandle();
        $result = System::getModel()->registry->setNumber($object_eid, $name, $value);
        $actual = System::getModel()->registry->getNumber('', $name, 2.71);
        $expected = 2.71;
        TestCheck::assertNumber('C.5', 'RegistryModel::getNumber(); should be sensitive to the object', $actual, $expected, $results);
    }
}
