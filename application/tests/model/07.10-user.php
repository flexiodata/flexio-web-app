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
        // FUNCTION: \Flexio\Model\User::getEmailFromEid()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: test ability to get the user email from the eid

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
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\User::getEmailFromEid(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $email = $model->getEmailFromEid('xxxxxxxxxxxx');
        $actual = $email === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\User::getEmailFromEid(); return false when a user\'s email can\'t be found',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Model\User::getEmailFromEid(); use the eid to get the user\'s email',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Model\User::getEmailFromEid(); make sure the returned email is case insensitive',  $actual, $expected, $results);
    }
}
