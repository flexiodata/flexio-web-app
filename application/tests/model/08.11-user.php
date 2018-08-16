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
        // FUNCTION: \Flexio\Model\User::checkUserPassword()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: tests to check password validation

        // BEGIN TEST
        $actual = '';
        try
        {
            $actual = $model->checkUserPassword(null, null);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\User::checkUserPassword(); throw an error with a null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $actual = $model->checkUserPassword($handle,'');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\User::checkUserPassword(); return false if user cannot be found',  $actual, $expected, $results);

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Password::generate();
        $info = array(
            'username' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = $model->create($info);
        $actual = $model->checkUserPassword($username,$password.'x');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Model\User::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Password::generate();
        $info = array(
            'username' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = $model->create($info);
        $actual = $model->checkUserPassword($username.'x',$password);
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Model\User::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

        // BEGIN TEST
        $username = \Flexio\Base\Util::generateHandle();
        $email = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Password::generate();
        $info = array(
            'username' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = $model->create($info);
        $actual = $model->checkUserPassword($username,$password);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Model\User::checkUserPassword(); return true if password matches',  $actual, $expected, $results);

        // BEGIN TEST
        $username = strtoupper(\Flexio\Base\Util::generateHandle());
        $email = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Password::generate();
        $info = array(
            'username' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = $model->create($info);
        $actual = $model->checkUserPassword(strtolower($username),$password);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Model\User::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);

        // BEGIN TEST
        $username = strtolower(\Flexio\Base\Util::generateHandle());
        $email = \Flexio\Tests\Util::createEmailAddress();
        $password = \Flexio\Base\Password::generate();
        $info = array(
            'username' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = $model->create($info);
        $actual = $model->checkUserPassword(strtoupper($username),$password);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Model\User::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);
    }
}
