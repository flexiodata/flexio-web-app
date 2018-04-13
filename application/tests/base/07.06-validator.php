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
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Base\Validator::check(); return parameter if it passes an identifier check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.3', '\Flexio\Base\Validator::check(); return parameter if it passes an identifier check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.7', '\Flexio\Base\Validator::check(); return parameter if it passes an identifier check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.10', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an identifier check',  $actual, $expected, $results);
    }
}
