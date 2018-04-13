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
        // test required object properties

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": null
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
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\ValidatorSchema::check(); return false if the required parameter isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": true
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
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\ValidatorSchema::check(); return false if the required parameter isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": "a"
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
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\ValidatorSchema::check(); return false if the required parameter isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": []
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
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\ValidatorSchema::check(); return false if the required parameter is empty',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": [null,true,[]]
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
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Base\ValidatorSchema::check(); return false if the required parameter contains something besides strings',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["a","a"]
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
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Base\ValidatorSchema::check(); return false if the required parameter contains duplicate string values',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["a"]
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
        $expected = array(true,true,true,true,true,true,false,true,false,false,false,false,true,false,false,false);
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Base\ValidatorSchema::check(); return false if a matching object doesn\'t contain all of the required keys',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["b"]
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
        $expected = array(true,true,true,true,true,true,false,false,true,false,false,false,true,true,false,false);
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Base\ValidatorSchema::check(); return false if a matching object doesn\'t contain all of the required keys',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["c"]
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
        $expected = array(true,true,true,true,true,true,false,false,false,true,false,false,false,true,true,false);
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Base\ValidatorSchema::check(); return false if a matching object doesn\'t contain all of the required keys',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["d"]
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
        $expected = array(true,true,true,true,true,true,false,false,false,false,true,false,false,false,true,true);
        \Flexio\Tests\Check::assertArray('A.9', '\Flexio\Base\ValidatorSchema::check(); return false if a matching object doesn\'t contain all of the required keys',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["e"]
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
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,true,false,false,false,true);
        \Flexio\Tests\Check::assertArray('A.10', '\Flexio\Base\ValidatorSchema::check(); return false if a matching object doesn\'t contain all of the required keys',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["a","b"]
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
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,true,false,false,false);
        \Flexio\Tests\Check::assertArray('A.11', '\Flexio\Base\ValidatorSchema::check(); return false if a matching object doesn\'t contain all of the required keys',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["a","b","c"]
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
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.11', '\Flexio\Base\ValidatorSchema::check(); return false if a matching object doesn\'t contain all of the required keys',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["a","c"]
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
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.12', '\Flexio\Base\ValidatorSchema::check(); return false if a matching object doesn\'t contain all of the required keys',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = -0.9;
        $v5 = 'a';
        $v6 = json_decode('[]');
        $v7 = json_decode('{}');
        $v8 = json_decode('{"a":null}');
        $v9 = json_decode('{"b":false}');
        $v10 = json_decode('{"c":0}');
        $v11 = json_decode('{"d":""}');
        $v12 = json_decode('{"e":[]}');
        $v13 = json_decode('{"a":{}, "b":true}');
        $v14 = json_decode('{"b":true, "c":1}');
        $v15 = json_decode('{"c":1.1, "d":"a"}');
        $v16 = json_decode('{"d":"abc", "e":[1,2,3]}');
        $template = <<<EOD
{
    "required": ["b","c"]
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
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,false,true,false,false);
        \Flexio\Tests\Check::assertArray('A.13', '\Flexio\Base\ValidatorSchema::check(); return false if a matching object doesn\'t contain all of the required keys',  $actual, $expected, $results);
    }
}
