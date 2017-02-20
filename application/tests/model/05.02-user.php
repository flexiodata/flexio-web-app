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


class Test
{
    public function run(&$results)
    {
        // TEST: Model::create(); user creation with invalid input

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = false;
        TestCheck::assertBoolean('A.1', 'Model::create(); invalid input should return false',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $info = array(
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->getErrors();
        $expected = array(array(
            'code' => Model::ERROR_MISSING_PARAMETER,
            'message' => 'Missing user_name parameter'
        ));
        TestCheck::assertInArray('A.2', 'Model::create(); invalid input should set an error',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = TestUtil::generateEmail();
        $info = array(
            'email' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = false;
        TestCheck::assertBoolean('A.3', 'Model::create(); when creating a user, the user_name is required; return false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $info = array(
            'user_name' => $handle
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = false;
        TestCheck::assertBoolean('A.4', 'Model::create(); when creating a user, an email address is required; return false otherwise',  $actual, $expected, $results);


        // TEST: Model::create(); user creation with basic user_name input
        System::getModel()->clearErrors();
        $handle1 = Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = Eid::isValid($eid);
        $expected = true;
        TestCheck::assertBoolean('B.1', 'Model::create(); make sure valid eid is returned when user is created',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle1 = Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info1 = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $info2 = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid_first_time_creation = System::getModel()->create(Model::TYPE_USER, $info1);
        $eid_second_time_creation = System::getModel()->create(Model::TYPE_USER, $info2);
        $actual = (Eid::isValid($eid_first_time_creation) && $eid_second_time_creation === false);
        $expected = true;
        TestCheck::assertBoolean('B.2', 'Model::create(); do not allow multiple users with the same username',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle1 = Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info1 = array(
            'user_name' => $handle1 . 'a',
            'email' => $handle2
        );
        $info2 = array(
            'user_name' => $handle1 . 'b',
            'email' => $handle2
        );
        $eid_first_time_creation = System::getModel()->create(Model::TYPE_USER, $info1);
        $eid_second_time_creation = System::getModel()->create(Model::TYPE_USER, $info2);
        $actual = (Eid::isValid($eid_first_time_creation) && $eid_second_time_creation === false);
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Model::create(); do not allow multiple users with the same email',  $actual, $expected, $results);



        // TEST: Model::getUsernameFromEid(); test ability to get the username from the eid

        // BEGIN TEST
        System::getModel()->clearErrors();
        $username = System::getModel()->user->getUsernameFromEid(null);
        $actual = $username === false && System::getModel()->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('C.1', 'UserModel::getUsernameFromEid(); check null input; return false with invalid eid input, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $username = System::getModel()->user->getUsernameFromEid('xxxxxxxxxxxx');
        $actual = $username === false && System::getModel()->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('C.2', 'UserModel::getUsernameFromEid(); return false when username can\'t be found, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle1 = Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->user->getUsernameFromEid($eid);
        $expected = $handle1;
        TestCheck::assertString('C.3', 'Model::getUsernameFromEid(); use the eid to get the username',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle1 = strtoupper(Util::generateHandle());
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->user->getUsernameFromEid($eid);
        $expected = strtolower($handle1);
        TestCheck::assertString('C.4', 'Model::getUsernameFromEid(); make sure username is case insensitive',  $actual, $expected, $results);



        // TEST: Model::getEmailFromEid(); test ability to get the user email from the eid

        // BEGIN TEST
        System::getModel()->clearErrors();
        $email = System::getModel()->user->getEmailFromEid(null);
        $actual = $email === false && System::getModel()->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('D.1', 'UserModel::getEmailFromEid(); check null input; return false with invalid eid input, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $email = System::getModel()->user->getEmailFromEid('xxxxxxxxxxxx');
        $actual = $email === false && System::getModel()->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('D.2', 'UserModel::getEmailFromEid(); return false when a user\'s email can\'t be found, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle1 = Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->user->getEmailFromEid($eid);
        $expected = $handle2;
        TestCheck::assertString('D.3', 'Model::getEmailFromEid(); use the eid to get the user\'s email',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle1 = Util::generateHandle();
        $handle2 = strtoupper(TestUtil::generateEmail());
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->user->getEmailFromEid($eid);
        $expected = strtolower($handle2);
        TestCheck::assertString('D.4', 'Model::getEmailFromEid(); make sure the returned email is case insensitive',  $actual, $expected, $results);



        // TEST: Model::getEidFromIdentifier(); test ability to get the eid from the user_name or email

        // BEGIN TEST
        System::getModel()->clearErrors();
        $eid = System::getModel()->user->getEidFromIdentifier(null);
        $actual = $eid === false && System::getModel()->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('E.1', 'UserModel::getEidFromIdentifier(); check null input; return false when eid can\'t be found, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $eid = System::getModel()->user->getEidFromIdentifier($handle);
        $actual = $eid === false && System::getModel()->hasErrors() === false; // shouldn't set an error
        $expected = true;
        TestCheck::assertBoolean('E.2', 'UserModel::getEidFromIdentifier(); return false when eid can\'t be found, but don\'t flag an error',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle1 = Util::generateHandle();
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = System::getModel()->user->getEidFromIdentifier($handle1);
        TestCheck::assertString('E.3', 'Model::getEidFromIdentifier(); use the username to get the eid',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle1 = strtoupper(Util::generateHandle());
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = System::getModel()->user->getEidFromIdentifier(strtolower($handle1));
        TestCheck::assertString('E.4', 'Model::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle1 = strtolower(Util::generateHandle());
        $handle2 = TestUtil::generateEmail();
        $info = array(
            'user_name' => $handle1,
            'email' => $handle2
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = $eid;
        $expected = System::getModel()->user->getEidFromIdentifier(strtoupper($handle1));
        TestCheck::assertString('E.5', 'Model::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);



        // TEST: Model::checkUserPassword; tests to check password validation

        // BEGIN TEST
        System::getModel()->clearErrors();
        $actual = System::getModel()->user->checkUserPassword(null, null);
        $expected = false;
        TestCheck::assertBoolean('F.1', 'UserModel::checkUserPassword(); checks for null input; should return false',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $handle = Util::generateHandle();
        $actual = System::getModel()->user->checkUserPassword($handle,'');
        $expected = false;
        TestCheck::assertBoolean('F.2', 'UserModel::checkUserPassword(); return false if user cannot be found',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $username = Util::generateHandle();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'password' => $password
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->user->checkUserPassword($username,$password.'x');
        $expected = false;
        TestCheck::assertBoolean('F.3', 'UserModel::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $username = Util::generateHandle();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'password' => $password
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->user->checkUserPassword($username.'x',$password);
        $expected = false;
        TestCheck::assertBoolean('F.4', 'UserModel::checkUserPassword(); return false if password does not match',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $username = Util::generateHandle();
        $email = TestUtil::generateEmail();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->user->checkUserPassword($username,$password);
        $expected = true;
        TestCheck::assertBoolean('F.5', 'UserModel::checkUserPassword(); return true if password matches',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $username = strtoupper(Util::generateHandle());
        $email = TestUtil::generateEmail();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->user->checkUserPassword(strtolower($username),$password);
        $expected = true;
        TestCheck::assertBoolean('F.6', 'UserModel::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);

        // BEGIN TEST
        System::getModel()->clearErrors();
        $username = strtolower(Util::generateHandle());
        $email = TestUtil::generateEmail();
        $password = 'xxxxxxxx';
        $info = array(
            'user_name' => $username,
            'email' => $email,
            'password' => $password
        );
        $eid = System::getModel()->create(Model::TYPE_USER, $info);
        $actual = System::getModel()->user->checkUserPassword(strtoupper($username),$password);
        $expected = true;
        TestCheck::assertBoolean('F.7', 'UserModel::checkUserPassword(); make sure username is case insensitive',  $actual, $expected, $results);
    }
}
