<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-14
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
        $model = \Flexio\Tests\Util::getModel();



        // TEST: \Model::delete(); invalid type

        // BEGIN TEST
        $delete_result = $model->delete('');
        $actual = $delete_result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Model::delete(); invalid eid should return false',  $actual, $expected, $results);



        // TEST: \Model::delete(); invalid type

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $delete_result = $model->delete($eid);
        $actual = $delete_result;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::delete(); eid that doesn\'t exist should return false',  $actual, $expected, $results);



        // TEST: \Model::delete(); valid type

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $delete_result = $model->delete($eid);
        $actual = \Flexio\Base\Eid::isValid($eid) === true && $delete_result === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Model::delete(); for object deletion, return true when object is deleted',  $actual, $expected, $results);

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_OBJECT, $info);
        $status_after_add = $model->getStatus($eid);
        $delete_result = $model->delete($eid);
        $status_after_delete = $model->getStatus($eid);
        $actual = \Flexio\Base\Eid::isValid($eid) === true && $status_after_add !== \Model::STATUS_DELETED && $status_after_delete === \Model::STATUS_DELETED;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Model::delete(); for object deletion, make sure an object is actually deleted',  $actual, $expected, $results);
    }
}
