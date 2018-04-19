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
        // TEST: for array parameter, allow delimited lists

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid1 = \Flexio\Base\Eid::generate();
        $eid2 = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => "$eid1,$eid2"
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'array' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'eid' => array(
                $eid1,
                $eid2
            )
        );
        \Flexio\Tests\Check::assertInArray('A.1', '\Flexio\Base\Validator::check(); if array parameter is specified, allow a comma-delimited list, but make sure each value conforms to validation type',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'eid' => "$eid1,x"
        );
        $checks = array(
            'eid' => array('type' => 'eid', 'array' => true, 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Validator::check(); if array parameter is specified, allow a comma-delimited list, but make sure each value conforms to validation type',  $actual, $expected, $results);
    }
}
