<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-09-19
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
        // note: general expression tests

        // TODO:
        //     - fill out case standardization tests
        //     - fill out padding standardization tests
        //     - fill out common cleaning tests
        //     - add tests for standardizing common formats:
        //       - names
        //       - financial numbers
        //       - phone numbers
        //       - dates



        // TEST: basic expression example

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression("LEFT('ABC',1)");
        $expected = 'A';
        \Flexio\Tests\Check::assertString('A.1', 'Expression; LEFT() function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression("left('ABC',1)");
        $expected = 'A';
        \Flexio\Tests\Check::assertString('A.2', 'Expression; LEFT() function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression("Left('ABC',2)");
        $expected = 'AB';
        \Flexio\Tests\Check::assertString('A.3', 'Expression; LEFT() function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression("LefT('ABC',2)");
        $expected = 'AB';
        \Flexio\Tests\Check::assertString('A.4', 'Expression; LEFT() function',  $actual, $expected, $results);



        // TEST: common case standardization tests

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression("upper(trim(null))");
        $expected = null;
        \Flexio\Tests\Check::assertString('B.1', 'Expression; common case standardization',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = '  company name  ';
        $actual = \Flexio\Tests\Util::evalExpression("upper(trim('$expr'))");
        $expected = 'COMPANY NAME';
        \Flexio\Tests\Check::assertString('B.2', 'Expression; common case standardization',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression("concat(upper(substr((null),1,1)) , lower(substr((null),2)))");
        $expected = null;
        \Flexio\Tests\Check::assertString('B.3', 'Expression; common case standardization',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = 'this is a sentence.';
        $actual = \Flexio\Tests\Util::evalExpression("concat(upper(substr(('$expr'),1,1)) , lower(substr(('$expr'),2)))");
        $expected = 'This is a sentence.';
        \Flexio\Tests\Check::assertString('B.4', 'Expression; common case standardization',  $actual, $expected, $results);



        // TEST: common padding standardization tests

        // BEGIN TEST
        $expr = '  123  ';
        $actual = \Flexio\Tests\Util::evalExpression("lpad(trim('$expr'),10,'0')");
        $expected = '0000000123';
        \Flexio\Tests\Check::assertString('C.1', 'Expression; common padding standardization',  $actual, $expected, $results);



        // TEST: tests specific to ExprEvaluate (testing field values)

        // BEGIN TEST
        $expr = 'concat(f)';
        $row = array('f' => null);
        $structure = array(
            array("name" => "f", "type" => "character")
        );
        $retval = null;
        \Flexio\Base\ExprEvaluate::evaluate($expr, $row, $structure, $retval);
        $actual = $retval;
        $expected = null;
        \Flexio\Tests\Check::assertNull('D.1', 'Expression; ExprEvaluate with row input',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = 'lpad((f),0,\'0\')';
        $row = array('f' => null);
        $structure = array(
            array("name" => "f", "type" => "character")
        );
        $retval = null;
        \Flexio\Base\ExprEvaluate::evaluate($expr, $row, $structure, $retval);
        $actual = $retval;
        $expected = null;
        \Flexio\Tests\Check::assertNull('D.2', 'Expression; ExprEvaluate with row input',  $actual, $expected, $results);



        // TEST: nested conditionals

        // BEGIN TEST
        $expr = 'if(isnull(f),"a",if(isnull(f),"b","c"))';
        $row = array('f' => null);
        $structure = array(
            array("name" => "f", "type" => "character")
        );
        $retval = null;
        \Flexio\Base\ExprEvaluate::evaluate($expr, $row, $structure, $retval);
        $actual = $retval;
        $expected = "a";
        \Flexio\Tests\Check::assertString('E.1', 'Expression; ExprEvaluate with row input',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = 'if(!isnull(f),"a",if(isnull(f),"b","c"))';
        $row = array('f' => null);
        $structure = array(
            array("name" => "f", "type" => "character")
        );
        $retval = null;
        \Flexio\Base\ExprEvaluate::evaluate($expr, $row, $structure, $retval);
        $actual = $retval;
        $expected = "b";
        \Flexio\Tests\Check::assertString('E.2', 'Expression; ExprEvaluate with row input',  $actual, $expected, $results);

        // BEGIN TEST
        $expr = 'if(isnull(f) = false,"a",if(!isnull(f),"b","c"))';
        $row = array('f' => null);
        $structure = array(
            array("name" => "f", "type" => "character")
        );
        $retval = null;
        \Flexio\Base\ExprEvaluate::evaluate($expr, $row, $structure, $retval);
        $actual = $retval;
        $expected = "c";
        \Flexio\Tests\Check::assertString('E.3', 'Expression; ExprEvaluate with row input',  $actual, $expected, $results);
    }
}
