<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-11
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
        $model = \Flexio\Tests\Util::getModel()->connection;
        $base_model = \Flexio\Tests\Util::getModel();


        // TEST: delete tests with non-eid input

        // BEGIN TEST
        $actual = '';
        try
        {
            $model->delete(null);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Model::delete(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->delete('');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Model::delete(); return false with invalid input',  $actual, $expected, $results);



        // TEST: delete tests with valid eid input, but object doesn't exist

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->delete($eid);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::delete(); return false after trying to delete an object that doesn\'t exist',  $actual, $expected, $results);



        // TEST: delete tests with valid eid input, and object exists

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create($info);
        $actual = $model->delete($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Model::delete(); return true when deleting an object that exists',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create($info);
        $status_before_deletion = $base_model->getStatus($eid);
        $delete_result = $model->delete($eid);
        $status_after_deletion = $base_model->getStatus($eid);
        $actual = $delete_result === true && $status_before_deletion !== \Model::STATUS_DELETED && $status_after_deletion === \Model::STATUS_DELETED;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Model::delete(); when deleting, make sure object is deleted',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create($info);
        $status_before_deletion = $base_model->getStatus($eid);
        $first_deletion = $model->delete($eid);
        $second_deletion = $model->delete($eid);
        $status_after_deletion = $base_model->getStatus($eid);
        $actual = $status_before_deletion !== \Model::STATUS_DELETED && $status_after_deletion === \Model::STATUS_DELETED && $first_deletion === true && $second_deletion === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Model::delete(); multiple deletion should succeed',  $actual, $expected, $results);
    }
}
