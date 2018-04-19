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
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Base\Validator::check(); return parameter if it passes an alias check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.2', '\Flexio\Base\Validator::check(); return parameter if it passes an alias check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.3', '\Flexio\Base\Validator::check(); return parameter if it passes an alias check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass an alias check',  $actual, $expected, $results);
    }
}
