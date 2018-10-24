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
        // TEST: Model type constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::TYPE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_USER;
        $expected = 'USR';
        \Flexio\Tests\Check::assertString('B.2', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_PIPE;
        $expected = 'PIP';
        \Flexio\Tests\Check::assertString('B.3', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_STREAM;
        $expected = 'STR';
        \Flexio\Tests\Check::assertString('B.4', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_CONNECTION;
        $expected = 'CTN';
        \Flexio\Tests\Check::assertString('B.5', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_COMMENT;
        $expected = 'CMT';
        \Flexio\Tests\Check::assertString('B.6', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_PROCESS;
        $expected = 'PRC';
        \Flexio\Tests\Check::assertString('B.7', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_TOKEN;
        $expected = 'TKN';
        \Flexio\Tests\Check::assertString('B.8', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_RIGHT;
        $expected = 'RGH';
        \Flexio\Tests\Check::assertString('B.9', 'Model type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::TYPE_ACTION;
        $expected = 'ACT';
        \Flexio\Tests\Check::assertString('B.10', 'Model type constant',  $actual, $expected, $results);


        // TEST: Model edge constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::EDGE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('C.1', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_INVITED;
        $expected = 'INV';
        \Flexio\Tests\Check::assertString('C.6', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_INVITED_BY;
        $expected = 'INB';
        \Flexio\Tests\Check::assertString('C.7', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_SHARED_WITH;
        $expected = 'SHW';
        \Flexio\Tests\Check::assertString('C.8', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_SHARED_FROM;
        $expected = 'SHF';
        \Flexio\Tests\Check::assertString('C.9', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_FOLLOWING;
        $expected = 'FLW';
        \Flexio\Tests\Check::assertString('C.10', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_FOLLOWED_BY;
        $expected = 'FLB';
        \Flexio\Tests\Check::assertString('C.11', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_LINKED_TO;
        $expected = 'LKT';
        \Flexio\Tests\Check::assertString('C.14', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_LINKED_FROM;
        $expected = 'LKF';
        \Flexio\Tests\Check::assertString('C.15', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_COPIED_TO;
        $expected = 'CPT';
        \Flexio\Tests\Check::assertString('C.16', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_COPIED_FROM;
        $expected = 'CPF';
        \Flexio\Tests\Check::assertString('C.17', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_COMMENT_ON;
        $expected = 'CMO';
        \Flexio\Tests\Check::assertString('C.18', 'Model edge constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::EDGE_HAS_COMMENT;
        $expected = 'HCM';
        \Flexio\Tests\Check::assertString('C.19', 'Model edge constant',  $actual, $expected, $results);



        // TEST: Model status constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::STATUS_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('D.1', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_PENDING;
        $expected = 'P';
        \Flexio\Tests\Check::assertString('D.2', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_AVAILABLE;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('D.3', 'Model status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::STATUS_DELETED;
        $expected = 'D';
        \Flexio\Tests\Check::assertString('D.5', 'Model status constant',  $actual, $expected, $results);



        // TEST: Model registry constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('E.1', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_STRING;
        $expected = 'S';
        \Flexio\Tests\Check::assertString('E.2', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_NUMBER;
        $expected = 'N';
        \Flexio\Tests\Check::assertString('E.3', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_BOOLEAN;
        $expected = 'B';
        \Flexio\Tests\Check::assertString('E.4', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_DATE;
        $expected = 'D';
        \Flexio\Tests\Check::assertString('E.5', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_DATETIME;
        $expected = 'T';
        \Flexio\Tests\Check::assertString('E.6', 'Pipe status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::REGISTRY_VALUE_BINARY;
        $expected = 'X';
        \Flexio\Tests\Check::assertString('E.7', 'Pipe status constant',  $actual, $expected, $results);



        // TEST: Model connection constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('F.1', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_AVAILABLE;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('F.2', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_UNAVAILABLE;
        $expected = 'U';
        \Flexio\Tests\Check::assertString('F.3', 'Connection status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::CONNECTION_STATUS_ERROR;
        $expected = 'E';
        \Flexio\Tests\Check::assertString('F.4', 'Connection status constant',  $actual, $expected, $results);



        // TEST: Model pipe constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_STATUS_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('I.1', 'Pipe deploy status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_STATUS_ACTIVE;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('I.2', 'Pipe deploy status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
        $expected = 'I';
        \Flexio\Tests\Check::assertString('I.3', 'Pipe deploy status constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_MODE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('I.4', 'Pipe deply mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_MODE_BUILD;
        $expected = 'B';
        \Flexio\Tests\Check::assertString('I.5', 'Pipe deploy mode constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::PIPE_DEPLOY_MODE_RUN;
        $expected = 'R';
        \Flexio\Tests\Check::assertString('I.6', 'Pipe deploy mode constant',  $actual, $expected, $results);



        // TEST: Model pipe trigger constants; the database stores raw values for
        // some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        $actual = \Model::PROCESS_TRIGGERED_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('J.1', 'Pipe deploy mode constant',  $actual, $expected, $results);

        $actual = \Model::PROCESS_TRIGGERED_API;
        $expected = 'A';
        \Flexio\Tests\Check::assertString('J.2', 'Pipe deploy mode constant',  $actual, $expected, $results);

        $actual = \Model::PROCESS_TRIGGERED_EMAIL;
        $expected = 'E';
        \Flexio\Tests\Check::assertString('J.3', 'Pipe deploy mode constant',  $actual, $expected, $results);

        $actual = \Model::PROCESS_TRIGGERED_SCHEDULER;
        $expected = 'S';
        \Flexio\Tests\Check::assertString('J.4', 'Pipe deploy mode constant',  $actual, $expected, $results);

        $actual = \Model::PROCESS_TRIGGERED_INTERFACE;
        $expected = 'I';
        \Flexio\Tests\Check::assertString('J.5', 'Pipe deploy mode constant',  $actual, $expected, $results);



        // TEST: Model access code type constants; the database stores raw values
        // for some of these constants, so these tests ensure that the constants
        // are consistent between various models and the values in the
        // databases

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('L.1', 'Access code type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_EID;
        $expected = 'EID';
        \Flexio\Tests\Check::assertString('L.2', 'Access code type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_TOKEN;
        $expected = 'TKN';
        \Flexio\Tests\Check::assertString('L.3', 'Access code type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_EMAIL;
        $expected = 'EML';
        \Flexio\Tests\Check::assertString('L.4', 'Access code type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Model::ACCESS_CODE_TYPE_CATEGORY;
        $expected = 'CAT';
        \Flexio\Tests\Check::assertString('L.5', 'Access code type constant',  $actual, $expected, $results);
    }
}
