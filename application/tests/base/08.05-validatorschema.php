<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-17
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
        // TEST: single value enum validation

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": []
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [9999]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [null]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(true,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [false]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,true,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [true]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,true,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [1]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,true,true,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [1.0]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,true,true,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [-0.9]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,true,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [""]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,true,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.9', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": ["a"]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,true,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.10', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": ["abc"]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,true,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.11', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [{}]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,true,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.12', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [{"a":1}]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,false,true,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.13', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [{"a":1,"b":[1,2,{}],"c":{"a":"1"}}]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,true,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.14', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [[]]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,true,false,false,false);
        \Flexio\Tests\Check::assertArray('A.15', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [[true,false]]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,true,false,false);
        \Flexio\Tests\Check::assertArray('A.16', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [[["a"],["b"]]]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,true,false);
        \Flexio\Tests\Check::assertArray('A.17', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [[{"a":1},{"b":2}]]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,true);
        \Flexio\Tests\Check::assertArray('A.18', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);



        // TEST: multiple value enum validation

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [true,false]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,true,true,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": [{},[]]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(false,false,false,false,false,false,false,false,false,true,false,false,true,false,false,false);
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 1;
        $v5 = 1.0;
        $v6 = -0.9;
        $v7 = '';
        $v8 = 'a';
        $v9 = 'abc';
        $v10 = json_decode('{}');
        $v11 = json_decode('{"a":1}');
        $v12 = json_decode('{"a":1,"b":[1,2,{}],"c":{"a":"1"}}');
        $v13 = json_decode('[]');
        $v14 = json_decode('[true,false]');
        $v15 = json_decode('[["a"],["b"]]');
        $v16 = json_decode('[{"a":1},{"b":2}]');
        $template = <<<EOD
{
    "enum": ["",1,2,3,null,"ab",[true,false],{"a":1,"b":[1,2,{}]}]
}
EOD;
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \Flexio\Base\ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \Flexio\Base\ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \Flexio\Base\ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \Flexio\Base\ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \Flexio\Base\ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \Flexio\Base\ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \Flexio\Base\ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = \Flexio\Base\ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = \Flexio\Base\ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16);
        $expected = array(true,false,false,true,true,false,true,false,false,false,false,false,false,true,false,false);
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Base\ValidatorSchema::check(); return false if value isn\'t one of the enumerated values',  $actual, $expected, $results);
    }
}
