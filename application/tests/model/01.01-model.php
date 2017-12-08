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

        // BEGIN TEST
        $actual = \Model::EDGE_STORE_FOR;
        $expected = 'STF';
        TestCheck::assertString('C.22', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_HAS_STORE;
        $expected = 'HST';
        TestCheck::assertString('C.23', 'Model edge constant',  $actual, $expected, $results);



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
        $actual = \Model::CONNECTION_STATUS_UNDEFINED;
        $expected = '';
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




        // TEST: Model pipe constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::PIPE_STATUS_UNDEFINED;
        $expected = '';
        TestCheck::assertString('I.1', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_STATUS_ACTIVE;
        $expected = 'A';
        TestCheck::assertString('I.2', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_STATUS_INACTIVE;
        $expected = 'I';
        TestCheck::assertString('I.3', 'Pipe status constant',  $actual, $expected, $results);



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
