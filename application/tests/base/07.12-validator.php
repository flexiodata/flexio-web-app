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
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Base\Validator::check(); return parameter if it passes a number check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a number check',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Validator::check(); fail if parameter doesn\'t pass a number check',  $actual, $expected, $results);
    }
}
