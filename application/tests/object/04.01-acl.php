<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-05-03
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
        // TODO: add tests for grant, revoke, etc


        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::addRights()

        // BEGIN TEST
        $rights = json_decode('
        [
        ]
        ',true);
        $object = \Flexio\Object\Object::create(array());
        $object->setRights($rights);
        $actual = $object->getRights();
        $expected = array();
        TestCheck::assertInArray('A.1', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]}
        ]
        ',true);
        $object = \Flexio\Object\Object::create(array());
        $object->setRights($rights);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]}
        ]
        ',true);
        TestCheck::assertInArray('A.2', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $object = \Flexio\Object\Object::create(array());
        $object->setRights($rights);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        TestCheck::assertInArray('A.3', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]}
        ]
        ',true);
        $object = \Flexio\Object\Object::create(array());
        $object->setRights($rights1);
        $object->setRights($rights2);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        TestCheck::assertInArray('A.4', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $object = \Flexio\Object\Object::create(array());
        $object->setRights($rights1);
        $object->setRights($rights2);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        TestCheck::assertInArray('A.5', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $object = \Flexio\Object\Object::create(array());
        $object->setRights($rights1);
        $object->setRights($rights2);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        TestCheck::assertInArray('A.6', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "c", "access_code": "d", "actions": ["object.read"]}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $object = \Flexio\Object\Object::create(array());
        $object->setRights($rights1);
        $object->setRights($rights2);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        TestCheck::assertInArray('A.7', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"access_type": "b", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "c", "actions": ["object.read"]},
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $object = \Flexio\Object\Object::create(array());
        $object->setRights($rights1);
        $object->setRights($rights2);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "b", "access_code": "d", "actions": ["object.write"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "c", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        TestCheck::assertInArray('A.8', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"access_type": "b", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "c", "actions": ["object.read"]},
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $object1 = \Flexio\Object\Object::create(array());
        $object2 = \Flexio\Object\Object::create(array());
        $object1->setRights($rights1);
        $object2->setRights($rights2);
        $actual = $object1->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object1->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object1->getEid().'", "access_type": "b", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        TestCheck::assertInArray('A.9', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"access_type": "b", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "c", "actions": ["object.read"]},
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $object1 = \Flexio\Object\Object::create(array());
        $object2 = \Flexio\Object\Object::create(array());
        $object1->setRights($rights1);
        $object2->setRights($rights2);
        $actual = $object2->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object2->getEid().'", "access_type": "a", "access_code": "c", "actions": ["object.read"]},
            {"object_eid": "'.$object2->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        TestCheck::assertInArray('A.10', '\Model::addRights(); ',  $actual, $expected, $results);
    }
}
