<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-09
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
        // note: the following conditional functions are currently supported:
        //     if(boolean val, mixed val, mixed val)
        //     isnull()
        //     iskindof()

        // TODO:
        //     - add additional functions:
        //       ifnull()
        //     - add additional tests:
        //       iskindof() (for english format type)


        // TEST: conditional function: if()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; if() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.2', 'Expression; if() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.3', 'Expression; if() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,1,1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.4', 'Expression; if() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(0,1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.5', 'Expression; if() function; fail if conditional param is not boolean',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if("a",1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.6', 'Expression; if() function; fail if conditional param is not boolean',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,1,"a")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.7', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,"a",1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.8', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,true,"a")');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.9', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,"a",true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.10', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,1,true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.11', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,true,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.12', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,1,1)');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('A.13', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,1,2)');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('A.14', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(false,1,2)');
        $expected = 2;
        \Flexio\Tests\Check::assertNumber('A.15', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,2.1,1.2)');
        $expected = 2.1;
        \Flexio\Tests\Check::assertNumber('A.16', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(false,2.1,1.2)');
        $expected = 1.2;
        \Flexio\Tests\Check::assertNumber('A.17', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,2.1,1)');
        $expected = (double)2.1;
        \Flexio\Tests\Check::assertNumber('A.18', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(false,2.1,1)');
        $expected = (double)1;
        \Flexio\Tests\Check::assertNumber('A.19', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,-2,1.2)');
        $expected = (double)-2;
        \Flexio\Tests\Check::assertNumber('A.20', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(false,-2,1.2)');
        $expected = 1.2;
        \Flexio\Tests\Check::assertNumber('A.21', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,"a","b")');
        $expected = "a";
        \Flexio\Tests\Check::assertString('A.22', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(false,"a","b")');
        $expected = "b";
        \Flexio\Tests\Check::assertString('A.23', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(true,false,true)');
        $expected = false;
        \Flexio\Tests\Check::assertString('A.24', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('if(false,false,true)');
        $expected = true;
        \Flexio\Tests\Check::assertString('A.25', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);



        // TEST: conditional function: isnull()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.1', 'Expression; isnull() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull(null,null)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.2', 'Expression; isnull() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull(null)');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull(false)');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull(true)');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull(0)');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.6', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull(1)');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.7', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull(-1)');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.8', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull(1.1)');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.9', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull("")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.10', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('isnull("a")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.11', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);



        // TEST: conditional function: iskindof(); parameter checks

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.1', 'Expression; iskindof() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof(null)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.2', 'Expression; iskindof() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof(null,null,null)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.3', 'Expression; iskindof() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("",false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.4', 'Expression; iskindof() function; fail if the second parameter is something besides a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("",1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.5', 'Expression; iskindof() function; fail if the second parameter is something besides a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain.com","invalid")');
        $expected = false;
        \Flexio\Tests\Check::assertString('C.6', 'Expression; iskindof() function; return false if the second parameter isn\'t an allowed format',  $actual, $expected, $results);



        // TEST: conditional function: iskindof(); email format

        // note: some email examples from here and here:
        // http://blogs.msdn.com/b/testing123/archive/2009/02/05/email-address-test-cases.aspx

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof(" email@domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.1', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain.com ","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.3', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("firstname.lastname@domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.4', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof(\'"firstnamelastname"@domain.com\',"email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.5', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("firstname-lastname@domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.6', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("firstname+lastname@domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.7', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("firstname_lastname@domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.8', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("_@domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.9', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("name1@domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.10', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("1234567890@domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.11', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@subdomain.domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.12', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@[123.123.123.123]","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.13', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("\"email\"@domain.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.14', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain-one.com","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.15', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain.name","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.16', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain.co.jp","email")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.17', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.18', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email.domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.19', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.20', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.21', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@#.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.22', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@-domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.23', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("@domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.24', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("Joe Smith <email@domain.com>","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.25', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain@domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.26', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof(".email@domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.27', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email.@domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.28', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email..email@domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.29', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("あ@domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.30', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("あa@domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.31', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("aお@domain.com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.32', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain.com (Joe Smith)","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.33', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@111.222.333.44444","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.34', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@domain..com","email")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.35', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxx.com","email")');
        $expected = true; // 254 chars
        \Flexio\Tests\Check::assertBoolean('D.36', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("email@xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxx.com","email")');
        $expected = false; // 254 chars
        \Flexio\Tests\Check::assertBoolean('D.37', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);



        // TEST: conditional function: iskindof(); english format

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("a","english")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.1', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("A","english")');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("á","english")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.3', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("à","english")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.4', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("ä","english")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.5', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("â","english")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.6', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("ã","english")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.7', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("Á","english")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.8', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('iskindof("Ä","english")');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.9', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);
    }
}
