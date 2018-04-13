<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron N. Williams
 * Created:  2018-04-13
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
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.2', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.3', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Validator::check(); return parameter if it passes a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.10', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a json check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.11', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a json check',  $actual, $expected, $results);
    }
}
