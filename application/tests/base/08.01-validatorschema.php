<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-15
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
        // TODO: add more schema validation tests


        // TEST: basic tests for schema validation

        // BEGIN TEST
        $template = null;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = json_decode('{}');
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "value":
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema isn\'t a valid object or object string representation',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minimum": false
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "maximum": true
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "maximum": 1
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "multipleOf": true
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "multipleOf": 0
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.10', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "multipleOf": 1
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.11', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema has an invalid parameter',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": {}
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.12', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema \'required\' parameter isn\'t an array',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": []
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.13', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema \'required\' parameter doesn\'t have any elements',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": ["a",false]
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.14', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema \'required\' parameter has any non-string elements',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": ["a","b","c","a"]
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.15', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema \'required\' parameter has any duplicate values',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "required": ["a","b","c"]
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.16', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema \'required\' parameter has any duplicate values',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minItems": -1
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.17', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema \'minItems\' isn\'t an integer greater than or equal to zero',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minItems": 0
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.18', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema \'minItems\' isn\'t an integer greater than or equal to zero',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minItems": 1
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.19', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema \'minItems\' isn\'t an integer greater than or equal to zero',  $actual, $expected, $results);

        // BEGIN TEST
        $template = <<<EOD
{
    "minItems": 1.1
}
EOD;
        $actual = \Flexio\Base\ValidatorSchema::checkSchema($template)->hasErrors();
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.20', '\Flexio\Base\ValidatorSchema::check(); flag an error if the schema \'minItems\' isn\'t an integer greater than or equal to zero',  $actual, $expected, $results);



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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('B.1', '\Flexio\Base\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('B.2', '\Flexio\Base\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('B.3', '\Flexio\Base\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(true,true,true,true,true,true,true);
        \Flexio\Tests\Check::assertArray('B.4', '\Flexio\Base\ValidatorSchema::check(); return true as long as the title parameter is a string',  $actual, $expected, $results);

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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(true,true,true,true,true,true,true);
        \Flexio\Tests\Check::assertArray('B.5', '\Flexio\Base\ValidatorSchema::check(); return true as long as the title parameter is a string',  $actual, $expected, $results);



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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('C.1', '\Flexio\Base\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('C.2', '\Flexio\Base\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(false,false,false,false,false,false,false);
        \Flexio\Tests\Check::assertArray('C.3', '\Flexio\Base\ValidatorSchema::check(); return false if the title parameter isn\'t a string',  $actual, $expected, $results);

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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(true,true,true,true,true,true,true);
        \Flexio\Tests\Check::assertArray('C.4', '\Flexio\Base\ValidatorSchema::check(); return true as long as the title parameter is a string',  $actual, $expected, $results);

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
        $r1 = \Flexio\Base\ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = \Flexio\Base\ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = \Flexio\Base\ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = \Flexio\Base\ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = \Flexio\Base\ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = \Flexio\Base\ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = \Flexio\Base\ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7);
        $expected = array(true,true,true,true,true,true,true);
        \Flexio\Tests\Check::assertArray('C.5', '\Flexio\Base\ValidatorSchema::check(); return true as long as the title parameter is a string',  $actual, $expected, $results);
    }
}
