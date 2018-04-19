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
        // TEST: allow parameters to be decoded

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid1 = \Flexio\Base\Eid::generate();
        $eid2 = \Flexio\Base\Eid::generate();
        $values = array(
            'info' => '['
        );
        $checks = array(
            'info' => array('type' => 'string', 'decode' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'info' => false
        );
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Base\Validator::check(); if decode is specified, then try to decode the parameter as json; return false if json is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid1 = \Flexio\Base\Eid::generate();
        $eid2 = \Flexio\Base\Eid::generate();
        $values = array(
            'info' => '[1,2,3]'
        );
        $checks = array(
            'info' => array('type' => 'json', 'decode' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'info' => array(
                1,
                2,
                3
            )
        );
        \Flexio\Tests\Check::assertInArray('A.2', '\Flexio\Base\Validator::check(); if decode is specified, then try to decode the parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'info' => '{"a":"b"}'
        );
        $checks = array(
            'info' => array('type' => 'json', 'decode' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'info' => array(
                "a"=>"b"
            )
        );
        \Flexio\Tests\Check::assertInArray('A.3', '\Flexio\Base\Validator::check(); if decode is specified, then try to decode the parameter',  $actual, $expected, $results);
    }
}
