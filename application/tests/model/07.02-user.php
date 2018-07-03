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


        // TEST: \Flexio\Model\User::create(); multiple unique user creation

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
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\User::create(); creating users should succeed and produce a unique eid for each new user',  $actual, $expected, $results);




        // FUNCTION: \Flexio\Model\User::checkPasswordHash()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: tests for empty string input

        // BEGIN TEST
        $actual = $model->checkPasswordHash('','');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Model\User::checkPasswordHash(): empty string input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}b5e06a0994664b8674c182864515de4dc44333b0','');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\User::checkPasswordHash(): hash for empty string input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}b5e06a0994664b8674c182864515de4dc44333b0',' '); // check for trimming
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Model\User::checkPasswordHash(): single space password; check that spaces arent trimmed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('b5e06a0994664b8674c182864515de4dc44333b0',''); // check for leading SSHA prefix
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Model\User::checkPasswordHash(): check for hash identifier', $actual, $expected, $results);



        // TEST: tests for non-empty string input

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}87a0f0cfc2cd5b68a9a3b5a3937ca1211227a542','test');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Model\User::checkPasswordHash(): basic non-empty string input', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}87a0f0cfc2cd5b68a9a3b5a3937ca1211227a542','test '); // don't allow trimming after
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Model\User::checkPasswordHash(): non-empty string input; check that spaces after password are not trimmed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}87a0f0cfc2cd5b68a9a3b5a3937ca1211227a542',' test'); // don't allow trimming before
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Model\User::checkPasswordHash(): non-empty string input; check that spaces before password are not trimmed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}3226155047ca866b1724d14f2e8167aa2ef88afb','mj6dc95k99tc');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Model\User::checkPasswordHash(): non-trivial password check', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}3226155047ca866b1724d14f2e8167aa2ef88afb','mj6dc95k99tcd');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Model\User::checkPasswordHash(): non-trivial password check; password length sensitivity', $actual, $expected, $results);

        // BEGIN TEST
        $actual = $model->checkPasswordHash('{SSHA}3226155047ca866b1724d14f2e8167aa2ef88afb','mj6dc95k99t');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Model\User::checkPasswordHash(): non-trivial password check; password length sensitivity', $actual, $expected, $results);




        // TEST: \Flexio\Model\User::getUsernameFromEid(); test ability to get the username from the eid

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
        \Flexio\Tests\Check::assertString('C.1', '\Flexio\Model\User::getUsernameFromEid(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $username = $model->getUsernameFromEid('xxxxxxxxxxxx');
        $actual = $username === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('C.2', '\Flexio\Model\User::getUsernameFromEid(); return false when username can\'t be found',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('C.3', '\Flexio\Model\User::getUsernameFromEid(); use the eid to get the username',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('C.4', '\Flexio\Model\User::getUsernameFromEid(); make sure username is case insensitive',  $actual, $expected, $results);



        // TEST: \Flexio\Model\User::getEmailFromEid(); test ability to get the user email from the eid

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
        \Flexio\Tests\Check::assertString('D.1', '\Flexio\Model\User::getEmailFromEid(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $email = $model->getEmailFromEid('xxxxxxxxxxxx');
        $actual = $email === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', '\Flexio\Model\User::getEmailFromEid(); return false when a user\'s email can\'t be found',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('D.3', '\Flexio\Model\User::getEmailFromEid(); use the eid to get the user\'s email',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('D.4', '\Flexio\Model\User::getEmailFromEid(); make sure the returned email is case insensitive',  $actual, $expected, $results);



        // TEST: \Flexio\Model\User::getEidFromIdentifier(); test ability to get the eid from the username or email

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
        \Flexio\Tests\Check::assertString('E.1', '\Flexio\Model\User::getEidFromIdentifier(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $eid = $model->getEidFromIdentifier($handle);
        $actual = $eid === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('E.2', '\Flexio\Model\User::getEidFromIdentifier(); return false when eid can\'t be found',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('E.3', '\Flexio\Model\User::getEidFromIdentifier(); use the username to get the eid',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('E.4', '\Flexio\Model\User::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('E.5', '\Flexio\Model\User::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);



        // TEST: \Flexio\Model\User::checkUserPassword; tests to check password validation

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
        \Flexio\Tests\Check::assertString('F.1', '\Flexio\Model\User::checkUserPassword(); throw an error with a null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $actual = $model->checkUserPassword($handle,'');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.2', '\Flexio\Model\User::checkUserPassword(); return false if user cannot be found',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.3', '\Flexio\Model\User::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.4', '\Flexio\Model\User::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.5', '\Flexio\Model\User::checkUserPassword(); return true if password matches',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.6', '\Flexio\Model\User::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('F.7', '\Flexio\Model\User::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);
    }
}
