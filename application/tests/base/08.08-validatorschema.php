<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        // TODO: add additional tests for array validation parameter for array items


        // test array items

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": null
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\ValidatorSchema::check(); return false if the items parameter isn\'t an array or an object',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": true
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\ValidatorSchema::check(); return false if the items parameter isn\'t an array or an object',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": "a"
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.3', '\Flexio\Base\ValidatorSchema::check(); return false if the items parameter isn\'t an array or an object',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": []
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.4', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true);
        \Flexio\Tests\Check::assertArray('A.5', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": "null"}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,true,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.6', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": "boolean"}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,true,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.7', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": "integer"}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,false,true,false,false,false,true,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.8', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": "string"}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,false,true,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.9', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": "array"}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,true,false,false,false,false,false,false,true,false);
        \Flexio\Tests\Check::assertArray('A.10', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": "object"}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,false,false,false,true,false,false,false,false,false,false,false,false,true);
        \Flexio\Tests\Check::assertArray('A.11', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": ["string","number"]}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,false,true,true,false,false,true,true,true,true,true,true,false,false);
        \Flexio\Tests\Check::assertArray('A.12', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": "object", "required": ["a"], "properties": {"a": {"type": "number"}}}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.13', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": "object", "required": ["b"], "properties": {"b": {"type": "number"}}}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,false,false,false,false,false,false,false,true);
        \Flexio\Tests\Check::assertArray('A.14', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = json_decode('[null]');
        $v8 = json_decode('[false]');
        $v9 = json_decode('[0]');
        $v10 = json_decode('[1.1]');
        $v11 = json_decode('[{}]');
        $v12 = json_decode('[[]]');
        $v13 = json_decode('[0,1,-1]');
        $v14 = json_decode('["a","b","c"]');
        $v15 = json_decode('[1, "a","b","c"]');
        $v16 = json_decode('["a",1,"b","c"]');
        $v17 = json_decode('["a","b",1,"c"]');
        $v18 = json_decode('["a","b","c",1]');
        $v19 = json_decode('[[1],[1.1],["a"],[{}]]');
        $v20 = json_decode('[{"a":1,"b":2},{"a":0,"b":-0.9},{"b":2,"c":3}]');
        $template = <<<EOD
{
    "items": {"type": "object", "required": ["b"], "properties": {"b": {"type": "integer"}}}
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
        $r17 = \Flexio\Base\ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = \Flexio\Base\ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $r19 = \Flexio\Base\ValidatorSchema::check($v19, $template)->hasErrors() === false;
        $r20 = \Flexio\Base\ValidatorSchema::check($v20, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20);
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('A.15', '\Flexio\Base\ValidatorSchema::check(); return false if an array doesn\'t validate against the items schema parameter',  $actual, $expected, $results);
    }
}
