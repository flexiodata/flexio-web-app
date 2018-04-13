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
        // TEST: allow null parameter values if specified

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'date' => null,
            'number' => null
        );
        $checks = array(
            'date' => array('type' => 'date', 'required' => true, 'allow_null' => true),
            'number' => array('type' => 'number', 'required' => true, 'allow_null' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'date' => null,
            'number' => null
        );
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Base\Validator::check(); enforce the type or a null if it\'s allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'date' => '2018-01-02',
            'number' => 1.23
        );
        $checks = array(
            'date' => array('type' => 'date', 'required' => true, 'allow_null' => true),
            'number' => array('type' => 'number', 'required' => true, 'allow_null' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'date' => '2018-01-02',
            'number' => 1.23
        );
        \Flexio\Tests\Check::assertInArray('A.2', '\Flexio\Base\Validator::check(); enforce the type or a null if it\'s allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'date' => 'x'
        );
        $checks = array(
            'date' => array('type' => 'date', 'required' => true, 'allow_null' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Validator::check(); enforce the type or a null if it\'s allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'number' => 'x'
        );
        $checks = array(
            'number' => array('type' => 'number', 'required' => true, 'allow_null' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Validator::check(); enforce the type or a null if it\'s allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
        );
        $checks = array(
            'date' => array('type' => 'date', 'required' => false, 'default' => null, 'allow_null' => true),
            'number' => array('type' => 'date', 'required' => false, 'default' => null, 'allow_null' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'date' => null,
            'number' => null
        );
        \Flexio\Tests\Check::assertInArray('A.5', '\Flexio\Base\Validator::check(); enforce the type or a null if it\'s allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
        );
        $checks = array(
            'date' => array('type' => 'date', 'required' => false, 'default' => '2018-01-02', 'allow_null' => true),
            'number' => array('type' => 'number', 'required' => false, 'default' => 1.23, 'allow_null' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'date' => '2018-01-02',
            'number' => 1.23
        );
        \Flexio\Tests\Check::assertInArray('A.6', '\Flexio\Base\Validator::check(); enforce the type or a null if it\'s allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'date' => 'x'
        );
        $checks = array(
            'date' => array('type' => 'date', 'required' => false, 'default' => '2018-01-02', 'allow_null' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Validator::check(); enforce the type or a null if it\'s allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'number' => 'x'
        );
        $checks = array(
            'number' => array('type' => 'number', 'required' => false, 'default' => 1.23, 'allow_null' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Validator::check(); enforce the type or a null if it\'s allowed',  $actual, $expected, $results);
    }
}
