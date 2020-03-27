<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        $model = \Flexio\Tests\Util::getModel()->registry;


        // TEST: basic expired entry cleanup

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->setString($object_eid, $name, $value, 4); // expires in 4 seconds
        $first_exists = $model->entryExists($object_eid, $name);
        $model->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\Registry::cleanupExpiredEntries(); clean up entries that have expired', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->setString($object_eid, $name, $value, 4); // expires in 4 seconds
        $first_exists = $model->entryExists($object_eid, $name);
        sleep(5);
        $model->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\Registry::cleanupExpiredEntries(); clean up entries that have expired', $actual, $expected, $results);



        // TEST: entry expiration update

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->setString($object_eid, $name, $value, 3600); // expires in an hour
        $model->expireKey($object_eid, $name, -1); // try to set expiration to one second ago
        $first_exists = $model->entryExists($object_eid, $name);
        sleep(1);
        $model->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Registry::expireKey(); make sure key expiration value is valid', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->setString($object_eid, $name, $value, 3600); // expires in an hour
        sleep(1);
        $first_exists = $model->entryExists($object_eid, $name);
        $model->expireKey($object_eid, $name, 0); // expire now
        $second_exists = $model->entryExists($object_eid, $name);
        $model->cleanupExpiredEntries($object_eid, $name);
        $third_exists = $model->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists == true && $third_exists === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Model\Registry::expireKey(); make sure keys can be expired immediately', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->setString($object_eid, $name, $value, 3600); // expires in an hour
        $model->expireKey($object_eid, $name, 4); // expire in 4 seconds
        $first_exists = $model->entryExists($object_eid, $name);
        sleep(5);
        $model->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Model\Registry::expireKey(); make sure keys expire for time that\'s set', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->setString($object_eid, $name, $value, 3600); // expires in an hour
        $model->expireKey($object_eid, $name.'a', 0); // expire now
        $first_exists = $model->entryExists($object_eid, $name);
        $model->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists == true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Model\Registry::expireKey(); make sure expiration is sensitive to name', $actual, $expected, $results);

        // BEGIN TEST
        $object_eid = \Flexio\Base\Eid::generate();
        $name = \Flexio\Base\Util::generateHandle();
        $value = 'a';
        $creation = $model->setString($object_eid, $name, $value, 3600); // expires in an hour
        $model->expireKey($object_eid.'a', $name, 0); // expire now
        $first_exists = $model->entryExists($object_eid, $name);
        $model->cleanupExpiredEntries($object_eid, $name);
        $second_exists = $model->entryExists($object_eid, $name);
        $actual = $first_exists === true && $second_exists == true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Model\Registry::expireKey(); make sure expiration is sensitive to object', $actual, $expected, $results);
    }
}
