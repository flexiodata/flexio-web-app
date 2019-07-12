<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
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
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Validator::check(); make sure non-required parameters type are enforced when specified',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Validator::check(); make sure non-required parameters type are enforced when specified',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.3', '\Flexio\Base\Validator::check(); make sure non-required parameters type are enforced when specified',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.4', '\Flexio\Base\Validator::check(); make sure non-required parameters aren\'t enforced if they\'re not present in input values',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertInArray('A.5', '\Flexio\Base\Validator::check(); make sure defaults are supplied for non-required parameters that aren\'t in input values',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Validator::check(); make sure default values conform to validation type',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Validator::check(); make sure default values conform to validation type',  $actual, $expected, $results);

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
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Validator::check(); make sure default values conform to validation type',  $actual, $expected, $results);
    }
}
