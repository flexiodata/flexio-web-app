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
        // TEST: numeric maxLength validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "maxLength": null
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.1', 'ValidatorSchema::check(); return false if the maxLength value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "maxLength": true
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.2', 'ValidatorSchema::check(); return false if the maxLength value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "maxLength": 0
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,true,false,false,false,false);
        TestCheck::assertArray('A.3', 'ValidatorSchema::check(); return false if a string value length is greater than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "maxLength": 1
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,true,true,false,false,false);
        TestCheck::assertArray('A.4', 'ValidatorSchema::check(); return false if a string value length is greater than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "maxLength": 2
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,true,true,true,false,false);
        TestCheck::assertArray('A.5', 'ValidatorSchema::check(); return false if a string value length is greater than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "maxLength": 3
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,true,true,true,true,false);
        TestCheck::assertArray('A.6', 'ValidatorSchema::check(); return false if a string value length is greater than the maximum allowed',  $actual, $expected, $results);



        // TEST: numeric minLength validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "minLength": null
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.1', 'ValidatorSchema::check(); return false if the minLength value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "minLength": true
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.2', 'ValidatorSchema::check(); return false if the minLength value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "minLength": 0
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,true,true,true,true,true);
        TestCheck::assertArray('B.3', 'ValidatorSchema::check(); return false if a string value length is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "minLength": 1
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,true,true,true,true);
        TestCheck::assertArray('B.4', 'ValidatorSchema::check(); return false if a string value length is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "minLength": 2
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,true,true,true);
        TestCheck::assertArray('B.5', 'ValidatorSchema::check(); return false if a string value length is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "minLength": 3
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,false,true,true);
        TestCheck::assertArray('B.6', 'ValidatorSchema::check(); return false if a string value length is less than the minimum allowed',  $actual, $expected, $results);



        // TEST: pattern validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": null
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('C.1', 'ValidatorSchema::check(); return false if the pattern value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": true
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('C.2', 'ValidatorSchema::check(); return false if the pattern value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": ""
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,true,true,true,true,true);
        TestCheck::assertArray('C.3', 'ValidatorSchema::check(); return true for empty pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "/"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,false,false,false);
        TestCheck::assertArray('C.4', 'ValidatorSchema::check(); return false for bad pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "\\\\"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,false,false,false);
        TestCheck::assertArray('C.5', 'ValidatorSchema::check(); return false for bad pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "["
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,false,false,false);
        TestCheck::assertArray('C.6', 'ValidatorSchema::check(); return false for bad pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "{}()[]/-+*?"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,false,false,false);
        TestCheck::assertArray('C.7', 'ValidatorSchema::check(); return false for bad pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "a"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,true,true,true,true);
        TestCheck::assertArray('C.8', 'ValidatorSchema::check(); return false if a string value doesn\'t match the pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "b"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,true,true,true);
        TestCheck::assertArray('C.9', 'ValidatorSchema::check(); return false if a string value doesn\'t match the pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "A"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,false,false,false);
        TestCheck::assertArray('C.10', 'ValidatorSchema::check(); return false if a string value doesn\'t match the pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "ab"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,true,true,true);
        TestCheck::assertArray('C.11', 'ValidatorSchema::check(); return false if a string value doesn\'t match the pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "bc"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,false,true,true);
        TestCheck::assertArray('C.12', 'ValidatorSchema::check(); return false if a string value doesn\'t match the pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "^bc"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,false,false,false);
        TestCheck::assertArray('C.13', 'ValidatorSchema::check(); return false if a string value doesn\'t match the pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "[b-d]*"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,true,true,true,true,true);
        TestCheck::assertArray('C.14', 'ValidatorSchema::check(); return false if a string value doesn\'t match the pattern',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = json_decode('{}');
        $v5 = json_decode('[]');
        $v6 = '';
        $v7 = 'a';
        $v8 = 'ab';
        $v9 = 'abc';
        $v10 = 'abcd';
        $template = <<<EOD
{
    "pattern": "[b-d]+"
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10);
        $expected = array(true,true,true,true,true,false,false,true,true,true);
        TestCheck::assertArray('C.15', 'ValidatorSchema::check(); return false if a string value doesn\'t match the pattern',  $actual, $expected, $results);
    }
}
