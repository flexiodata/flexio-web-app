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
        $actual = TestUtil::evalExpression('if()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.1', 'Expression; if() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.2', 'Expression; if() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.3', 'Expression; if() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,1,1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.4', 'Expression; if() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(0,1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.5', 'Expression; if() function; fail if conditional param is not boolean',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if("a",1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.6', 'Expression; if() function; fail if conditional param is not boolean',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,1,"a")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.7', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,"a",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.8', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,true,"a")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.9', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,"a",true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.10', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,1,true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.11', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,true,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.12', 'Expression; if() function; fail if output parameters are different types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,1,1)');
        $expected = 1;
        TestCheck::assertNumber('A.13', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,1,2)');
        $expected = 1;
        TestCheck::assertNumber('A.14', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(false,1,2)');
        $expected = 2;
        TestCheck::assertNumber('A.15', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,2.1,1.2)');
        $expected = 2.1;
        TestCheck::assertNumber('A.16', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(false,2.1,1.2)');
        $expected = 1.2;
        TestCheck::assertNumber('A.17', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,2.1,1)');
        $expected = (double)2.1;
        TestCheck::assertNumber('A.18', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(false,2.1,1)');
        $expected = (double)1;
        TestCheck::assertNumber('A.19', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,-2,1.2)');
        $expected = (double)-2;
        TestCheck::assertNumber('A.20', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(false,-2,1.2)');
        $expected = 1.2;
        TestCheck::assertNumber('A.21', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,"a","b")');
        $expected = "a";
        TestCheck::assertString('A.22', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(false,"a","b")');
        $expected = "b";
        TestCheck::assertString('A.23', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(true,false,true)');
        $expected = false;
        TestCheck::assertString('A.24', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('if(false,false,true)');
        $expected = true;
        TestCheck::assertString('A.25', 'Expression; if() function; return first parameter if condition is true and second parameter otherwise',  $actual, $expected, $results);



        // TEST: conditional function: isnull()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.1', 'Expression; isnull() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull(null,null)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.2', 'Expression; isnull() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull(null)');
        $expected = true;
        TestCheck::assertBoolean('B.3', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull(false)');
        $expected = false;
        TestCheck::assertBoolean('B.4', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull(true)');
        $expected = false;
        TestCheck::assertBoolean('B.5', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull(0)');
        $expected = false;
        TestCheck::assertBoolean('B.6', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull(1)');
        $expected = false;
        TestCheck::assertBoolean('B.7', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull(-1)');
        $expected = false;
        TestCheck::assertBoolean('B.8', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull(1.1)');
        $expected = false;
        TestCheck::assertBoolean('B.9', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull("")');
        $expected = false;
        TestCheck::assertBoolean('B.10', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('isnull("a")');
        $expected = false;
        TestCheck::assertBoolean('B.11', 'Expression; isnull() function; return if true if param is null, and false otherwise',  $actual, $expected, $results);



        // TEST: conditional function: iskindof(); parameter checks

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.1', 'Expression; iskindof() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof(null)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.2', 'Expression; iskindof() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof(null,null,null)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.3', 'Expression; iskindof() function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("",false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.4', 'Expression; iskindof() function; fail if the second parameter is something besides a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.5', 'Expression; iskindof() function; fail if the second parameter is something besides a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain.com","invalid")');
        $expected = false;
        TestCheck::assertString('C.6', 'Expression; iskindof() function; return false if the second parameter isn\'t an allowed format',  $actual, $expected, $results);



        // TEST: conditional function: iskindof(); email format

        // note: some email examples from here and here:
        // http://blogs.msdn.com/b/testing123/archive/2009/02/05/email-address-test-cases.aspx

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof(" email@domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.1', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain.com ","email")');
        $expected = false;
        TestCheck::assertBoolean('D.2', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.3', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("firstname.lastname@domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.4', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof(\'"firstnamelastname"@domain.com\',"email")');
        $expected = true;
        TestCheck::assertBoolean('D.5', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("firstname-lastname@domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.6', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("firstname+lastname@domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.7', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("firstname_lastname@domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.8', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("_@domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.9', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("name1@domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.10', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("1234567890@domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.11', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@subdomain.domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.12', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@[123.123.123.123]","email")');
        $expected = true;
        TestCheck::assertBoolean('D.13', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("\"email\"@domain.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.14', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain-one.com","email")');
        $expected = true;
        TestCheck::assertBoolean('D.15', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain.name","email")');
        $expected = true;
        TestCheck::assertBoolean('D.16', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain.co.jp","email")');
        $expected = true;
        TestCheck::assertBoolean('D.17', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email","email")');
        $expected = false;
        TestCheck::assertBoolean('D.18', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email.domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.19', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@","email")');
        $expected = false;
        TestCheck::assertBoolean('D.20', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain","email")');
        $expected = false;
        TestCheck::assertBoolean('D.21', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@#.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.22', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@-domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.23', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("@domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.24', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("Joe Smith <email@domain.com>","email")');
        $expected = false;
        TestCheck::assertBoolean('D.25', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain@domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.26', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof(".email@domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.27', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email.@domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.28', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email..email@domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.29', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("あ@domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.30', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("あa@domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.31', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("aお@domain.com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.32', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain.com (Joe Smith)","email")');
        $expected = false;
        TestCheck::assertBoolean('D.33', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@111.222.333.44444","email")');
        $expected = false;
        TestCheck::assertBoolean('D.34', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@domain..com","email")');
        $expected = false;
        TestCheck::assertBoolean('D.35', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxx.com","email")');
        $expected = true; // 254 chars
        TestCheck::assertBoolean('D.36', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("email@xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxx.com","email")');
        $expected = false; // 254 chars
        TestCheck::assertBoolean('D.37', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);



        // TEST: conditional function: iskindof(); english format

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("a","english")');
        $expected = true;
        TestCheck::assertBoolean('D.1', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("A","english")');
        $expected = true;
        TestCheck::assertBoolean('D.2', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("á","english")');
        $expected = false;
        TestCheck::assertBoolean('D.3', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("à","english")');
        $expected = false;
        TestCheck::assertBoolean('D.4', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("ä","english")');
        $expected = false;
        TestCheck::assertBoolean('D.5', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("â","english")');
        $expected = false;
        TestCheck::assertBoolean('D.6', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("ã","english")');
        $expected = false;
        TestCheck::assertBoolean('D.7', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("Á","english")');
        $expected = false;
        TestCheck::assertBoolean('D.8', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('iskindof("Ä","english")');
        $expected = false;
        TestCheck::assertBoolean('D.9', 'Expression; iskindof() function; return true if the first parameter is a type of the kind specified in the second parameter; false otherwise',  $actual, $expected, $results);
    }
}
