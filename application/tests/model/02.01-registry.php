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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: string entry creation tests

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = null;
        $value = null;
        $actual = $model->registry->setString($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Registry\Model::setString(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = '';
        $value = '';
        $actual = $model->registry->setString($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Registry\Model::setString(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = null;
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $actual = $model->registry->setString($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Registry\Model::setString(); if object isn\'t specified, it should be an empty string', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = 'a';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $actual = $model->registry->setString($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.4', 'Registry\Model::setString(); if object is specified, it should be an eid', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $actual = $model->registry->setString($object_eid, $name, $value);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Registry\Model::setString(); don\'t require object to be specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = 'a';
        $actual = $model->registry->setString($object_eid, $name, $value, $expires);
        $expected = false;
        TestCheck::assertBoolean('A.6', 'Registry\Model::setString(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = -1;
        $actual = $model->registry->setString($object_eid, $name, $value, $expires);
        $expected = false;
        TestCheck::assertBoolean('A.7', 'Registry\Model::setString(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = null;
        $actual = $model->registry->setString($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.8', 'Registry\Model::setString(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $expires = 1000;
        $actual = $model->registry->setString($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.9', 'Registry\Model::setString(); set expiration time if it\'s a positive integer', $actual, $expected, $results);


        // TEST: test for existence of created values

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Registry\Model::entryExists(); handle null input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Registry\Model::entryExists(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Registry\Model::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->entryExists($object_eid, 'a');
        $expected = false;
        TestCheck::assertBoolean('B.4', 'Registry\Model::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = '';
        $result = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->entryExists('', $name);
        $expected = false;
        TestCheck::assertBoolean('B.5', 'Registry\Model::entryExists(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $entry = $model->registry->getString($object_eid, $name);
        $actual = !isset($entry);
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Registry\Model::getString(); handle null input; default output is null', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $actual = $model->registry->getString($object_eid, $name, 'value');
        $expected = 'value';
        TestCheck::assertString('C.2', 'Registry\Model::getString(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\Base\Util::generateHandle();
        $value = \Flexio\Base\Util::generateHandle();
        $result = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->getString($object_eid, $name, 'default');
        $expected = $value;
        TestCheck::assertString('C.3', 'Registry\Model::getString(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = \Flexio\Base\Util::generateHandle();
        $result = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->getString($object_eid, 'a', 'default');
        $expected = 'default';
        TestCheck::assertString('C.4', 'Registry\Model::getString(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = \Flexio\Base\Util::generateHandle();
        $result = $model->registry->setString($object_eid, $name, $value);
        $actual = $model->registry->getString('', $name, 'default');
        $expected = 'default';
        TestCheck::assertString('C.5', 'Registry\Model::getString(); should be sensitive to the object', $actual, $expected, $results);
    }
}
