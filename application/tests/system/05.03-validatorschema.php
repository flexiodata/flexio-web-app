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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: numeric minimum validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": null
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.1', '\ValidatorSchema::check(); return false if the minimum value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": false
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.2', '\ValidatorSchema::check(); return false if the minimum value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": -1.1
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,true,true,true,true,true,true,true);
        TestCheck::assertArray('A.3', '\ValidatorSchema::check(); return false if a numeric value is less than or equal to than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": -1.0
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,true,true,true,true,true,true);
        TestCheck::assertArray('A.4', '\ValidatorSchema::check(); return false if a numeric value is less than or equal to the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": 0
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,true,true,true,true,true);
        TestCheck::assertArray('A.5', '\ValidatorSchema::check(); return false if a numeric value is less than or equal to the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": 1
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,false,true,true,true,true);
        TestCheck::assertArray('A.6', '\ValidatorSchema::check(); return false if a numeric value is less than or equal to the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": 1.1
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,true,true,true);
        TestCheck::assertArray('A.7', '\ValidatorSchema::check(); return false if a numeric value is less than or equal to the minimum allowed',  $actual, $expected, $results);



        // TEST: numeric minimum validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": null,
    "exclusiveMinimum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.1', '\ValidatorSchema::check(); return false if the minimum value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": false,
    "exclusiveMinimum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.2', '\ValidatorSchema::check(); return false if the minimum value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": -1.1,
    "exclusiveMinimum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,true,true,true,true,true,true);
        TestCheck::assertArray('B.3', '\ValidatorSchema::check(); return false if a numeric value is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": -1.0,
    "exclusiveMinimum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,true,true,true,true,true);
        TestCheck::assertArray('B.4', '\ValidatorSchema::check(); return false if a numeric value is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": 0,
    "exclusiveMinimum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,false,true,true,true,true);
        TestCheck::assertArray('B.5', '\ValidatorSchema::check(); return false if a numeric value is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": 1,
    "exclusiveMinimum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,true,true,true);
        TestCheck::assertArray('B.6', '\ValidatorSchema::check(); return false if a numeric value is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "minimum": 1.1,
    "exclusiveMinimum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,false,false,false,true,true);
        TestCheck::assertArray('B.7', '\ValidatorSchema::check(); return false if a numeric value is less than the minimum allowed',  $actual, $expected, $results);



        // TEST: numeric minimum validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": null
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('C.1', '\ValidatorSchema::check(); return false if the maximum value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": false
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('C.2', '\ValidatorSchema::check(); return false if the maximum value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": -1.1
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,true,false,false,false,false,false,false);
        TestCheck::assertArray('C.3', '\ValidatorSchema::check(); return false if a numeric value is greater than or equal to than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": -1.0
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,true,true,false,false,false,false,false);
        TestCheck::assertArray('C.4', '\ValidatorSchema::check(); return false if a numeric value is greater than or equal to the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": 0
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,true,true,true,false,false,false,false);
        TestCheck::assertArray('C.5', '\ValidatorSchema::check(); return false if a numeric value is greater than or equal to the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": 1
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,true,true,true,true,false,false,false);
        TestCheck::assertArray('C.6', '\ValidatorSchema::check(); return false if a numeric value is greater than or equal to the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": 1.1
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,true,true,true,true,true,false,false);
        TestCheck::assertArray('C.7', '\ValidatorSchema::check(); return false if a numeric value is greatear than or equal to the maximum allowed',  $actual, $expected, $results);



        // TEST: numeric minimum validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": null,
"exclusiveMaximum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('D.1', '\ValidatorSchema::check(); return false if the maximum value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": false,
"exclusiveMaximum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('D.2', '\ValidatorSchema::check(); return false if the maximum value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": -1.1,
"exclusiveMaximum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,false,false,false,false,false,false,false);
        TestCheck::assertArray('D.3', '\ValidatorSchema::check(); return false if a numeric value is greatear than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": -1.0,
"exclusiveMaximum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,true,false,false,false,false,false,false);
        TestCheck::assertArray('D.4', '\ValidatorSchema::check(); return false if a numeric value is greater than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": 0,
"exclusiveMaximum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,true,true,false,false,false,false,false);
        TestCheck::assertArray('D.5', '\ValidatorSchema::check(); return false if a numeric value is greater than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": 1,
"exclusiveMaximum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,true,true,true,false,false,false,false);
        TestCheck::assertArray('D.6', '\ValidatorSchema::check(); return false if a numeric value is greater than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
"maximum": 1.1,
"exclusiveMaximum": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,true,true,true,true,false,false,false);
        TestCheck::assertArray('D.7', '\ValidatorSchema::check(); return false if a numeric value is greater than the maximum allowed',  $actual, $expected, $results);



        // TEST: numeric multipleOf validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": null
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('E.1', '\ValidatorSchema::check(); return false if the multipleOf value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": false
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('E.2', '\ValidatorSchema::check(); return false if the multipleOf value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 0
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('E.3', '\ValidatorSchema::check(); return false if the multipleOf value is invalid; multipleOf must be strictly greater than zero',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": -1
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('E.4', '\ValidatorSchema::check(); return false if the multipleOf value is invalid; multipleOf must be strictly greater than zero',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 1
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,true,true,true,false,false,true);
        TestCheck::assertArray('E.5', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 2
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,true,false,false,false,true);
        TestCheck::assertArray('E.6', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 1.1
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,true,false,true,false,true,false,false);
        TestCheck::assertArray('E.7', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 1.2
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,false,false,true,false,false,true,false);
        TestCheck::assertArray('E.8', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 0.2
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,false,true,true,true,false,true,true);
        TestCheck::assertArray('E.9', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 0.3
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,false,false,true,false,false,true,false);
        TestCheck::assertArray('E.10', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 0.00000000000011
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,true,false,true,false,true,false,false);
        TestCheck::assertArray('E.11', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 0.00000000000003
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,false,false,true,false,false,true,false);
        TestCheck::assertArray('E.12', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 1.000000000000001
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,true,false,false,false,false);
        TestCheck::assertArray('E.13', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 0.600000000000001
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,false,false,false,true,false,false,false,false);
        TestCheck::assertArray('E.14', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = '';
        $v4 = 'a';
        $v5 = json_decode('{}');
        $v6 = json_decode('[]');
        $v7 = -1.2;
        $v8 = -1.1;
        $v9 = -1;
        $v10 = 0;
        $v11 = 1;
        $v12 = 1.1;
        $v13 = 1.2;
        $v14 = 2;
        $template = <<<EOD
{
    "multipleOf": 0.000000000000048
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = \ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = \ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = \ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = \ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = \ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = \ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = \ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14);
        $expected = array(true,true,true,true,true,true,true,false,false,true,false,false,true,false);
        TestCheck::assertArray('E.15', '\ValidatorSchema::check(); return false if a numeric value is not a multiple of the validation parameter',  $actual, $expected, $results);
    }
}
