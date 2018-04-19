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
        // TEST: check email validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => 'test@flex.io'
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'email' => 'test@flex.io'
        );
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Base\Validator::check(); return parameter if it passes an email check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => ''
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an email check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => '@flex.io'
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an email check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => 'test@'
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an email check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => '@'
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an email check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => 'test@flex'
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an email check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => 'Test.Email.Address@Some.Domain.Com'
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'email' => 'Test.Email.Address@Some.Domain.Com'
        );
        \Flexio\Tests\Check::assertInArray('A.7', '\Flexio\Base\Validator::check(); return parameter if it passes an email check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => \Flexio\Base\Eid::generate()
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an email check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => 123
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an email check',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'email' => true
        );
        $checks = array(
            'email' => array('type' => 'email', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.10', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an email check',  $actual, $expected, $results);
    }
}
