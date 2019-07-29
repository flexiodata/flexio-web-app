<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-03
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
        // TEST: class syntax check

        // BEGIN TEST
        $class = new \Flexio\Api\Action;
        $actual = get_class($class);
        $expected = 'Flexio\Api\Action';
        \Flexio\Tests\Check::assertString('A.1', 'new \Flexio\Api\Action; basic class syntax check',  $actual, $expected, $results);


        // TEST: class constants

        // BEGIN TEST
        $actual = \Flexio\Api\Action::TYPE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEST;
        $expected = 'action.test';
        \Flexio\Tests\Check::assertString('A.2', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_LOGIN;
        $expected = 'action.user.login';
        \Flexio\Tests\Check::assertString('A.3', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_LOGOUT;
        $expected = 'action.user.logout';
        \Flexio\Tests\Check::assertString('A.4', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_CREATE;
        $expected = 'action.user.create';
        \Flexio\Tests\Check::assertString('A.5', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_UPDATE;
        $expected = 'action.user.update';
        \Flexio\Tests\Check::assertString('A.6', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_DELETE;
        $expected = 'action.user.delete';
        \Flexio\Tests\Check::assertString('A.7', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_READ;
        $expected = 'action.user.read';
        \Flexio\Tests\Check::assertString('A.8', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_CREDENTIAL_UPDATE;
        $expected = 'action.user.credential.update';
        \Flexio\Tests\Check::assertString('A.9', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_CREDENTIAL_RESET;
        $expected = 'action.user.credential.reset';
        \Flexio\Tests\Check::assertString('A.10', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAM_CREATE;
        $expected = 'action.team.create';
        \Flexio\Tests\Check::assertString('A.11', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAM_UPDATE;
        $expected = 'action.team.update';
        \Flexio\Tests\Check::assertString('A.12', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAM_DELETE;
        $expected = 'action.team.delete';
        \Flexio\Tests\Check::assertString('A.13', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAM_READ;
        $expected = 'action.team.read';
        \Flexio\Tests\Check::assertString('A.14', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAMMEMBER_CREATE;
        $expected = 'action.teammember.create';
        \Flexio\Tests\Check::assertString('A.15', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAMMEMBER_UPDATE;
        $expected = 'action.teammember.update';
        \Flexio\Tests\Check::assertString('A.16', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAMMEMBER_DELETE;
        $expected = 'action.teammember.delete';
        \Flexio\Tests\Check::assertString('A.17', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAMMEMBER_READ;
        $expected = 'action.teammember.read';
        \Flexio\Tests\Check::assertString('A.18', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAMMEMBER_SENDINVITATION;
        $expected = 'action.teammember.sendinvitation';
        \Flexio\Tests\Check::assertString('A.19', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_TEAMMEMBER_JOINTEAM;
        $expected = 'action.teammember.jointeam';
        \Flexio\Tests\Check::assertString('A.20', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_AUTHKEY_CREATE;
        $expected = 'action.user.authkey.create';
        \Flexio\Tests\Check::assertString('A.21', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_AUTHKEY_DELETE;
        $expected = 'action.user.authkey.delete';
        \Flexio\Tests\Check::assertString('A.22', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_USER_AUTHKEY_READ;
        $expected = 'action.user.authkey.read';
        \Flexio\Tests\Check::assertString('A.23', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_PIPE_CREATE;
        $expected = 'action.pipe.create';
        \Flexio\Tests\Check::assertString('A.24', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_PIPE_UPDATE;
        $expected = 'action.pipe.update';
        \Flexio\Tests\Check::assertString('A.25', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_PIPE_DELETE;
        $expected = 'action.pipe.delete';
        \Flexio\Tests\Check::assertString('A.26', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_PIPE_READ;
        $expected = 'action.pipe.read';
        \Flexio\Tests\Check::assertString('A.27', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_CONNECTION_CREATE;
        $expected = 'action.connection.create';
        \Flexio\Tests\Check::assertString('A.28', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_CONNECTION_UPDATE;
        $expected = 'action.connection.update';
        \Flexio\Tests\Check::assertString('A.29', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_CONNECTION_DELETE;
        $expected = 'action.connection.delete';
        \Flexio\Tests\Check::assertString('A.30', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_CONNECTION_READ;
        $expected = 'action.connection.read';
        \Flexio\Tests\Check::assertString('A.31', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_CONNECTION_CONNECT;
        $expected = 'action.connection.connect';
        \Flexio\Tests\Check::assertString('A.32', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_CONNECTION_DISCONNECT;
        $expected = 'action.connection.disconnect';
        \Flexio\Tests\Check::assertString('A.33', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_PROCESS_CREATE;
        $expected = 'action.process.create';
        \Flexio\Tests\Check::assertString('A.34', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_PROCESS_UPDATE;
        $expected = 'action.process.update';
        \Flexio\Tests\Check::assertString('A.35', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_PROCESS_DELETE;
        $expected = 'action.process.delete';
        \Flexio\Tests\Check::assertString('A.36', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_PROCESS_READ;
        $expected = 'action.process.read';
        \Flexio\Tests\Check::assertString('A.37', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_STREAM_CREATE;
        $expected = 'action.stream.create';
        \Flexio\Tests\Check::assertString('A.38', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_STREAM_UPDATE;
        $expected = 'action.stream.update';
        \Flexio\Tests\Check::assertString('A.39', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_STREAM_DELETE;
        $expected = 'action.stream.delete';
        \Flexio\Tests\Check::assertString('A.40', 'Action type',  $actual, $expected, $results);

        $actual = \Flexio\Api\Action::TYPE_STREAM_READ;
        $expected = 'action.stream.read';
        \Flexio\Tests\Check::assertString('A.41', 'Action type',  $actual, $expected, $results);
    }
}
