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
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::addRights()

        // BEGIN TEST
        $rights = json_decode('
        [
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights);
        $actual = $model->getRights($eid);
        $expected = array();
        TestCheck::assertArray('A.1', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"}
        ]
        ',true);
        TestCheck::assertArray('A.2', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"},
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('A.3', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights1);
        $model->addRights($eid, $rights2);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"},
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('A.4', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights1);
        $model->addRights($eid, $rights2);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"},
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('A.5', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights1);
        $model->addRights($eid, $rights2);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"},
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('A.6', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights1);
        $model->addRights($eid, $rights2);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"},
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"}
        ]
        ',true);
        TestCheck::assertArray('A.7', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "b", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "c", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights1);
        $model->addRights($eid, $rights2);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"},
            {"object_eid": "'.$eid.'", "access_type": "b", "access_code": "d", "action": "action.write"},
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "c", "action": "action.read"},
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('A.8', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "b", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "c", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid1 = $model->create(\Model::TYPE_OBJECT, array());
        $eid2 = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid1, $rights1);
        $model->addRights($eid2, $rights2);
        $actual = $model->getRights($eid1);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid1.'", "access_type": "a", "access_code": "b", "action": "action.read"},
            {"object_eid": "'.$eid1.'", "access_type": "b", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('A.9', '\Model::addRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights1 = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "b", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights2 = json_decode('
        [
            {"access_type": "a", "access_code": "c", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid1 = $model->create(\Model::TYPE_OBJECT, array());
        $eid2 = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid1, $rights1);
        $model->addRights($eid2, $rights2);
        $actual = $model->getRights($eid2);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid2.'", "access_type": "a", "access_code": "c", "action": "action.read"},
            {"object_eid": "'.$eid2.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('A.10', '\Model::addRights(); ',  $actual, $expected, $results);



        // TEST: \Model::deleteRights()

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights_add);
        $model->deleteRights($eid, $rights_delete);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"},
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('B.1', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights_add);
        $model->deleteRights($eid, $rights_delete);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"}
        ]
        ',true);
        TestCheck::assertArray('B.2', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights_add);
        $model->deleteRights($eid, $rights_delete);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('B.3', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights_add);
        $model->deleteRights($eid, $rights_delete);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
        ]
        ',true);
        TestCheck::assertArray('B.4', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "c", "access_code": "d", "action": "action.write"},
            {"access_type": "a", "access_code": "b", "action": "action.read"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights_add);
        $model->deleteRights($eid, $rights_delete);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
        ]
        ',true);
        TestCheck::assertArray('B.5', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "f", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "a", "access_code": "f", "action": "action.read"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights_add);
        $model->deleteRights($eid, $rights_delete);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('B.6', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "f", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights_add);
        $model->deleteRights($eid, $rights_delete);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "f", "action": "action.read"}
        ]
        ',true);
        TestCheck::assertArray('B.7', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "c", "access_code": "d", "action": "action.execute"},
            {"access_type": "a", "access_code": "f", "action": "action.read"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights_add);
        $model->deleteRights($eid, $rights_delete);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid.'", "access_type": "a", "access_code": "b", "action": "action.read"},
            {"object_eid": "'.$eid.'", "access_type": "c", "access_code": "d", "action": "action.write"}
        ]
        ',true);
        TestCheck::assertArray('B.8', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "b", "action": "action.read"},
            {"access_type": "c", "access_code": "d", "action": "action.write"},
            {"access_type": "c", "access_code": "d", "action": "action.execute"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "c", "access_code": "d", "action": "action.write"},
            {"access_type": "c", "access_code": "d", "action": "action.execute"},
            {"access_type": "a", "access_code": "b", "action": "action.read"}
        ]
        ',true);
        $eid = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid, $rights_add);
        $model->deleteRights($eid, $rights_delete);
        $actual = $model->getRights($eid);
        $expected = json_decode('
        [
        ]
        ',true);
        TestCheck::assertArray('B.9', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "f", "action": "action.read"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "a", "access_code": "f", "action": "action.read"}
        ]
        ',true);
        $eid1 = $model->create(\Model::TYPE_OBJECT, array());
        $eid2 = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid1, $rights_add);
        $model->deleteRights($eid1, $rights_delete);
        $actual = $model->getRights($eid1);
        $expected = json_decode('
        [
        ]
        ',true);
        TestCheck::assertArray('B.10', '\Model::deleteRights(); ',  $actual, $expected, $results);

        // BEGIN TEST
        $rights_add = json_decode('
        [
            {"access_type": "a", "access_code": "f", "action": "action.read"}
        ]
        ',true);
        $rights_delete = json_decode('
        [
            {"access_type": "a", "access_code": "f", "action": "action.read"}
        ]
        ',true);
        $eid1 = $model->create(\Model::TYPE_OBJECT, array());
        $eid2 = $model->create(\Model::TYPE_OBJECT, array());
        $model->addRights($eid1, $rights_add);
        $model->deleteRights($eid2, $rights_delete);
        $actual = $model->getRights($eid1);
        $expected = json_decode('
        [
            {"object_eid": "'.$eid1.'", "access_type": "a", "access_code": "f", "action": "action.read"}
        ]
        ',true);
        TestCheck::assertArray('B.11', '\Model::deleteRights(); ',  $actual, $expected, $results);
    }
}
