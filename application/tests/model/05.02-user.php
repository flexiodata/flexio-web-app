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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // SETUP
        $model = TestUtil::getModel();



        // TEST: \Model::create(); user creation with invalid input

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Model::create(); invalid input should return false',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $info = array(
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->getErrors();
        $expected = array(array(
            'code' => \Flexio\Base\Error::MISSING_PARAMETER,
            'message' => 'Missing user_name parameter'
        ));
        TestCheck::assertInArray('A.2', '\Model::create(); invalid input should set an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = TestUtil::generateEmail();
        $info = array(
            'email' => $handle
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Model::create(); when creating a user, the user_name is required; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $info = array(
            'user_name' => $handle
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Model::create(); when creating a user, an email address is required; return false otherwise',  $actual, $expected, $results);


        // TEST: \Model::create(); user creation with basic user_name input
        $model->clearErrors();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = \Flexio\Base\Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Model::create(); make sure valid eid is returned when user is created',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info1 = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $info2 = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid_first_time_creation = $model->create(\Model::TYPE_USER, $info1);
        $eid_second_time_creation = $model->create(\Model::TYPE_USER, $info2);
        $actual = (\Flexio\Base\Eid::isValid($eid_first_time_creation) && $eid_second_time_creation === false);
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Model::create(); do not allow multiple users with the same username',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info1 = array(
            'user_name' => $handle1 . 'a',
            'email' => $handle2
        );
        $info2 = array(
            'user_name' => $handle1 . 'b',
            'email' => $handle2
        );
        $eid_first_time_creation = $model->create(\Model::TYPE_USER, $info1);
        $eid_second_time_creation = $model->create(\Model::TYPE_USER, $info2);
        $actual = (\Flexio\Base\Eid::isValid($eid_first_time_creation) && $eid_second_time_creation === false);
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Model::create(); do not allow multiple users with the same email',  $actual, $expected, $results);



        // TEST: \Model::getUsernameFromEid(); test ability to get the username from the eid

        // BEGIN TEST
        $model->clearErrors();
        $username = $model->user->getUsernameFromEid(null);
        $actual = $username === false && $model->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('C.1', 'User\Model::getUsernameFromEid(); check null input; return false with invalid eid input, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $username = $model->user->getUsernameFromEid('xxxxxxxxxxxx');
        $actual = $username === false && $model->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('C.2', 'User\Model::getUsernameFromEid(); return false when username can\'t be found, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->user->getUsernameFromEid($eid);
        $expected = $handle1;
        TestCheck::assertString('C.3', '\Model::getUsernameFromEid(); use the eid to get the username',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = strtoupper(\Flexio\Base\Util::generateHandle());
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->user->getUsernameFromEid($eid);
        $expected = strtolower($handle1);
        TestCheck::assertString('C.4', '\Model::getUsernameFromEid(); make sure username is case insensitive',  $actual, $expected, $results);



        // TEST: \Model::getEmailFromEid(); test ability to get the user email from the eid

        // BEGIN TEST
        $model->clearErrors();
        $email = $model->user->getEmailFromEid(null);
        $actual = $email === false && $model->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('D.1', 'User\Model::getEmailFromEid(); check null input; return false with invalid eid input, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $email = $model->user->getEmailFromEid('xxxxxxxxxxxx');
        $actual = $email === false && $model->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('D.2', 'User\Model::getEmailFromEid(); return false when a user\'s email can\'t be found, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->user->getEmailFromEid($eid);
        $expected = $handle2;
        TestCheck::assertString('D.3', '\Model::getEmailFromEid(); use the eid to get the user\'s email',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = strtoupper(TestUtil::generateEmail());
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->user->getEmailFromEid($eid);
        $expected = strtolower($handle2);
        TestCheck::assertString('D.4', '\Model::getEmailFromEid(); make sure the returned email is case insensitive',  $actual, $expected, $results);



        // TEST: \Model::getEidFromIdentifier(); test ability to get the eid from the user_name or email

        // BEGIN TEST
        $model->clearErrors();
        $eid = $model->user->getEidFromIdentifier(null);
        $actual = $eid === false && $model->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('E.1', 'User\Model::getEidFromIdentifier(); check null input; return false when eid can\'t be found, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $eid = $model->user->getEidFromIdentifier($handle);
        $actual = $eid === false && $model->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('E.2', 'User\Model::getEidFromIdentifier(); return false when eid can\'t be found, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = \Flexio\Base\Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = $model->user->getEidFromIdentifier($handle1);
        TestCheck::assertString('E.3', '\Model::getEidFromIdentifier(); use the username to get the eid',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = strtoupper(\Flexio\Base\Util::generateHandle());
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = $model->user->getEidFromIdentifier(strtolower($handle1));
        TestCheck::assertString('E.4', '\Model::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle1 = strtolower(\Flexio\Base\Util::generateHandle());
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = $model->user->getEidFromIdentifier(strtoupper($handle1));
        TestCheck::assertString('E.5', '\Model::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);



        // TEST: \Model::checkUserPassword; tests to check password validation

        // BEGIN TEST
        $model->clearErrors();
        $actual = $model->user->checkUserPassword(null, null);
        $expected = false;
        TestCheck::assertBoolean('F.1', 'User\Model::checkUserPassword(); checks for null input; should return false',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $handle = \Flexio\Base\Util::generateHandle();
        $actual = $model->user->checkUserPassword($handle,'');
        $expected = false;
        TestCheck::assertBoolean('F.2', 'User\Model::checkUserPassword(); return false if user cannot be found',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $username = \Flexio\Base\Util::generateHandle();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'password' => $password
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->user->checkUserPassword($username,$password.'x');
        $expected = false;
        TestCheck::assertBoolean('F.3', 'User\Model::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $username = \Flexio\Base\Util::generateHandle();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'password' => $password
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->user->checkUserPassword($username.'x',$password);
        $expected = false;
        TestCheck::assertBoolean('F.4', 'User\Model::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $username = \Flexio\Base\Util::generateHandle();
        $email = TestUtil::generateEmail();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->user->checkUserPassword($username,$password);
        $expected = true;
        TestCheck::assertBoolean('F.5', 'User\Model::checkUserPassword(); return true if password matches',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $username = strtoupper(\Flexio\Base\Util::generateHandle());
        $email = TestUtil::generateEmail();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->user->checkUserPassword(strtolower($username),$password);
        $expected = true;
        TestCheck::assertBoolean('F.6', 'User\Model::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);

        // BEGIN TEST
        $model->clearErrors();
        $username = strtolower(\Flexio\Base\Util::generateHandle());
        $email = TestUtil::generateEmail();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = $model->create(\Model::TYPE_USER, $info);
        $actual = $model->user->checkUserPassword(strtoupper($username),$password);
        $expected = true;
        TestCheck::assertBoolean('F.7', 'User\Model::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);
    }
}
