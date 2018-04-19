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
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: \Model::create(); user creation with invalid input

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
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        \Flexio\Tests\Check::assertInArray('A.1', '\Model::create(); throw an exception with invalid input',  $actual, $expected, $results);

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
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        \Flexio\Tests\Check::assertInArray('A.2', '\Model::create(); throw an exception if a username isn\'t specified',  $actual, $expected, $results);

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
            'code' => \Flexio\Base\Error::INVALID_PARAMETER
        );
        \Flexio\Tests\Check::assertInArray('A.3', '\Model::create(); throw an exception if an email isn\'t specified',  $actual, $expected, $results);



        // TEST: \Model::create(); user creation with basic username input

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
        \Flexio\Tests\Check::assertBoolean('B.1', '\Model::create(); make sure a valid eid is returned when user is created',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('B.2', '\Model::create(); do not allow multiple users with the same username',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('B.3', '\Model::create(); do not allow multiple users with the same email',  $actual, $expected, $results);



        // TEST: \Model::getUsernameFromEid(); test ability to get the username from the eid

        // BEGIN TEST
        $actual = '';
        try
        {
            $username = $model->getUsernameFromEid(null);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('C.1', 'User\Model::getUsernameFromEid(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $username = $model->getUsernameFromEid('xxxxxxxxxxxx');
        $actual = $username === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', 'User\Model::getUsernameFromEid(); return false when username can\'t be found',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $model->getUsernameFromEid($eid);
        $expected = $handle1;
        \Flexio\Tests\Check::assertString('C.3', '\Model::getUsernameFromEid(); use the eid to get the username',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = strtoupper(\Flexio\Base\Util::generateHandle());
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $model->getUsernameFromEid($eid);
        $expected = strtolower($handle1);
        \Flexio\Tests\Check::assertString('C.4', '\Model::getUsernameFromEid(); make sure username is case insensitive',  $actual, $expected, $results);



        // TEST: \Model::getEmailFromEid(); test ability to get the user email from the eid

        // BEGIN TEST
        $actual = '';
        try
        {
            $email = $model->getEmailFromEid(null);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('D.1', 'User\Model::getEmailFromEid(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $email = $model->getEmailFromEid('xxxxxxxxxxxx');
        $actual = $email === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', 'User\Model::getEmailFromEid(); return false when a user\'s email can\'t be found',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $model->getEmailFromEid($eid);
        $expected = $handle2;
        \Flexio\Tests\Check::assertString('D.3', '\Model::getEmailFromEid(); use the eid to get the user\'s email',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = strtoupper(\Flexio\Tests\Util::createEmailAddress());
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $model->getEmailFromEid($eid);
        $expected = strtolower($handle2);
        \Flexio\Tests\Check::assertString('D.4', '\Model::getEmailFromEid(); make sure the returned email is case insensitive',  $actual, $expected, $results);



        // TEST: \Model::getEidFromIdentifier(); test ability to get the eid from the username or email

        // BEGIN TEST
        $actual = '';
        try
        {
            $eid = $model->getEidFromIdentifier(null);
            $actual = \Flexio\Tests\Base::ERROR_NO_EXCEPTION;
        }
        catch (\Error $e)
        {
            $actual = \Flexio\Tests\Base::ERROR_EXCEPTION;
        }
        $expected = \Flexio\Tests\Base::ERROR_EXCEPTION;
        \Flexio\Tests\Check::assertString('E.1', 'User\Model::getEidFromIdentifier(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $eid = $model->getEidFromIdentifier($handle);
        $actual = $eid === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.2', 'User\Model::getEidFromIdentifier(); return false when eid can\'t be found',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $eid;
        $expected = $model->getEidFromIdentifier($handle1);
        \Flexio\Tests\Check::assertString('E.3', '\Model::getEidFromIdentifier(); use the username to get the eid',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = strtoupper(\Flexio\Base\Util::generateHandle());
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $eid;
        $expected = $model->getEidFromIdentifier(strtolower($handle1));
        \Flexio\Tests\Check::assertString('E.4', '\Model::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);

        // BEGIN TEST
        $handle1 = strtolower(\Flexio\Base\Util::generateHandle());
        $handle2 = \Flexio\Tests\Util::createEmailAddress();
        $info = array(
            'username' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create($info);
        $actual = $eid;
        $expected = $model->getEidFromIdentifier(strtoupper($handle1));
        \Flexio\Tests\Check::assertString('E.5', '\Model::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);



        // TEST: \Model::checkUserPassword; tests to check password validation

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
        \Flexio\Tests\Check::assertString('F.1', 'User\Model::checkUserPassword(); throw an error with a null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $actual = $model->checkUserPassword($handle,'');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.2', 'User\Model::checkUserPassword(); return false if user cannot be found',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.3', 'User\Model::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.4', 'User\Model::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.5', 'User\Model::checkUserPassword(); return true if password matches',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.6', 'User\Model::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.7', 'User\Model::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);
    }
}
