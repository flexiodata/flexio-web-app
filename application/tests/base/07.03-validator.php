<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
    }
}
