<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-13
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: make sure the values parameter is specified and is an array

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = null;
        $checks = array();
        $result = $validator->check($values, $checks);
        $has_errors = $validator->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Validator::check(); should return false and flag an error if values param isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = true;
        $checks = array();
        $result = $validator->check($values, $checks);
        $has_errors = $validator->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Validator::check(); should return false and flag an error if values param isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array();
        $checks = array();
        $result = $validator->check($values, $checks);
        $actual = $validator->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Validator::check(); don\'t flag an error if the input values is an array',  $actual, $expected, $results);



        // TEST: make sure the checks parameter is specified and is an array with appropriate validation fields

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = null;
        $checks = array();
        $result = $validator->check($values, $checks);
        $has_errors = $validator->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Validator::check(); should return false and flag an error if the checks param isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = true;
        $checks = array();
        $result = $validator->check($values, $checks);
        $has_errors = $validator->hasErrors();
        $actual = $result === false && $has_errors === true;
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\System\Validator::check(); should return false and flag an error if the checks param isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array();
        $checks = array();
        $result = $validator->check($values, $checks);
        $actual = $validator->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('B.3', '\Flexio\System\Validator::check(); don\'t flag an error if the checks param is an array',  $actual, $expected, $results);

        // BEGIN TEST
        // TODO: check format of validation parameter



        // TEST: return parameters should be limited to those specified by the check

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array();
        $checks = array();
        $actual = $validator->check($values, $checks);
        $expected = array(
        );
        TestCheck::assertArray('C.1', '\Flexio\System\Validator::check(); return parameter should be an empty array when values param is empty',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => $eid
        );
        $checks = array();
        $actual = $validator->check($values, $checks);
        $expected = array(
        );
        TestCheck::assertArray('C.2', '\Flexio\System\Validator::check(); return parameter should be an empty array when checks param is empty',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
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
        $actual = $validator->check($values, $checks);
        $expected = array(
            'eid' => $eid,
            'description' => 'Object description'
        );
        TestCheck::assertArray('C.3', '\Flexio\System\Validator::check(); return parameter should be filtered values param based on keys existing in the checks param',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => false, 'default' =>  $eid),
            'description' => array('type' => 'string', 'required' => false, 'default' => 'Object description')
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'eid' => $eid,
            'description' => 'Object description'
        );
        TestCheck::assertArray('C.4', '\Flexio\System\Validator::check(); return parameters should add in default values that aren\'t specified in the input',  $actual, $expected, $results);



        // TEST: check basic validation

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array();
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('D.1', '\Flexio\System\Validator::check(); should return false when required parameter isn\'t present',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'eid' => $eid,
            'name' => -1
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true),
            'name' => array('type' => 'string', 'required' => true),
            'description' => array('type' => 'string', 'required' => true)
        );
        $result = $validator->check($values, $checks);
        $actual = $validator->getErrors();
        $expected = array(
            array(
                'code' => \Flexio\System\Validator::ERROR_MISSING_PARAMETER, // missing parameters get reported first
                'message' => 'Missing parameter(s): description'
            ),
            array(
                'code' => \Flexio\System\Validator::ERROR_INVALID_PARAMETER,
                'message' => 'Invalid parameter(s): name:-1'
            )
        );
        TestCheck::assertArray('D.2', '\Flexio\System\Validator::check(); flag an error when the input values don\t pass the checks',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => $eid,
            'name' => 'Object name'
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $result = $validator->check($values, $checks);
        $actual = $validator->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('D.3', '\Flexio\System\Validator::check(); make sure an error isn\'t flagged when the input values pass the checks',  $actual, $expected, $results);



        // TEST: check eid validation

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => $eid
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'eid' => $eid
        );
        TestCheck::assertInArray('E.1', '\Flexio\System\Validator::check(); return parameter if it passes an eid check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => 'abc'
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('E.2', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass an eid check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => 123
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('E.3', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass an eid check',  $actual, $expected, $results);



        // TEST: check identifier validation

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $handle = \Flexio\System\Util::generateHandle();
        $values = array(
            'id' => $handle
        );
        $checks = array(
            'id' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'id' => $handle
        );
        TestCheck::assertInArray('F.1', '\Flexio\System\Validator::check(); return parameter if it passes an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'id' => 'X'
        );
        $checks = array(
            'id' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('F.2', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'id' => 123
        );
        $checks = array(
            'id' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('F.3', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'id' => true
        );
        $checks = array(
            'id' => array('type' => 'identifier', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('F.4', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);



        // TEST: check json validation

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => '{"name" : "value", "properties" : [1,2,3]}'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'definition' => '{"name" : "value", "properties" : [1,2,3]}'
        );
        TestCheck::assertInArray('G.1', '\Flexio\System\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => '{}'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'definition' => '{}'
        );
        TestCheck::assertInArray('G.2', '\Flexio\System\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => '[]'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'definition' => '[]'
        );
        TestCheck::assertInArray('G.3', '\Flexio\System\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => true
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('G.4', '\Flexio\System\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => false
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('G.5', '\Flexio\System\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => null
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('G.6', '\Flexio\System\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => 123
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('G.7', '\Flexio\System\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => '"abc"'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('G.8', '\Flexio\System\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => '{'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('G.9', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => ''
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('G.10', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a json check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'definition' => '{"name" : value"}'
        );
        $checks = array(
            'definition' => array('type' => 'json', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('G.11', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a json check',  $actual, $expected, $results);



        // TEST: check string validation

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'name' => 'John Williams'
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'name' => 'John Williams'
        );
        TestCheck::assertInArray('H.1', '\Flexio\System\Validator::check(); return parameter if it passes a string check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'name' => 1
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('H.2', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a string check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'name' => true
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('H.3', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a string check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'name' => null
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('H.4', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a string check',  $actual, $expected, $results);



        // TEST: check integer validation

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'value' => 2
        );
        $checks = array(
            'value' => array('type' => 'integer', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'value' => 2
        );
        TestCheck::assertInArray('I.1', '\Flexio\System\Validator::check(); return parameter if it passes an integer check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => 3.2
        );
        $checks = array(
            'name' => array('type' => 'integer', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('I.2', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass an integer check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => 'John Williams'
        );
        $checks = array(
            'name' => array('type' => 'integer', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('I.3', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass an integer check',  $actual, $expected, $results);



        // TEST: check number validation

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'value' => 2.1
        );
        $checks = array(
            'value' => array('type' => 'number', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'value' => 2.1
        );
        TestCheck::assertInArray('J.1', '\Flexio\System\Validator::check(); return parameter if it passes a number check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => true
        );
        $checks = array(
            'name' => array('type' => 'number', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('J.2', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a number check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => 'John Williams'
        );
        $checks = array(
            'name' => array('type' => 'number', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('J.3', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a number check',  $actual, $expected, $results);



        // TEST: check boolean validation

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $values = array(
            'value' => true
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'value' => true
        );
        TestCheck::assertInArray('K.1', '\Flexio\System\Validator::check(); return parameter if it passes a boolean check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => false
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'value' => false
        );
        TestCheck::assertInArray('K.2', '\Flexio\System\Validator::check(); return parameter if it passes a boolean check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => 'true'
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'value' => true
        );
        TestCheck::assertInArray('K.3', '\Flexio\System\Validator::check(); allow boolean parameters to be specified as true/false string',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => 1
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('K.4', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a boolean check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => null
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('K.5', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a boolean check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'value' => null
        );
        $checks = array(
            'value' => array('type' => 'boolean', 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('K.6', '\Flexio\System\Validator::check(); fail if parameter doesn\'t pass a boolean check',  $actual, $expected, $results);



        // TEST: check any validation

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
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
        $actual = $validator->check($values, $checks);
        $expected = array(
            'id' => 1,
            'name' => 'John Williams',
            'address' => null,
            'active' => true
        );
        TestCheck::assertArray('L.1', '\Flexio\System\Validator::check(); return parameter if it passes an "any type" check',  $actual, $expected, $results);



        // TEST: non-required parameters

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => ''
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => false),
            'name' => array('type' => 'string', 'required' => false)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('M.1', '\Flexio\System\Validator::check(); make sure non-required parameters type are enforced when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'name' => null
        );
        $checks = array(
            'name' => array('type' => 'string', 'required' => false)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('M.2', '\Flexio\System\Validator::check(); make sure non-required parameters type are enforced when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => $eid
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => false),
            'name' => array('type' => 'string', 'required' => false)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'eid' => $eid
        );
        TestCheck::assertInArray('M.3', '\Flexio\System\Validator::check(); make sure non-required parameters type are enforced when specified',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
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
        $actual = $validator->check($values, $checks);
        $expected = array(
        );
        TestCheck::assertInArray('M.4', '\Flexio\System\Validator::check(); make sure non-required parameters aren\'t enforced if they\'re not present in input values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
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
        $actual = $validator->check($values, $checks);
        $expected = array(
            'f1' => $eid,
            'f2' => 'abc',
            'f3' => 123,
            'f4' => -1,
            'f5' => true,
            'f6' => 'def'
        );
        TestCheck::assertInArray('M.5', '\Flexio\System\Validator::check(); make sure defaults are supplied for non-required parameters that aren\'t in input values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'required' => false, 'default' => 'x')
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('M.6', '\Flexio\System\Validator::check(); make sure default values conform to validation type',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'eid' => array('type' => 'string', 'required' => false, 'default' => 0)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('M.7', '\Flexio\System\Validator::check(); make sure default values conform to validation type',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
        );
        $checks = array(
            'eid' => array('type' => 'boolean', 'required' => false, 'default' => 'x')
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('M.8', '\Flexio\System\Validator::check(); make sure default values conform to validation type',  $actual, $expected, $results);



        // TEST: for array parameter, allow delimited lists

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid1 = \Flexio\Base\Eid::generate();
        $eid2 = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => "$eid1,$eid2"
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'array' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'eid' => array(
                $eid1,
                $eid2
            )
        );
        TestCheck::assertInArray('N.1', '\Flexio\System\Validator::check(); if array parameter is specified, allow a comma-delimited list, but make sure each value conforms to validation type',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => "$eid1,x"
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'array' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = false;
        TestCheck::assertBoolean('N.2', '\Flexio\System\Validator::check(); if array parameter is specified, allow a comma-delimited list, but make sure each value conforms to validation type',  $actual, $expected, $results);



        // TEST: allow parameters to be decoded

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid1 = \Flexio\Base\Eid::generate();
        $eid2 = \Flexio\Base\Eid::generate();
        $values = array(
            'info' => '['
        );
        $checks = array(
            'info' => array('type' => 'string', 'decode' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'info' => false
        );
        TestCheck::assertInArray('M.1', '\Flexio\System\Validator::check(); if decode is specified, then try to decode the parameter as json; return false if json is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid1 = \Flexio\Base\Eid::generate();
        $eid2 = \Flexio\Base\Eid::generate();
        $values = array(
            'info' => '[1,2,3]'
        );
        $checks = array(
            'info' => array('type' => 'json', 'decode' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'info' => array(
                1,
                2,
                3
            )
        );
        TestCheck::assertInArray('M.2', '\Flexio\System\Validator::check(); if decode is specified, then try to decode the parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = new \Flexio\System\Validator;
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'info' => '{"a":"b"}'
        );
        $checks = array(
            'info' => array('type' => 'json', 'decode' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks);
        $expected = array(
            'info' => array(
                "a"=>"b"
            )
        );
        TestCheck::assertInArray('M.3', '\Flexio\System\Validator::check(); if decode is specified, then try to decode the parameter',  $actual, $expected, $results);
    }
}
