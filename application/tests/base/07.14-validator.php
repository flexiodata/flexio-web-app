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
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Base\Validator::check(); return parameter if it passes a boolean check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.2', '\Flexio\Base\Validator::check(); return parameter if it passes a boolean check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.3', '\Flexio\Base\Validator::check(); allow boolean parameters to be specified as true/false string',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a boolean check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a boolean check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a boolean check',  $actual, $expected, $results);
    }
}
