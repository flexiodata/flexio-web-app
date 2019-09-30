<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
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
        // TEST: Model type constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::TYPE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_USER;
        $expected = 'USR';
        \Flexio\Tests\Check::assertString('A.2', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_PIPE;
        $expected = 'PIP';
        \Flexio\Tests\Check::assertString('A.3', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_STREAM;
        $expected = 'STR';
        \Flexio\Tests\Check::assertString('A.4', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_CONNECTION;
        $expected = 'CTN';
        \Flexio\Tests\Check::assertString('A.5', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_COMMENT;
        $expected = 'CMT';
        \Flexio\Tests\Check::assertString('A.6', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_PROCESS;
        $expected = 'PRC';
        \Flexio\Tests\Check::assertString('A.7', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_TOKEN;
        $expected = 'TKN';
        \Flexio\Tests\Check::assertString('A.8', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_RIGHT;
        $expected = 'RGH';
        \Flexio\Tests\Check::assertString('A.9', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_ACTION;
        $expected = 'ACT';
        \Flexio\Tests\Check::assertString('A.10', 'Model type constant',  $actual, $expected, $results);


        // TEST: Model edge constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::EDGE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_INVITED;
        $expected = 'INV';
        \Flexio\Tests\Check::assertString('B.6', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_INVITED_BY;
        $expected = 'INB';
        \Flexio\Tests\Check::assertString('B.7', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_SHARED_WITH;
        $expected = 'SHW';
        \Flexio\Tests\Check::assertString('B.8', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_SHARED_FROM;
        $expected = 'SHF';
        \Flexio\Tests\Check::assertString('B.9', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_FOLLOWING;
        $expected = 'FLW';
        \Flexio\Tests\Check::assertString('B.10', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_FOLLOWED_BY;
        $expected = 'FLB';
        \Flexio\Tests\Check::assertString('B.11', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_LINKED_TO;
        $expected = 'LKT';
        \Flexio\Tests\Check::assertString('B.14', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_LINKED_FROM;
        $expected = 'LKF';
        \Flexio\Tests\Check::assertString('B.15', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_COPIED_TO;
        $expected = 'CPT';
        \Flexio\Tests\Check::assertString('B.16', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_COPIED_FROM;
        $expected = 'CPF';
        \Flexio\Tests\Check::assertString('B.17', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_COMMENT_ON;
        $expected = 'CMO';
        \Flexio\Tests\Check::assertString('B.18', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_HAS_COMMENT;
        $expected = 'HCM';
        \Flexio\Tests\Check::assertString('B.19', 'Model edge constant',  $actual, $expected, $results);



        // TEST: Model status constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::STATUS_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('C.1', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_PENDING;
        $expected = 'P';
        \Flexio\Tests\Check::assertString('C.2', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_AVAILABLE;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('C.3', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_DELETED;
        $expected = 'D';
        \Flexio\Tests\Check::assertString('C.5', 'Model status constant',  $actual, $expected, $results);



        // TEST: Model registry constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('D.1', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_STRING;
        $expected = 'S';
        \Flexio\Tests\Check::assertString('D.2', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_NUMBER;
        $expected = 'N';
        \Flexio\Tests\Check::assertString('D.3', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_BOOLEAN;
        $expected = 'B';
        \Flexio\Tests\Check::assertString('D.4', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_DATE;
        $expected = 'D';
        \Flexio\Tests\Check::assertString('D.5', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_DATETIME;
        $expected = 'T';
        \Flexio\Tests\Check::assertString('D.6', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_BINARY;
        $expected = 'X';
        \Flexio\Tests\Check::assertString('D.7', 'Pipe status constant',  $actual, $expected, $results);



        // TEST: Model pipe constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_STATUS_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('E.1', 'Pipe deploy status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_STATUS_ACTIVE;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('E.2', 'Pipe deploy status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
        $expected = 'I';
        \Flexio\Tests\Check::assertString('E.3', 'Pipe deploy status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_MODE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('E.4', 'Pipe deply mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_MODE_BUILD;
        $expected = 'B';
        \Flexio\Tests\Check::assertString('E.5', 'Pipe deploy mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_MODE_RUN;
        $expected = 'R';
        \Flexio\Tests\Check::assertString('E.6', 'Pipe deploy mode constant',  $actual, $expected, $results);



        // TEST: Model pipe trigger constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        $actual = \Model::PROCESS_TRIGGERED_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('F.1', 'Pipe deploy mode constant',  $actual, $expected, $results);

        $actual = \Model::PROCESS_TRIGGERED_API;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('F.2', 'Pipe deploy mode constant',  $actual, $expected, $results);

        $actual = \Model::PROCESS_TRIGGERED_EMAIL;
        $expected = 'E';
        \Flexio\Tests\Check::assertString('F.3', 'Pipe deploy mode constant',  $actual, $expected, $results);

        $actual = \Model::PROCESS_TRIGGERED_SCHEDULER;
        $expected = 'S';
        \Flexio\Tests\Check::assertString('F.4', 'Pipe deploy mode constant',  $actual, $expected, $results);

        $actual = \Model::PROCESS_TRIGGERED_INTERFACE;
        $expected = 'I';
        \Flexio\Tests\Check::assertString('F.5', 'Pipe deploy mode constant',  $actual, $expected, $results);


        // TEST: Model team member status constants; the database stores raw values
        // for some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the databases

        $actual = \Model::TEAM_MEMBER_STATUS_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('G.1', 'Team member status constant',  $actual, $expected, $results);

        $actual = \Model::TEAM_MEMBER_STATUS_PENDING;
        $expected = 'P';
        \Flexio\Tests\Check::assertString('G.2', 'Team member status constant',  $actual, $expected, $results);

        $actual = \Model::TEAM_MEMBER_STATUS_INACTIVE;
        $expected = 'I';
        \Flexio\Tests\Check::assertString('G.3', 'Team member status constant',  $actual, $expected, $results);

        $actual = \Model::TEAM_MEMBER_STATUS_ACTIVE;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('G.4', 'Team member status constant',  $actual, $expected, $results);



        // TEST: Model system role status constants; the database stores raw values
        // for some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the databases

        $actual = \Model::SYSTEM_ROLE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('H.1', 'System role status constant',  $actual, $expected, $results);

        $actual = \Model::SYSTEM_ROLE_ADMINISTRATOR;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('H.2', 'System role status constant',  $actual, $expected, $results);



        // TEST: Model system role status constants; the database stores raw values
        // for some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the databases

        $actual = \Model::TEAM_ROLE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('I.1', 'Team member role status constant',  $actual, $expected, $results);

        $actual = \Model::TEAM_ROLE_USER;
        $expected = 'U';
        \Flexio\Tests\Check::assertString('I.2', 'Team member role status constant',  $actual, $expected, $results);

        $actual = \Model::TEAM_ROLE_CONTRIBUTOR;
        $expected = 'C';
        \Flexio\Tests\Check::assertString('I.3', 'Team member role status constant',  $actual, $expected, $results);

        $actual = \Model::TEAM_ROLE_ADMINISTRATOR;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('I.4', 'Team member role status constant',  $actual, $expected, $results);



        // TEST: Model connection constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('J.1', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_AVAILABLE;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('J.2', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_UNAVAILABLE;
        $expected = 'U';
        \Flexio\Tests\Check::assertString('J.3', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_ERROR;
        $expected = 'E';
        \Flexio\Tests\Check::assertString('J.4', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_MODE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('J.5', 'Connection mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_MODE_FUNCTION;
        $expected = 'F';
        \Flexio\Tests\Check::assertString('J.6', 'Connection mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_MODE_RESOURCE;
        $expected = 'R';
        \Flexio\Tests\Check::assertString('J.7', 'Connection mode constant',  $actual, $expected, $results);



        // TEST: Model connection constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('K.1', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_FTP;
        $expected = 'ftp';
        \Flexio\Tests\Check::assertString('K.2', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_SFTP;
        $expected = 'sftp';
        \Flexio\Tests\Check::assertString('K.3', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_MYSQL;
        $expected = 'mysql';
        \Flexio\Tests\Check::assertString('K.4', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_POSTGRES;
        $expected = 'postgres';
        \Flexio\Tests\Check::assertString('K.5', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_ELASTICSEARCH;
        $expected = 'elasticsearch';
        \Flexio\Tests\Check::assertString('K.6', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_DROPBOX;
        $expected = 'dropbox';
        \Flexio\Tests\Check::assertString('K.7', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_BOX;
        $expected = 'box';
        \Flexio\Tests\Check::assertString('K.8', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_GMAIL;
        $expected = 'gmail';
        \Flexio\Tests\Check::assertString('K.9', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_GOOGLEDRIVE;
        $expected = 'googledrive';
        \Flexio\Tests\Check::assertString('K.10', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_GOOGLESHEETS;
        $expected = 'googlesheets';
        \Flexio\Tests\Check::assertString('K.11', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_GOOGLECLOUDSTORAGE;
        $expected = 'googlecloudstorage';
        \Flexio\Tests\Check::assertString('K.12', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_GITHUB;
        $expected = 'github';
        \Flexio\Tests\Check::assertString('K.13', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_AMAZONS3;
        $expected = 'amazons3';
        \Flexio\Tests\Check::assertString('K.14', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_EMAIL;
        $expected = 'email';
        \Flexio\Tests\Check::assertString('K.15', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_SMTP;
        $expected = 'smtp';
        \Flexio\Tests\Check::assertString('K.16', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_HTTP;
        $expected = 'http';
        \Flexio\Tests\Check::assertString('K.17', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_SOCRATA;
        $expected = 'socrata';
        \Flexio\Tests\Check::assertString('K.18', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_LINKEDIN;
        $expected = 'linkedin';
        \Flexio\Tests\Check::assertString('K.19', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_TWITTER;
        $expected = 'twitter';
        \Flexio\Tests\Check::assertString('K.20', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_CRUNCHBASE;
        $expected = 'crunchbase';
        \Flexio\Tests\Check::assertString('K.21', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_KEYRING;
        $expected = 'keyring';
        \Flexio\Tests\Check::assertString('K.22', 'Type constant',  $actual, $expected, $results);
    }
}
