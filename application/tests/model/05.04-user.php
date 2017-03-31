<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-07
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



        // TEST: \Model::create(); when creating a user, make sure the required parameters are valid

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = TestUtil::generateEmail();
            $info = array(
                'user_name' => null,
                'email' => $handle2,
                'password' => $handle1
            );
            $eid = $model->create(\Model::TYPE_USER, $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::MISSING_PARAMETER
        );
        TestCheck::assertInArray('A.1', '\Model::create(); throw an exception when a user_name is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = TestUtil::generateEmail();
            $info = array(
                'user_name' => 'ab',
                'email' => $handle2,
                'password' => $handle1
            );
            $eid = $model->create(\Model::TYPE_USER, $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        TestCheck::assertInArray('A.2', '\Model::create(); throw an exception when a user_name is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = TestUtil::generateEmail();
            $info = array(
                'user_name' => $handle1,
                'email' => null,
                'password' => $handle1
            );
            $eid = $model->create(\Model::TYPE_USER, $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::MISSING_PARAMETER
        );
        TestCheck::assertInArray('A.3', '\Model::create(); throw an exception when a user_name is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = TestUtil::generateEmail();
            $info = array(
                'user_name' => $handle1,
                'email' => 'abc',
                'password' => $handle1
            );
            $eid = $model->create(\Model::TYPE_USER, $info);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        TestCheck::assertInArray('A.4', '\Model::create(); throw an exception when a user_name is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('A.5', '\Model::create(); make sure that a valid eid is returned on success',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->get($eid);
        $expected = array(
            'eid' => $eid,
            'user_name' => $handle1,
            'email' => $handle2
        );
        TestCheck::assertInArray('A.6', '\Model::create(); make sure that eid and user_name are set when a user is created',  $actual, $expected, $results);
    }
}
