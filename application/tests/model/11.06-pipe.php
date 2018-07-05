<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-23
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
        // FUNCTION: \Flexio\Model\Pipe::purge()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->pipe;


        // TEST: non-eid input

        // BEGIN TEST
        $actual = '';
        try
        {
            $model->purge(null);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Pipe::purge(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->purge('');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\Pipe::purge(); return false with invalid input',  $actual, $expected, $results);


        // TEST: valid eid input, but object doesn't exist

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->purge($eid);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Pipe::purge(); return false after trying to purge an object that doesn\'t exist',  $actual, $expected, $results);


        // TEST: valid eid input, and object exists

        // BEGIN TEST
        $info = array();
        $eid = $model->create($info);
        $model->set($eid, array('owned_by' => $eid));
        $actual = $model->purge($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Model\Pipe::purge(); return true when purging an object that exists',  $actual, $expected, $results);

        // BEGIN TEST
        $info1 = array();
        $eid1 = $model->create($info1);
        $model->set($eid1, array('owned_by' => $eid1));
        $info2 = array();
        $eid2 = $model->create($info2);
        $model->set($eid2, array('owned_by' => $eid2));
        $info1_before_deletion = $model->get($eid1);
        $info2_before_deletion = $model->get($eid2);
        $delete1_result = $model->purge($eid1);
        $info1_after_deletion = $model->get($eid1);
        $info2_after_deletion = $model->get($eid2);
        $actual = $info1_before_deletion['eid'] === $eid1 && $info1_after_deletion === false && $info2_before_deletion['eid'] === $eid2 && $info2_after_deletion['eid'] === $eid2;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Model\Pipe::purge(); when purging, make sure object being purged is physically and that other objects are not effected',  $actual, $expected, $results);
    }
}
