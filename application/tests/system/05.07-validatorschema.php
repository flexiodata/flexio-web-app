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
        // TEST: array maxItems validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "maxItems": null
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.1', 'ValidatorSchema::check(); return false if the maxItems value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "maxItems": true
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.2', 'ValidatorSchema::check(); return false if the maxItems value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "maxItems": -1
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.3', 'ValidatorSchema::check(); return false if the maxItems value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "maxItems": 0
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(true,true,true,false,false,false,false,true);
        TestCheck::assertArray('A.4', 'ValidatorSchema::check(); return false if an array size is greater than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "maxItems": 1
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(true,true,true,true,false,false,false,true);
        TestCheck::assertArray('A.5', 'ValidatorSchema::check(); return false if an array size is greater than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "maxItems": 2
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(true,true,true,true,true,false,false,true);
        TestCheck::assertArray('A.6', 'ValidatorSchema::check(); return false if an array size is greater than the maximum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "maxItems": 3
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(true,true,true,true,true,true,false,true);
        TestCheck::assertArray('A.7', 'ValidatorSchema::check(); return false if an array size is greater than the maximum allowed',  $actual, $expected, $results);



        // TEST: array minItems validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "minItems": null
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.1', 'ValidatorSchema::check(); return false if the minItems value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "minItems": true
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.2', 'ValidatorSchema::check(); return false if the minItems value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "minItems": -1
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(false,false,false,false,false,false,false,false);
        TestCheck::assertArray('B.3', 'ValidatorSchema::check(); return false if the minItems value is invalid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "minItems": 0
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(true,true,true,true,true,true,true,true);
        TestCheck::assertArray('B.4', 'ValidatorSchema::check(); return false if an array size is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "minItems": 1
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(true,true,false,true,true,true,true,true);
        TestCheck::assertArray('B.5', 'ValidatorSchema::check(); return false if an array size is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "minItems": 2
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(true,true,false,false,true,true,true,true);
        TestCheck::assertArray('B.6', 'ValidatorSchema::check(); return false if an array size is less than the minimum allowed',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = json_decode('[]');
        $v4 = json_decode('["a"]');
        $v5 = json_decode('["a","b"]');
        $v6 = json_decode('["a","b","c"]');
        $v7 = json_decode('["a","b","c","d"]');
        $v8 = json_decode('{"a":1,"b":2}');
        $template = <<<EOD
{
    "minItems": 3
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
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8);
        $expected = array(true,true,false,false,false,true,true,true);
        TestCheck::assertArray('B.7', 'ValidatorSchema::check(); return false if an array size is less than the minimum allowed',  $actual, $expected, $results);
    }
}
