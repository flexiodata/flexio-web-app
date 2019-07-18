<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        // FUNCTION: \Flexio\Model\Token::purge()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->token;


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
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\Token::purge(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->purge('');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\Token::purge(); return false with invalid input',  $actual, $expected, $results);


        // TEST: valid eid input, but object doesn't exist

        // BEGIN TEST
        $eid = \Flexio\Base\Eid::generate();
        $actual = $model->purge($eid);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\Token::purge(); return false after trying to purge an object that doesn\'t exist',  $actual, $expected, $results);


        // TEST: valid eid input, and object exists

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'access_code' => $handle
        );
        $eid = $model->create($info);
        $model->set($eid, array('owned_by' => $eid));
        $actual = $model->purge($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Model\Token::purge(); return true when purging an object that exists',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Base\Util::generateHandle();
        $info1 = array(
            'access_code' => $handle1
        );
        $eid1 = $model->create($info1);
        $model->set($eid1, array('owned_by' => $eid1));
        $info2 = array(
            'access_code' => $handle2
        );
        $eid2 = $model->create($info2);
        $model->set($eid2, array('owned_by' => $eid2));
        $exists1_before_deletion = $model->exists($eid1);
        $exists2_before_deletion = $model->exists($eid2);
        $delete1_result = $model->purge($eid1);
        $exists1_after_deletion = $model->exists($eid1);
        $exists2_after_deletion = $model->exists($eid2);
        $actual = $exists1_before_deletion === true && $exists1_after_deletion === false && $exists2_before_deletion === true && $exists2_after_deletion === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Model\Token::purge(); when purging, make sure object being purged is physically removed and that other objects are not effected',  $actual, $expected, $results);
    }
}