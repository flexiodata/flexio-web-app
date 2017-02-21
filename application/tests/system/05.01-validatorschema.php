<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-15
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TODO: add more schema validation tests


        // TEST: basic tests for schema validation

        // BEGIN TEST
        $template = null;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.1', '\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.2', '\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = json_decode('{}');
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.3', '\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.4', '\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "value":
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.5', '\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minimum": false
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.6', '\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "maximum": true
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.7', '\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "maximum": 1
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.8', '\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "multipleOf": true
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.9', '\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "multipleOf": 0
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.10', '\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "multipleOf": 1
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.11', '\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": {}
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.12', '\ValidatorSchema::check(); flag an error if the schema \'required\' parameter isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": []
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.13', '\ValidatorSchema::check(); flag an error if the schema \'required\' parameter doesn\'t have any elements',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": ["a",false]
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.14', '\ValidatorSchema::check(); flag an error if the schema \'required\' parameter has any non-string elements',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": ["a","b","c","a"]
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.15', '\ValidatorSchema::check(); flag an error if the schema \'required\' parameter has any duplicate values',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": ["a","b","c"]
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.16', '\ValidatorSchema::check(); flag an error if the schema \'required\' parameter has any duplicate values',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minItems": -1
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.17', '\ValidatorSchema::check(); flag an error if the schema \'minItems\' isn\'t an integer greater than or equal to zero',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minItems": 0
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.18', '\ValidatorSchema::check(); flag an error if the schema \'minItems\' isn\'t an integer greater than or equal to zero',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minItems": 1
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        TestCheck::assertBoolean('A.19', '\ValidatorSchema::check(); flag an error if the schema \'minItems\' isn\'t an integer greater than or equal to zero',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minItems": 1.1
}
EOD;
        $actual = \ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        TestCheck::assertBoolean('A.20', '\ValidatorSchema::check(); flag an error if the schema \'minItems\' isn\'t an integer greater than or equal to zero',  $actual, $expected, $results);



        // TEST: test the title parameter

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "title": null
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        TestCheck::assertArray('B.1', '\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "title": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        TestCheck::assertArray('B.2', '\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "title": {}
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        TestCheck::assertArray('B.3', '\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "title": ""
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(true,true,true,true,true,true,true);
        TestCheck::assertArray('B.4', '\ValidatorSchema::check(); return true as long as the title parameter is a string',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "title": "Test Title"
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(true,true,true,true,true,true,true);
        TestCheck::assertArray('B.5', '\ValidatorSchema::check(); return true as long as the title parameter is a string',  $actual, $expected, $results);



        // TEST: test the description parameter

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "description": null
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        TestCheck::assertArray('C.1', '\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "description": true
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        TestCheck::assertArray('C.2', '\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "description": {}
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        TestCheck::assertArray('C.3', '\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "description": ""
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(true,true,true,true,true,true,true);
        TestCheck::assertArray('C.4', '\ValidatorSchema::check(); return true as long as the title parameter is a string',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = false;
        $v3 = 0;
        $v4 = 1.1;
        $v5 = '';
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $template = <<<EOD
{
    "description": "Test Description"
}
EOD;
        $r1 = \ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(true,true,true,true,true,true,true);
        TestCheck::assertArray('C.5', '\ValidatorSchema::check(); return true as long as the title parameter is a string',  $actual, $expected, $results);
    }
}
