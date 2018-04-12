<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-11
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
        // ENDPOINT: POST /:userid/pipes/:objeid/run


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/api/v2';
        $password1 = \Flexio\Base\Util::generatePassword();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Util::generatePassword();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "ename": "ename1",
                "task": {
                    "op": "echo",
                    "params": {
                        "msg": "${form.msg}"
                    }
                }
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid1 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid2/pipes",
            'token' => $token2,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "ename": "ename2",
                "task": {
                    "op": "echo",
                    "params": {
                        "msg": "${form.msg}"
                    }
                }
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid2 = $response['eid'] ?? '';

        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
                "name": "Test Pipe",
                "ename": "ename3",
                "task": {
                    "op": "echo",
                    "params": {
                        "msg": "Hello, ${form.noun}. Would you like to ${form.verb}?"
                    }
                }
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = json_decode($result['response'],true);
        $objeid3 = $response['eid'] ?? '';


        // TEST: run pipe with rights checks

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            // 'token' => '', // no token included
            'content_type' => 'application/json',
            'params' => '{
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.1', 'POST /:userid/pipes/:objeid/run; fail if requesting user doesn\'t have credentials',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid2/run",
            'token' => $token1,
            'content_type' => 'application/json',
            'params' => '{
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "no-object"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.2', 'POST /:userid/pipes/:objeid/run; fail if object isn\'t owned by specified owner',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token2, // token for another user
            'content_type' => 'application/json',
            'params' => '{
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $actual = $result['response'];
        $expected = '
        {
            "error": {
                "code": "insufficient-rights"
            }
        }';
        \Flexio\Tests\Check::assertInArray('A.3', 'POST /:userid/pipes/:objeid/run; fail if requesting user doesn\'t have rights',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.4', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe without posted variables',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg"=> "Hello, World!"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = 'Hello, World!';
        \Flexio\Tests\Check::assertString('A.5', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'content-type' => 'application/json',
            'params' => '{
                "msg": "Hello, World!"
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = 'Hello, World!';
        \Flexio\Tests\Check::assertString('A.6', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables using json content type',  $actual, $expected, $results);


        // TEST: run pipe with extra parameters

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => "value1",
                "msg" => "value2"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = 'value2';
        \Flexio\Tests\Check::assertString('B.1', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; redefined variables',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "param1" => "value1",
                "param2" => "value2",
                "msg" => "Hello, World!"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = 'Hello, World!';
        \Flexio\Tests\Check::assertString('B.2', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; extra variables',  $actual, $expected, $results);


        // TEST: run pipe with variable string variations

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "Msg" => "Hello, World!"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = '';
        \Flexio\Tests\Check::assertString('C.1', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable case sensitivity',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msgs" => "Hello, World!"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = '';
        \Flexio\Tests\Check::assertString('C.2', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable whole-word sensitivity',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => null
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = '';
        \Flexio\Tests\Check::assertString('C.3', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable null value type handling',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => 1
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = '1';
        \Flexio\Tests\Check::assertString('C.4', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable number value type handling',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => 1.1
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = '1.1';
        \Flexio\Tests\Check::assertString('C.5', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable number value type handling',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => true
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = 'true';
        \Flexio\Tests\Check::assertString('C.6', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable boolean value type handling',  $actual, $expected, $results);
/*
// TODO: fix callApi so it can handle a parameter with an array value
        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => array(1,2,3)
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = '';
        \Flexio\Tests\Check::assertString('C.7', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable boolean value type handling',  $actual, $expected, $results);
*/
        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => "\n"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "\n";
        \Flexio\Tests\Check::assertString('C.8', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable\'s with special values',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => "a\n"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "a\n";
        \Flexio\Tests\Check::assertString('C.9', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable\'s with special values',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => "\nb"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "\nb";
        \Flexio\Tests\Check::assertString('C.10', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable\'s with special values',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => "\""
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "\"";
        \Flexio\Tests\Check::assertString('C.11', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable\'s with special values',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => "\\"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "\\";
        \Flexio\Tests\Check::assertString('C.12', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable\'s with special values',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => "/"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "/";
        \Flexio\Tests\Check::assertString('C.13', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable\'s with special values',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => "boundary="
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "boundary=";
        \Flexio\Tests\Check::assertString('C.14', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable\'s with special values',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid1/run",
            'token' => $token1,
            'params' => array(
                "msg" => "https://www.flex.io"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "https://www.flex.io";
        \Flexio\Tests\Check::assertString('C.15', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; variable\'s with special values',  $actual, $expected, $results);


        // TEST: run pipe with multiple variables

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid3/run",
            'token' => $token1,
            'params' => array(
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "Hello, . Would you like to ?";
        \Flexio\Tests\Check::assertString('D.1', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; multiple variables',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid3/run",
            'token' => $token1,
            'params' => array(
                "noun" => "Scott"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "Hello, Scott. Would you like to ?";
        \Flexio\Tests\Check::assertString('D.2', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; multiple variables with one missing',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid3/run",
            'token' => $token1,
            'params' => array(
                "verb" => "continue"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "Hello, . Would you like to continue?";
        \Flexio\Tests\Check::assertString('D.3', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; multiple variables with one missing',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid3/run",
            'token' => $token1,
            'params' => array(
                "noun" => "Scott",
                "verb" => "continue"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "Hello, Scott. Would you like to continue?";
        \Flexio\Tests\Check::assertString('D.4', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; multiple variables with both populated',  $actual, $expected, $results);

        // BEGIN TEST
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid1/pipes/$objeid3/run",
            'token' => $token1,
            'params' => array(
                "verb" => "continue",
                "noun" => "Scott"
            )
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $actual = $response;
        $expected = "Hello, Scott. Would you like to continue?";
        \Flexio\Tests\Check::assertString('D.5', 'POST /:userid/pipes/:objeid/run; return the results of running a pipe with posted variables; multiple variables with changed order',  $actual, $expected, $results);
    }
}
