<?php
/**
 *
 * Copyright (c) 2018, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-08-15
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
        // FUNCTION: \Flexio\Model\Stream::delete()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->stream;


        // TEST: non-eid input

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
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Stream::delete(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->delete('');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\Stream::delete(); return false with invalid input',  $actual, $expected, $results);


        // TEST: valid eid input, but object doesn't exist

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->delete($eid);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Stream::delete(); return false after trying to delete an object that doesn\'t exist',  $actual, $expected, $results);


        // TEST: valid eid input, and object exists

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create($info);
        $actual = $model->delete($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Model\Stream::delete(); return true when deleting an object that exists',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create($info);
        $status_before_deletion = $model->get($eid)['eid_status'];
        $delete_result = $model->delete($eid);
        $status_after_deletion = $model->get($eid)['eid_status'];
        $actual = $delete_result === true && $status_before_deletion !== \Model::STATUS_DELETED && $status_after_deletion === \Model::STATUS_DELETED;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Model\Stream::delete(); when deleting, make sure object is deleted',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'name' => $handle
        );
        $eid = $model->create($info);
        $status_before_deletion = $model->get($eid)['eid_status'];
        $first_deletion = $model->delete($eid);
        $second_deletion = $model->delete($eid);
        $status_after_deletion = $model->get($eid)['eid_status'];
        $actual = $status_before_deletion !== \Model::STATUS_DELETED && $status_after_deletion === \Model::STATUS_DELETED && $first_deletion === true && $second_deletion === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.3', '\Flexio\Model\Stream::delete(); multiple deletion should succeed',  $actual, $expected, $results);
    }
}
