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



        // TEST: number entry creation tests

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = null;
        $value = null;
        $actual = $model->registry->setNumber($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Registry\Model::setNumber(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = '';
        $value = '';
        $actual = $model->registry->setNumber($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.2', 'Registry\Model::setNumber(); return false when no name is specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = null;
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $actual = $model->registry->setNumber($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Registry\Model::setNumber(); if object isn\'t specified, it should be an empty string', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = true;
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $actual = $model->registry->setNumber($object_eid, $name, $value);
        $expected = false;
        TestCheck::assertBoolean('A.4', 'Registry\Model::setNumber(); if object is specified, it should be an eid', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $actual = $model->registry->setNumber($object_eid, $name, $value);
        $expected = true;
        TestCheck::assertBoolean('A.5', 'Registry\Model::setNumber(); don\'t require an object to be specified', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $expires = false;
        $actual = $model->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = false;
        TestCheck::assertBoolean('A.6', 'Registry\Model::setNumber(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $expires = 0;
        $actual = $model->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.7', 'Registry\Model::setNumber(); when specified, expiration time should be non-negative integer', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $expires = null;
        $actual = $model->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.8', 'Registry\Model::setNumber(); ignore null expiration times', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $expires = 3600;
        $actual = $model->registry->setNumber($object_eid, $name, $value, $expires);
        $expected = true;
        TestCheck::assertBoolean('A.9', 'Registry\Model::setNumber(); set expiration time if it\'s a positive integer', $actual, $expected, $results);



        // TEST: test for existence of created values

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('B.1', 'Registry\Model::entryExists(); handle null input', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\System\Util::generateHandle();
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = false;
        TestCheck::assertBoolean('B.2', 'Registry\Model::entryExists(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $result = $model->registry->setNumber($object_eid, $name, $value);
        $actual = $model->registry->entryExists($object_eid, $name);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Registry\Model::entryExists(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $result = $model->registry->setNumber($object_eid, $name, $value);
        $actual = $model->registry->entryExists($object_eid, 'a');
        $expected = false;
        TestCheck::assertBoolean('B.4', 'Registry\Model::entryExists(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\System\Util::generateHandle();
        $value = '';
        $result = $model->registry->setNumber($object_eid, $name, $value);
        $actual = $model->registry->entryExists('', $name);
        $expected = false;
        TestCheck::assertBoolean('B.5', 'Registry\Model::entryExists(); should be sensitive to the object', $actual, $expected, $results);



        // TEST: test for readability of created entries

        // BEGIN TEST
        $object_eid = null;
        $name = null;
        $entry = $model->registry->getNumber($object_eid, $name);
        $actual = !isset($entry);
        $expected = true;
        TestCheck::assertBoolean('C.1', 'Registry\Model::getNumber(); handle null input; default output is null', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\System\Util::generateHandle();
        $actual = $model->registry->getNumber($object_eid, $name, 0);
        $expected = 0;
        TestCheck::assertNumber('C.2', 'Registry\Model::getNumber(); with no entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = '';
        $name = \Flexio\System\Util::generateHandle();
        $value = 3.14;
        $result = $model->registry->setNumber($object_eid, $name, $value);
        $actual = $model->registry->getNumber($object_eid, $name, 1);
        $expected = 3.14;
        TestCheck::assertNumber('C.3', 'Registry\Model::getNumber(); with existing entry', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\System\Util::generateHandle();
        $value = \Flexio\System\Util::generateHandle();
        $result = $model->registry->setNumber($object_eid, $name, $value);
        $actual = $model->registry->getNumber($object_eid, 'a', -1);
        $expected = -1;
        TestCheck::assertNumber('C.4', 'Registry\Model::getNumber(); should be sensitive to the name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\System\Util::generateHandle();
        $value = \Flexio\System\Util::generateHandle();
        $result = $model->registry->setNumber($object_eid, $name, $value);
        $actual = $model->registry->getNumber('', $name, 2.71);
        $expected = 2.71;
        TestCheck::assertNumber('C.5', 'Registry\Model::getNumber(); should be sensitive to the object', $actual, $expected, $results);
    }
}
