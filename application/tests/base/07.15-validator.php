<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
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
        // TEST: check any validation

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $eid = \Flexio\Base\Eid::generate();
        $values = array(
            'id' => 1,
            'name' => 'John Williams',
            'address' => null,
            'active' => true
        );
        $checks = array(
            'id' => array('type' => 'any', 'required' => true),
            'name' => array('type' => 'any', 'required' => true),
            'address' => array('type' => 'any', 'required' => true),
            'active' => array('type' => 'any', 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 1,
            'name' => 'John Williams',
            'address' => null,
            'active' => true
        );
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Validator::check(); return parameter if it passes an "any type" check',  $actual, $expected, $results);
    }
}
