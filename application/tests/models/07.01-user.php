<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
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


        // TEST: creation with no parameters

        // BEGIN TEST
        $info = array(
        );
        $eid = $model->create($info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\User::create(); allow creation with no input; empty username and email are allowed, but if they\'re specified they need to be both valid and unique',  $actual, $expected, $results);


        // TEST: creation with basic input

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


        // TEST: multiple unique user creation

        // BEGIN TEST
        $total_count = 1000;
        $created_eids = array();
        $failed_user_creation = 0;
        for ($i = 0; $i < $total_count; $i++)
        {
            $handle1 = \Flexio\Base\Util::generateHandle();
            $handle2 = \Flexio\Tests\Util::createEmailAddress();
            $info = array(
                'username' => $handle1,
                'email' => $handle2
            );
            $eid = $model->create($info);
            $created_eids[$eid] = 1;
            if (!\Flexio\Base\Eid::isValid($eid))
                $failed_user_creation++;
        }
        $actual = count($created_eids) == $total_count && $failed_user_creation == 0;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.1', '\Flexio\Model\User::create(); creating users should succeed and produce a unique eid for each new user',  $actual, $expected, $results);
    }
}
