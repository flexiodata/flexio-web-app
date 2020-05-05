<?php
/**
 *
 * Copyright (c) 2018, Flex Research LLC. All rights reserved.
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
        // TEST: check enumeration of allowed values

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 1
        );
        $checks = array(
            'id' => array('type' => 'number', 'required' => true, 'enum' => [])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 1
        );
        $checks = array(
            'id' => array('type' => 'number', 'required' => true, 'enum' => [2])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 1
        );
        $checks = array(
            'id' => array('type' => 'number', 'required' => true, 'enum' => ['1'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 1
        );
        $checks = array(
            'id' => array('type' => 'number', 'required' => true, 'enum' => [1])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 1
        );
        \Flexio\Tests\Check::assertInArray('A.4', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 1
        );
        $checks = array(
            'id' => array('type' => 'number', 'required' => true, 'enum' => [1,2])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 1
        );
        \Flexio\Tests\Check::assertInArray('A.5', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 1
        );
        $checks = array(
            'id' => array('type' => 'number', 'required' => true, 'enum' => [1,2])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 1
        );
        \Flexio\Tests\Check::assertInArray('A.6', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 'A'
        );
        $checks = array(
            'id' => array('enum' => [''])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 'A'
        );
        $checks = array(
            'id' => array('enum' => ['a'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 'A'
        );
        $checks = array(
            'id' => array('enum' => ['AA'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 'A'
        );
        $checks = array(
            'id' => array('enum' => ['A'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 'A'
        );
        \Flexio\Tests\Check::assertInArray('A.10', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => ''
        );
        $checks = array(
            'id' => array('enum' => ['','A','B'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => ''
        );
        \Flexio\Tests\Check::assertInArray('A.11', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 'A'
        );
        $checks = array(
            'id' => array('enum' => ['','A','B'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 'A'
        );
        \Flexio\Tests\Check::assertInArray('A.12', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 'B'
        );
        $checks = array(
            'id' => array('enum' => ['','A','B'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 'B'
        );
        \Flexio\Tests\Check::assertInArray('A.13', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
        );
        $checks = array(
            'id' => array('enum' => ['','A','B'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
        );
        \Flexio\Tests\Check::assertInArray('A.14', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
        );
        $checks = array(
            'id' => array('enum' => ['','A','B'], 'required' => true)
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.15', '\Flexio\Base\Validator::check(); check enumeration of allowed values',  $actual, $expected, $results);



        // TEST: check enumeration of allowed values; make sure values still pass other contraints

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 1
        );
        $checks = array(
            'id' => array('type' => 'string', 'required' => true, 'enum' => [1])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => true
        );
        $checks = array(
            'id' => array('type' => 'any', 'required' => true, 'enum' => [1,'A'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 1
        );
        $checks = array(
            'id' => array('type' => 'any', 'required' => true, 'enum' => [1,'A',true])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 1
        );
        \Flexio\Tests\Check::assertInArray('B.3', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 'A'
        );
        $checks = array(
            'id' => array('type' => 'any', 'required' => true, 'enum' => [1,'A',true])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => 'A'
        );
        \Flexio\Tests\Check::assertInArray('B.4', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => true
        );
        $checks = array(
            'id' => array('type' => 'any', 'required' => true, 'enum' => [1,'A',true])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'id' => true
        );
        \Flexio\Tests\Check::assertInArray('B.5', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => false
        );
        $checks = array(
            'id' => array('type' => 'any', 'required' => true, 'enum' => [1,'A',true])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'id' => 1
        );
        $checks = array(
            'status' => array('type' => 'string', 'required' => false, 'enum' => ['A'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
        );
        \Flexio\Tests\Check::assertInArray('B.7', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'status' => 'A'
        );
        $checks = array(
            'status' => array('type' => 'string', 'required' => false, 'enum' => ['A'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'status' => 'A'
        );
        \Flexio\Tests\Check::assertInArray('B.8', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'status' => 'B'
        );
        $checks = array(
            'status' => array('type' => 'string', 'required' => false, 'enum' => ['A'])
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.9', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);



        // TEST: check enumeration of allowed values

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'status' => 'A'
        );
        $checks = array(
            'status' => array(
                'array' => true, // explode status into an array, each element of which must satisfy type/enum
                'type' => 'string',
                'required' => false,
                'enum' => ['A','B']
            )
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'status' => ['A']
        );
        \Flexio\Tests\Check::assertInArray('C.1', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'status' => 'B'
        );
        $checks = array(
            'status' => array(
                'array' => true, // explode status into an array, each element of which must satisfy type/enum
                'type' => 'string',
                'required' => false,
                'enum' => ['A','B']
            )
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'status' => ['B']
        );
        \Flexio\Tests\Check::assertInArray('C.2', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'status' => 'A,B'
        );
        $checks = array(
            'status' => array(
                'array' => true, // explode status into an array, each element of which must satisfy type/enum
                'type' => 'string',
                'required' => false,
                'enum' => ['A','B']
            )
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'status' => ['A','B']
        );
        \Flexio\Tests\Check::assertInArray('C.3', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'status' => 'A,B'
        );
        $checks = array(
            'status' => array(
                'array' => false, // explode status into an array, each element of which must satisfy type/enum
                'type' => 'string',
                'required' => false,
                'enum' => ['A,B']
            )
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = array(
            'status' => 'A,B'
        );
        \Flexio\Tests\Check::assertInArray('C.4', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'status' => 'A,B'
        );
        $checks = array(
            'status' => array(
                'array' => false, // explode status into an array, each element of which must satisfy type/enum
                'type' => 'string',
                'required' => false,
                'enum' => ['A','B']
            )
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.5', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);

        // BEGIN TEST
        $validator = \Flexio\Base\Validator::create();
        $values = array(
            'status' => 'A,C'
        );
        $checks = array(
            'status' => array(
                'array' => true, // explode status into an array, each element of which must satisfy type/enum
                'type' => 'string',
                'required' => false,
                'enum' => ['A','B']
            )
        );
        $actual = $validator->check($values, $checks)->getParams();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.6', '\Flexio\Base\Validator::check(); check enumeration of allowed values; value must still pass other contraints',  $actual, $expected, $results);
    }
}
