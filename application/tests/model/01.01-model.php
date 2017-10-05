<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
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
        // SETUP
        $model = TestUtil::getModel();



        // TEST: Model type constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::TYPE_UNDEFINED;
        $expected = '';
        TestCheck::assertString('B.1', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_OBJECT;
        $expected = 'OBJ';
        TestCheck::assertString('B.2', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_USER;
        $expected = 'USR';
        TestCheck::assertString('B.3', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_PROJECT;
        $expected = 'PRJ';
        TestCheck::assertString('B.4', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_PIPE;
        $expected = 'PIP';
        TestCheck::assertString('B.5', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_STREAM;
        $expected = 'STR';
        TestCheck::assertString('B.6', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_CONNECTION;
        $expected = 'CTN';
        TestCheck::assertString('B.7', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_COMMENT;
        $expected = 'CMT';
        TestCheck::assertString('B.8', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_PROCESS;
        $expected = 'PRC';
        TestCheck::assertString('B.9', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_TOKEN;
        $expected = 'ATH';
        TestCheck::assertString('B.10', 'Model type constant',  $actual, $expected, $results);



        // TEST: Model edge constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::EDGE_UNDEFINED;
        $expected = '';
        TestCheck::assertString('C.1', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_CREATED;
        $expected = 'CRT';
        TestCheck::assertString('C.2', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_CREATED_BY;
        $expected = 'CRB';
        TestCheck::assertString('C.3', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_OWNS;
        $expected = 'OWN';
        TestCheck::assertString('C.4', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_OWNED_BY;
        $expected = 'OWB';
        TestCheck::assertString('C.5', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_INVITED;
        $expected = 'INV';
        TestCheck::assertString('C.6', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_INVITED_BY;
        $expected = 'INB';
        TestCheck::assertString('C.7', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_SHARED_WITH;
        $expected = 'SHW';
        TestCheck::assertString('C.8', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_SHARED_FROM;
        $expected = 'SHF';
        TestCheck::assertString('C.9', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_FOLLOWING;
        $expected = 'FLW';
        TestCheck::assertString('C.10', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_FOLLOWED_BY;
        $expected = 'FLB';
        TestCheck::assertString('C.11', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_MEMBER_OF;
        $expected = 'MBO';
        TestCheck::assertString('C.12', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_HAS_MEMBER;
        $expected = 'HMB';
        TestCheck::assertString('C.13', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_LINKED_TO;
        $expected = 'LKT';
        TestCheck::assertString('C.14', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_LINKED_FROM;
        $expected = 'LKF';
        TestCheck::assertString('C.15', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_COPIED_TO;
        $expected = 'CPT';
        TestCheck::assertString('C.16', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_COPIED_FROM;
        $expected = 'CPF';
        TestCheck::assertString('C.17', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_COMMENT_ON;
        $expected = 'CMO';
        TestCheck::assertString('C.18', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_HAS_COMMENT;
        $expected = 'HCM';
        TestCheck::assertString('C.19', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_PROCESS_OF;
        $expected = 'PRO';
        TestCheck::assertString('C.20', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_HAS_PROCESS;
        $expected = 'HPR';
        TestCheck::assertString('C.21', 'Model edge constant',  $actual, $expected, $results);



        // TEST: Model status constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::STATUS_UNDEFINED;
        $expected = '';
        TestCheck::assertString('D.1', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_PENDING;
        $expected = 'P';
        TestCheck::assertString('D.2', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_AVAILABLE;
        $expected = 'A';
        TestCheck::assertString('D.3', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_TRASH;
        $expected = 'T';
        TestCheck::assertString('D.4', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_DELETED;
        $expected = 'D';
        TestCheck::assertString('D.5', 'Model status constant',  $actual, $expected, $results);



        // TEST: Model registry constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_UNDEFINED;
        $expected = '';
        TestCheck::assertString('E.1', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_STRING;
        $expected = 'S';
        TestCheck::assertString('E.2', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_NUMBER;
        $expected = 'N';
        TestCheck::assertString('E.3', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_BOOLEAN;
        $expected = 'B';
        TestCheck::assertString('E.4', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_DATE;
        $expected = 'D';
        TestCheck::assertString('E.5', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_DATETIME;
        $expected = 'T';
        TestCheck::assertString('E.6', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_BINARY;
        $expected = 'X';
        TestCheck::assertString('E.7', 'Pipe status constant',  $actual, $expected, $results);



        // TEST: Model connection constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_INVALID;
        $expected = 'I';
        TestCheck::assertString('F.1', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_AVAILABLE;
        $expected = 'A';
        TestCheck::assertString('F.2', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_UNAVAILABLE;
        $expected = 'U';
        TestCheck::assertString('F.3', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_ERROR;
        $expected = 'E';
        TestCheck::assertString('F.4', 'Connection status constant',  $actual, $expected, $results);



        // TEST: Model connection constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::CONNECTION_CONFIG_TYPE_DATABASE;
        $expected = 'database';
        TestCheck::assertString('G.1', 'Connection configuration constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_CONFIG_TYPE_OAUTH2;
        $expected = 'oauth2';
        TestCheck::assertString('G.2', 'Connection configuration constant',  $actual, $expected, $results);



        // TEST: Model connection constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_FLEXIO;
        $expected = 'flexio';
        TestCheck::assertString('H.1', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_FTP;
        $expected = 'ftp';
        TestCheck::assertString('H.3', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_SFTP;
        $expected = 'sftp';
        TestCheck::assertString('H.4', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_MYSQL;
        $expected = 'mysql';
        TestCheck::assertString('H.5', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_POSTGRES;
        $expected = 'postgres';
        TestCheck::assertString('H.6', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_ELASTICSEARCH;
        $expected = 'elasticsearch';
        TestCheck::assertString('H.7', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_DROPBOX;
        $expected = 'dropbox';
        TestCheck::assertString('H.8', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_GOOGLEDRIVE;
        $expected = 'googledrive';
        TestCheck::assertString('H.9', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_GOOGLESHEETS;
        $expected = 'googlesheets';
        TestCheck::assertString('H.10', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_AMAZONS3;
        $expected = 'amazons3';
        TestCheck::assertString('H.11', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_EMAIL;
        $expected = 'email';
        TestCheck::assertString('H.16', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_HTTP;
        $expected = 'http';
        TestCheck::assertString('H.17', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_RSS;
        $expected = 'rss';
        TestCheck::assertString('H.18', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_SOCRATA;
        $expected = 'socrata';
        TestCheck::assertString('H.19', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_PIPELINEDEALS;
        $expected = 'pipelinedeals';
        TestCheck::assertString('H.20', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_MAILJET;
        $expected = 'mailjet';
        TestCheck::assertString('H.21', 'Connection type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_TYPE_TWILIO;
        $expected = 'twilio';
        TestCheck::assertString('H.22', 'Connection type constant',  $actual, $expected, $results);



        // TEST: Model pipe constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::PIPE_STATUS_ACTIVE;
        $expected = 'A';
        TestCheck::assertString('I.1', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_STATUS_INACTIVE;
        $expected = 'I';
        TestCheck::assertString('I.2', 'Pipe status constant',  $actual, $expected, $results);



        // TEST: Model task constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::PROCESS_MODE_BUILD;
        $expected = 'B';
        TestCheck::assertString('J.1', 'Process mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_MODE_RUN;
        $expected = 'R';
        TestCheck::assertString('J.2', 'Process mode constant',  $actual, $expected, $results);



        // TEST: Model task constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_UNDEFINED;
        $expected = '';
        TestCheck::assertString('K.1', 'Task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_PENDING;
        $expected = 'S';
        TestCheck::assertString('K.2', 'Task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_WAITING;
        $expected = 'W';
        TestCheck::assertString('K.3', 'Task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_RUNNING;
        $expected = 'R';
        TestCheck::assertString('K.4', 'Task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_CANCELLED;
        $expected = 'X';
        TestCheck::assertString('K.5', 'Task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_PAUSED;
        $expected = 'P';
        TestCheck::assertString('K.6', 'Task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_FAILED;
        $expected = 'F';
        TestCheck::assertString('K.7', 'Task status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PROCESS_STATUS_COMPLETED;
        $expected = 'C';
        TestCheck::assertString('K.8', 'Task status constant',  $actual, $expected, $results);



        // TEST: Model access code type constants; the database stores raw values
        // for some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_UNDEFINED;
        $expected = '';
        TestCheck::assertString('L.1', 'Access code type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_EID;
        $expected = 'EID';
        TestCheck::assertString('L.2', 'Access code type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_TOKEN;
        $expected = 'TKN';
        TestCheck::assertString('L.3', 'Access code type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_EMAIL;
        $expected = 'EML';
        TestCheck::assertString('L.4', 'Access code type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_CATEGORY;
        $expected = 'CAT';
        TestCheck::assertString('L.5', 'Access code type constant',  $actual, $expected, $results);
    }
}
