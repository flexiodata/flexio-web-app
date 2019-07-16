<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
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

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_READ;
        $expected = 'object.read';
        \Flexio\Tests\Check::assertString('A.2', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_WRITE;
        $expected = 'object.write';
        \Flexio\Tests\Check::assertString('A.3', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_DELETE;
        $expected = 'object.delete';
        \Flexio\Tests\Check::assertString('A.4', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_EXECUTE;
        $expected = 'object.execute';
        \Flexio\Tests\Check::assertString('A.5', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_READ_RIGHTS;
        $expected = 'rights.read';
        \Flexio\Tests\Check::assertString('A.6', 'Right type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Object\Action::TYPE_WRITE_RIGHTS;
        $expected = 'rights.write';
        \Flexio\Tests\Check::assertString('A.7', 'Right type',  $actual, $expected, $results);



        // TEST: \Model::addRights()

        // BEGIN TEST
        $rights = json_decode('
        [
        ]
        ',true);
        $object = \Flexio\Object\Pipe::create(array());
        $object->setRights($rights);
        $actual = $object->getRights();
        $expected = array();
        \Flexio\Tests\Check::assertInArray('B.1', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]}
        ]
        ',true);
        $object = \Flexio\Object\Pipe::create(array());
        $object->setRights($rights);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]}
        ]
        ',true);
        \Flexio\Tests\Check::assertInArray('B.2', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights = json_decode('
        [
            {"access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        $object = \Flexio\Object\Pipe::create(array());
        $object->setRights($rights);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        \Flexio\Tests\Check::assertInArray('B.3', '\Model::addRights(); ',  $actual, $expected, $results);

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
        $object = \Flexio\Object\Pipe::create(array());
        $object->setRights($rights1);
        $object->setRights($rights2);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        \Flexio\Tests\Check::assertInArray('B.4', '\Model::addRights(); ',  $actual, $expected, $results);

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
        $object = \Flexio\Object\Pipe::create(array());
        $object->setRights($rights1);
        $object->setRights($rights2);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        \Flexio\Tests\Check::assertInArray('B.5', '\Model::addRights(); ',  $actual, $expected, $results);

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
        $object = \Flexio\Object\Pipe::create(array());
        $object->setRights($rights1);
        $object->setRights($rights2);
        $actual = $object->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        \Flexio\Tests\Check::assertInArray('B.6', '\Model::addRights(); ',  $actual, $expected, $results);

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
        $object = \Flexio\Object\Pipe::create(array());
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
        \Flexio\Tests\Check::assertInArray('B.7', '\Model::addRights(); ',  $actual, $expected, $results);

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
        $object = \Flexio\Object\Pipe::create(array());
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
        \Flexio\Tests\Check::assertInArray('B.8', '\Model::addRights(); ',  $actual, $expected, $results);

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
        $object1 = \Flexio\Object\Pipe::create(array());
        $object2 = \Flexio\Object\Pipe::create(array());
        $object1->setRights($rights1);
        $object2->setRights($rights2);
        $actual = $object1->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object1->getEid().'", "access_type": "a", "access_code": "b", "actions": ["object.read"]},
            {"object_eid": "'.$object1->getEid().'", "access_type": "b", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        \Flexio\Tests\Check::assertInArray('B.9', '\Model::addRights(); ',  $actual, $expected, $results);

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
        $object1 = \Flexio\Object\Pipe::create(array());
        $object2 = \Flexio\Object\Pipe::create(array());
        $object1->setRights($rights1);
        $object2->setRights($rights2);
        $actual = $object2->getRights();
        $expected = json_decode('
        [
            {"object_eid": "'.$object2->getEid().'", "access_type": "a", "access_code": "c", "actions": ["object.read"]},
            {"object_eid": "'.$object2->getEid().'", "access_type": "c", "access_code": "d", "actions": ["object.write"]}
        ]
        ',true);
        \Flexio\Tests\Check::assertInArray('B.10', '\Model::addRights(); ',  $actual, $expected, $results);
    }
}
