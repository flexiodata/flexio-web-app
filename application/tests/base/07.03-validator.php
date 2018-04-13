<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron N. Williams
 * Created:  2015-05-13
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
        // TEST: make sure the values parameter is specified and is an array

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = null;
        $checks = array();
        $result = $validator->check($values, $checks)->getParams();
        $has_errors = $validator->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Validator::check(); should return false and flag an error if values param isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = true;
        $checks = array();
        $result = $validator->check($values, $checks)->getParams();
        $has_errors = $validator->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Validator::check(); should return false and flag an error if values param isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array();
        $checks = array();
        $result = $validator->check($values, $checks)->getParams();
        $actual = $validator->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Validator::check(); don\'t flag an error if the input values is an array',  $actual, $expected, $results);



        // TEST: make sure the checks parameter is specified and is an array with appropriate validation fields

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = null;
        $checks = array();
        $result = $validator->check($values, $checks)->getParams();
        $has_errors = $validator->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Validator::check(); should return false and flag an error if the checks param isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = true;
        $checks = array();
        $result = $validator->check($values, $checks)->getParams();
        $has_errors = $validator->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Validator::check(); should return false and flag an error if the checks param isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array();
        $checks = array();
        $result = $validator->check($values, $checks)->getParams();
        $actual = $validator->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Validator::check(); don\'t flag an error if the checks param is an array',  $actual, $expected, $results);

        // BEGIN TEST
        // TODO: check format of validation parameter



        // TEST: return parameters should be limited to those specified by the check

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array();
        $checks = array();
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('C.1', '\Flexio\Base\Validator::check(); return parameter should be an empty array when values param is empty',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => $eid
        );
        $checks = array();
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
        );
        \Flexio\Tests\Check::assertArray('C.2', '\Flexio\Base\Validator::check(); return parameter should be an empty array when checks param is empty',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => $eid,
            'name' => 'Object name',
            'description' => 'Object description'
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true),
            'description' => array('type' => 'string', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'eid' => $eid,
            'description' => 'Object description'
        );
        \Flexio\Tests\Check::assertArray('C.3', '\Flexio\Base\Validator::check(); return parameter should be filtered values param based on keys existing in the checks param',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => false, 'default' =>  $eid),
            'description' => array('type' => 'string', 'required' => false, 'default' => 'Object description')
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'eid' => $eid,
            'description' => 'Object description'
        );
        \Flexio\Tests\Check::assertArray('C.4', '\Flexio\Base\Validator::check(); return parameters should add in default values that aren\'t specified in the input',  $actual, $expected, $results);



        // TEST: check basic validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array();
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.1', '\Flexio\Base\Validator::check(); should return false when required parameter isn\'t present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'eid' => $eid,
            'name' => -1
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true),
            'name' => array('type' => 'string', 'required' => true),
            'description' => array('type' => 'string', 'required' => true)
        );
        $result = $validator->check($values, $checks)->getParams();
        $actual = $validator->getErrors();
        $expected = array(
            array(
                'code' => \Flexio\Base\Validator::ERROR_MISSING_PARAMETER, // missing parameters get reported first
                'message' => 'Missing parameter(s): description'
            ),
            array(
                'code' => \Flexio\Base\Validator::ERROR_INVALID_PARAMETER,
                'message' => 'Invalid parameter(s): name:-1'
            )
        );
        \Flexio\Tests\Check::assertArray('D.2', '\Flexio\Base\Validator::check(); flag an error when the input values don\t pass the checks',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => $eid,
            'name' => 'Object name'
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $result = $validator->check($values, $checks)->getParams();
        $actual = $validator->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.3', '\Flexio\Base\Validator::check(); make sure an error isn\'t flagged when the input values pass the checks',  $actual, $expected, $results);



        // TEST: check eid validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => $eid
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'eid' => $eid
        );
        \Flexio\Tests\Check::assertInArray('E.1', '\Flexio\Base\Validator::check(); return parameter if it passes an eid check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => 'abc'
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.2', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an eid check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => 123
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.3', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an eid check',  $actual, $expected, $results);



        // TEST: check password validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $password = \Flexio\Base\Password::generate();
        $values = array(
            'password' => $password
        );
        $checks = array(
            'password' => array('type' => 'password', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'password' => $password
        );
        \Flexio\Tests\Check::assertInArray('F.1', '\Flexio\Base\Validator::check(); return parameter if it passes a password check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'password' => ''
        );
        $checks = array(
            'password' => array('type' => 'password', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.2', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a password check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'password' => 'xxxxxxx1'
        );
        $checks = array(
            'password' => array('type' => 'password', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'password' => 'xxxxxxx1'
        );
        \Flexio\Tests\Check::assertInArray('F.3', '\Flexio\Base\Validator::check(); return parameter if it passes a password check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'password' => 'xxxxxxxx'
        );
        $checks = array(
            'password' => array('type' => 'password', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a password check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'password' => 'xxxxxx1'
        );
        $checks = array(
            'password' => array('type' => 'password', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.5', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a password check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'password' => 123
        );
        $checks = array(
            'password' => array('type' => 'password', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.6', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a password check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'password' => true
        );
        $checks = array(
            'password' => array('type' => 'password', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('F.7', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a password check',  $actual, $expected, $results);



        // TEST: check identifier validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $handle = \Flexio\Base\Identifier::generate();
        $values = array(
            'identifier' => $handle
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'identifier' => $handle
        );
        \Flexio\Tests\Check::assertInArray('G.1', '\Flexio\Base\Validator::check(); return parameter if it passes an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'identifier' => ''
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.2', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'identifier' => 'my-pipe'
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'identifier' => 'my-pipe'
        );
        \Flexio\Tests\Check::assertInArray('G.3', '\Flexio\Base\Validator::check(); return parameter if it passes an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'identifier' => 'pipe'
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'identifier' => 'X'
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.5', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'identifier' => 'xx'
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.6', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'identifier' => 'xxx'
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'identifier' => 'xxx'
        );
        \Flexio\Tests\Check::assertInArray('G.7', '\Flexio\Base\Validator::check(); return parameter if it passes an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'identifier' => \Flexio\Base\Eid::generate()
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.8', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'identifier' => 123
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.9', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'identifier' => true
        );
        $checks = array(
            'identifier' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.10', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);



        // TEST: check alias validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $handle = \Flexio\Base\Identifier::generate();
        $values = array(
            'alias' => $handle
        );
        $checks = array(
            'alias' => array('type' => 'alias', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'alias' => $handle
        );
        \Flexio\Tests\Check::assertInArray('H.1', '\Flexio\Base\Validator::check(); return parameter if it passes an alias check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'alias' => ''
        );
        $checks = array(
            'alias' => array('type' => 'alias', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'alias' => ''
        );
        \Flexio\Tests\Check::assertInArray('H.2', '\Flexio\Base\Validator::check(); return parameter if it passes an alias check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'alias' => 'my-pipe'
        );
        $checks = array(
            'alias' => array('type' => 'alias', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'alias' => 'my-pipe'
        );
        \Flexio\Tests\Check::assertInArray('H.3', '\Flexio\Base\Validator::check(); return parameter if it passes an alias check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'alias' => 'pipe'
        );
        $checks = array(
            'alias' => array('type' => 'alias', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'alias' => 'X'
        );
        $checks = array(
            'alias' => array('type' => 'alias', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.5', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'alias' => \Flexio\Base\Eid::generate()
        );
        $checks = array(
            'alias' => array('type' => 'alias', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.6', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'alias' => 123
        );
        $checks = array(
            'alias' => array('type' => 'alias', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.7', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'alias' => true
        );
        $checks = array(
            'alias' => array('type' => 'alias', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('H.8', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);



        // TEST: check json validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => '{"name" : "value", "properties" : [1,2,3]}'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'definition' => '{"name" : "value", "properties" : [1,2,3]}'
        );
        \Flexio\Tests\Check::assertInArray('I.1', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => '{}'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'definition' => '{}'
        );
        \Flexio\Tests\Check::assertInArray('I.2', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => '[]'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'definition' => '[]'
        );
        \Flexio\Tests\Check::assertInArray('I.3', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => true
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.4', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => false
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.5', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => null
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.6', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => 123
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.7', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => '"abc"'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.8', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => '{'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.9', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => ''
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.10', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'definition' => '{"name" : value"}'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('I.11', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a json check',  $actual, $expected, $results);



        // TEST: check string validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'name' => 'John Williams'
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'name' => 'John Williams'
        );
        \Flexio\Tests\Check::assertInArray('J.1', '\Flexio\Base\Validator::check(); return parameter if it passes a string check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'name' => 1
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.2', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a string check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'name' => true
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.3', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a string check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'name' => null
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('J.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a string check',  $actual, $expected, $results);



        // TEST: check integer validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'value' => 2
        );
        $checks = array(
            'value' => array('type' => 'integer', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'value' => 2
        );
        \Flexio\Tests\Check::assertInArray('K.1', '\Flexio\Base\Validator::check(); return parameter if it passes an integer check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => 3.2
        );
        $checks = array(
            'name' => array('type' => 'integer', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.2', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an integer check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => 'John Williams'
        );
        $checks = array(
            'name' => array('type' => 'integer', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('K.3', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an integer check',  $actual, $expected, $results);



        // TEST: check number validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'value' => 2.1
        );
        $checks = array(
            'value' => array('type' => 'number', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'value' => 2.1
        );
        \Flexio\Tests\Check::assertInArray('L.1', '\Flexio\Base\Validator::check(); return parameter if it passes a number check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => true
        );
        $checks = array(
            'name' => array('type' => 'number', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.2', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a number check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => 'John Williams'
        );
        $checks = array(
            'name' => array('type' => 'number', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('L.3', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a number check',  $actual, $expected, $results);



        // TEST: check date validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'value' => '2018-01-02'
        );
        $checks = array(
            'value' => array('type' => 'date', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'value' => '2018-01-02'
        );
        \Flexio\Tests\Check::assertInArray('M.1', '\Flexio\Base\Validator::check(); return parameter if it passes a date check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'value' => '2018-01-02 02:03:04'
        );
        $checks = array(
            'value' => array('type' => 'date', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'value' => '2018-01-02 02:03:04'
        );
        \Flexio\Tests\Check::assertInArray('M.2', '\Flexio\Base\Validator::check(); return parameter if it passes a date check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => 1
        );
        $checks = array(
            'name' => array('type' => 'date', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.3', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a date check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => true
        );
        $checks = array(
            'name' => array('type' => 'date', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a date check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => 'John Williams'
        );
        $checks = array(
            'name' => array('type' => 'date', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('M.5', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a date check',  $actual, $expected, $results);



        // TEST: check boolean validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'value' => true
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'value' => true
        );
        \Flexio\Tests\Check::assertInArray('N.1', '\Flexio\Base\Validator::check(); return parameter if it passes a boolean check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => false
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'value' => false
        );
        \Flexio\Tests\Check::assertInArray('N.2', '\Flexio\Base\Validator::check(); return parameter if it passes a boolean check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => 'true'
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'value' => true
        );
        \Flexio\Tests\Check::assertInArray('N.3', '\Flexio\Base\Validator::check(); allow boolean parameters to be specified as true/false string',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => 1
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a boolean check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => null
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.5', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a boolean check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => null
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('N.6', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a boolean check',  $actual, $expected, $results);



        // TEST: check any validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'id' => 1,
            'name' => 'John Williams',
            'address' => null,
            'active' => true
        );
        $checks = array(
            'id' => array('type' => 'any', 'required' => true),
            'name' => array('type' => 'any', 'required' => true),
            'address' => array('type' => 'any', 'required' => true),
            'active' => array('type' => 'any', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 1,
            'name' => 'John Williams',
            'address' => null,
            'active' => true
        );
        \Flexio\Tests\Check::assertArray('O.1', '\Flexio\Base\Validator::check(); return parameter if it passes an "any type" check',  $actual, $expected, $results);



        // TEST: non-required parameters

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => ''
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => false),
            'name' => array('type' => 'string', 'required' => false)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.1', '\Flexio\Base\Validator::check(); make sure non-required parameters type are enforced when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => null
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => false)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.2', '\Flexio\Base\Validator::check(); make sure non-required parameters type are enforced when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => $eid
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => false),
            'name' => array('type' => 'string', 'required' => false)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'eid' => $eid
        );
        \Flexio\Tests\Check::assertInArray('P.3', '\Flexio\Base\Validator::check(); make sure non-required parameters type are enforced when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'f1' => array('type' => 'eid', 'required' => false),
            'f2' => array('type' => 'string', 'required' => false),
            'f3' => array('type' => 'number', 'required' => false),
            'f4' => array('type' => 'integer', 'required' => false),
            'f5' => array('type' => 'boolean', 'required' => false),
            'f6' => array('type' => 'any', 'required' => false)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
        );
        \Flexio\Tests\Check::assertInArray('P.4', '\Flexio\Base\Validator::check(); make sure non-required parameters aren\'t enforced if they\'re not present in input values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'f1' => array('type' => 'eid', 'required' => false, 'default' => $eid),
            'f2' => array('type' => 'string', 'required' => false, 'default' => 'abc'),
            'f3' => array('type' => 'number', 'required' => false, 'default' => 123),
            'f4' => array('type' => 'integer', 'required' => false, 'default' => -1),
            'f5' => array('type' => 'boolean', 'required' => false, 'default' => true),
            'f6' => array('type' => 'any', 'required' => false, 'default' => 'def')
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'f1' => $eid,
            'f2' => 'abc',
            'f3' => 123,
            'f4' => -1,
            'f5' => true,
            'f6' => 'def'
        );
        \Flexio\Tests\Check::assertInArray('P.5', '\Flexio\Base\Validator::check(); make sure defaults are supplied for non-required parameters that aren\'t in input values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => false, 'default' => 'x')
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.6', '\Flexio\Base\Validator::check(); make sure default values conform to validation type',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'eid' => array('type' => 'string', 'required' => false, 'default' => 0)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.7', '\Flexio\Base\Validator::check(); make sure default values conform to validation type',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'eid' => array('type' => 'boolean', 'required' => false, 'default' => 'x')
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('P.8', '\Flexio\Base\Validator::check(); make sure default values conform to validation type',  $actual, $expected, $results);



        // TEST: for array parameter, allow delimited lists

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid1 = \Flexio\Base\Eid::generate();
        $eid2 = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => "$eid1,$eid2"
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'array' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'eid' => array(
                $eid1,
                $eid2
            )
        );
        \Flexio\Tests\Check::assertInArray('Q.1', '\Flexio\Base\Validator::check(); if array parameter is specified, allow a comma-delimited list, but make sure each value conforms to validation type',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => "$eid1,x"
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'array' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('Q.2', '\Flexio\Base\Validator::check(); if array parameter is specified, allow a comma-delimited list, but make sure each value conforms to validation type',  $actual, $expected, $results);



        // TEST: allow parameters to be decoded

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid1 = \Flexio\Base\Eid::generate();
        $eid2 = \Flexio\Base\Eid::generate();
        $values = array(
            'info' => '['
        );
        $checks = array(
            'info' => array('type' => 'string', 'decode' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'info' => false
        );
        \Flexio\Tests\Check::assertInArray('R.1', '\Flexio\Base\Validator::check(); if decode is specified, then try to decode the parameter as json; return false if json is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid1 = \Flexio\Base\Eid::generate();
        $eid2 = \Flexio\Base\Eid::generate();
        $values = array(
            'info' => '[1,2,3]'
        );
        $checks = array(
            'info' => array('type' => 'json', 'decode' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'info' => array(
                1,
                2,
                3
            )
        );
        \Flexio\Tests\Check::assertInArray('R.2', '\Flexio\Base\Validator::check(); if decode is specified, then try to decode the parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'info' => '{"a":"b"}'
        );
        $checks = array(
            'info' => array('type' => 'json', 'decode' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'info' => array(
                "a"=>"b"
            )
        );
        \Flexio\Tests\Check::assertInArray('R.3', '\Flexio\Base\Validator::check(); if decode is specified, then try to decode the parameter',  $actual, $expected, $results);
    }
}
