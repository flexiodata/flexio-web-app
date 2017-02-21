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


class Test
{
    public function run(&$results)
    {
        // TEST: single type parameter validation

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": null
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.1', 'ValidatorSchema::check(); return false if the type parameter isn\'t valid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": false
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.2', 'ValidatorSchema::check(); return false if the type parameter isn\'t valid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": ""
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.3', 'ValidatorSchema::check(); return false if the type parameter isn\'t recognized',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": "bad"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.4', 'ValidatorSchema::check(); return false if the type parameter isn\'t recognized',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": "any"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.5', 'ValidatorSchema::check(); \'any\' type no longer allowed in schema validation standard; return false',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": "null"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.6', 'ValidatorSchema::check(); return false if a null value doesn\'t match the specified type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": "boolean"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,true,true,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.7', 'ValidatorSchema::check(); return false if a boolean value doesn\'t match the specified type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": "integer"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,true,true,true,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.8', 'ValidatorSchema::check(); return false if an integer value doesn\'t match the specified type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": "number"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,true,true,true,true,true,false,false,false,false,false,false);
        TestCheck::assertArray('A.9', 'ValidatorSchema::check(); return false if a numeric value doesn\'t match the specified type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": "string"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,true,true,true,true,false,false);
        TestCheck::assertArray('A.10', 'ValidatorSchema::check(); return false if a string value doesn\'t match the specified type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": "object"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,true,false);
        TestCheck::assertArray('A.11', 'ValidatorSchema::check(); return false if an object value doesn\'t match the specified type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": "array"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,true);
        TestCheck::assertArray('A.12', 'ValidatorSchema::check(); return false if an array value doesn\'t match the specified type',  $actual, $expected, $results);



        // TEST: multiple type parameter validation

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": []
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.1', 'ValidatorSchema::check(); return false when empty array of multiple types is specified',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": ["null"]
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.2', 'ValidatorSchema::check(); return false if none of the types in an array of types match the given type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": ["boolean"]
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,true,true,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.3', 'ValidatorSchema::check(); return false if none of the types in an array of types match the given type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": ["null","boolean"]
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.4', 'ValidatorSchema::check(); return false if none of the types in an array of types match the given type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": ["boolean","integer","number","string"]
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,true,true,true,true,true,true,true,true,true,true,true,false,false);
        TestCheck::assertArray('B.5', 'ValidatorSchema::check(); return false if none of the types in an array of types match the given type',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = true;
        $v4 = 0;
        $v5 = 1;
        $v6 = -1;
        $v7 = 1.1;
        $v8 = -0.9;
        $v9 = '';
        $v10 = 'a';
        $v11 = \Eid::generate();
        $v12 = 'identifier1';
        $v13 = json_decode('{}');
        $v14 = json_decode('[]');
        $template = <<<EOD
{
    "type": ["array","object","null"]
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,false,false,false,false,false,false,false,false,false,false,false,true,true);
        TestCheck::assertArray('B.6', 'ValidatorSchema::check(); return false if none of the types in an array of types match the given type',  $actual, $expected, $results);
    }
}
