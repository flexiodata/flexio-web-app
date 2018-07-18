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
        // FUNCTION: \Flexio\Model\User::create()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: creation with invalid input

        // BEGIN TEST
        $actual = array();
        try
        {
            $info = array(
            );
            $eid = $model->create($info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_SYNTAX
        );
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Model\User::create(); throw an exception with invalid input',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Tests\Util::createEmailAddress();
            $info = array(
                'email' => $handle
            );
            $eid = $model->create($info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_SYNTAX
        );
        \Flexio\Tests\Check::assertInArray('A.2', '\Flexio\Model\User::create(); throw an exception if a username isn\'t specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle = \Flexio\Base\Util::generateHandle();
            $info = array(
                'username' => $handle
            );
            $eid = $model->create($info);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::INVALID_SYNTAX
        );
        \Flexio\Tests\Check::assertInArray('A.3', '\Flexio\Model\User::create(); throw an exception if an email isn\'t specified',  $actual, $expected, $results);


        // TEST: creation with basic username input

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\User::create(); make sure a valid eid is returned when user is created',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = \Flexio\Tests\Util::createEmailAddress();
            $info1 = array(
                'username' => $handle1,
                'email' => $handle2
            );
            $info2 = array(
                'username' => $handle1,
                'email' => $handle2
            );
            $eid_first_time_creation = $model->create($info1);
            $eid_second_time_creation = $model->create($info2);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::CREATE_FAILED
        );
        \Flexio\Tests\Check::assertInArray('B.2', '\Flexio\Model\User::create(); do not allow multiple users with the same username',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = array();
        try
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = \Flexio\Tests\Util::createEmailAddress();
            $info1 = array(
                'username' => $handle1 . 'a',
                'email' => $handle2
            );
            $info2 = array(
                'username' => $handle1 . 'b',
                'email' => $handle2
            );
            $eid_first_time_creation = $model->create($info1);
            $eid_second_time_creation = $model->create($info2);
        }
        catch (\Flexio\Base\Exception $e)
        {
            $message = $e->getMessage();
            $actual = json_decode($message,true);
        }
        $expected = array(
            'code' => \Flexio\Base\Error::CREATE_FAILED
        );
        \Flexio\Tests\Check::assertInArray('B.3', '\Flexio\Model\User::create(); do not allow multiple users with the same email',  $actual, $expected, $results);
    }
}
