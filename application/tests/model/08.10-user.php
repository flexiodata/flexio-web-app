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
        // FUNCTION: \Flexio\Model\User::getEidFromIdentifier()


        // SETUP
        $model = \Flexio\Tests\Util::getModel()->user;


        // TEST: test ability to get the eid from the username or email

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
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Model\User::getEidFromIdentifier(); throw an error with null input',  $actual, $expected, $results);

        // BEGIN TEST
        $handle = \Flexio\Base\Util::generateHandle();
        $eid = $model->getEidFromIdentifier($handle);
        $actual = $eid === false;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Model\User::getEidFromIdentifier(); return false when eid can\'t be found',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Model\User::getEidFromIdentifier(); use the username to get the eid',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Model\User::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Model\User::getEidFromIdentifier(); make sure username is case insensitive',  $actual, $expected, $results);
    }
}
