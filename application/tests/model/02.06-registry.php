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



        // TEST: basic expired entry cleanup

        // BEGIN TEST
        $object_eid = \Eid::generate();
        $name = \Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value, 4); // expires in 4 seconds
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $model->registry->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('A.1', 'Registry\Model::cleanupExpiredEntries(); clean up entries that have expired', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Eid::generate();
        $name = \Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value, 4); // expires in 4 seconds
        $first_exists = $model->registry->entryExists($object_eid, $name);
        sleep(5);
        $model->registry->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists === false;
        $expected = true;
        TestCheck::assertBoolean('A.2', 'Registry\Model::cleanupExpiredEntries(); clean up entries that have expired', $actual, $expected, $results);



        // TEST: entry expiration update

        // BEGIN TEST
        $object_eid = \Eid::generate();
        $name = \Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value, 3600); // expires in an hour
        $model->registry->expireKey($object_eid, $name, -1); // try to set expiration to one second ago
        $first_exists = $model->registry->entryExists($object_eid, $name);
        sleep(1);
        $model->registry->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists === true;
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Registry\Model::expireKey(); make sure key expiration value is valid', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Eid::generate();
        $name = \Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value, 3600); // expires in an hour
        sleep(1);
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $model->registry->expireKey($object_eid, $name, 0); // expire now
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $model->registry->cleanupExpiredEntries($object_eid, $name);
        $third_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists == true && $third_exists === false;
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Registry\Model::expireKey(); make sure keys can be expired immediately', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Eid::generate();
        $name = \Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value, 3600); // expires in an hour
        $model->registry->expireKey($object_eid, $name, 4); // expire in 4 seconds
        $first_exists = $model->registry->entryExists($object_eid, $name);
        sleep(5);
        $model->registry->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists === false;
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Registry\Model::expireKey(); make sure keys expire for time that\'s set', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Eid::generate();
        $name = \Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value, 3600); // expires in an hour
        $model->registry->expireKey($object_eid, $name.'a', 0); // expire now
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $model->registry->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists == true;
        $expected = true;
        TestCheck::assertBoolean('B.4', 'Registry\Model::expireKey(); make sure expiration is sensitive to name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Eid::generate();
        $name = \Util::generateHandle();
        $value = 'a';
        $creation = $model->registry->setString($object_eid, $name, $value, 3600); // expires in an hour
        $model->registry->expireKey($object_eid.'a', $name, 0); // expire now
        $first_exists = $model->registry->entryExists($object_eid, $name);
        $model->registry->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->registry->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists == true;
        $expected = true;
        TestCheck::assertBoolean('B.5', 'Registry\Model::expireKey(); make sure expiration is sensitive to object', $actual, $expected, $results);
    }
}
