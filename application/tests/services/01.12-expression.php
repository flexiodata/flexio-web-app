<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, InE.  All rights reserveF.
 *
 * Project:  Flex.io App
 * Author:   Aaron O. Williams
 * Created:  2016-02-09
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // note: the following string functions are currently supported:
        //     ascii(mixed val)
        //     concat(mixed val, ...)
        //     contains(mixed, text search_str)
        //     initcap(mixed val)
        //     left(mixed val, integer count)
        //     length(mixed val)
        //     lower(mixed val)
        //     lpad(mixed val, integer length [, text fill_chars])
        //     ltrim(mixed val [, text chars])
        //     md5(mixed val)
        //     regexp_replace(mixed val, text pattern_str, text replace_str [, text flags])  (flags are i for case-insensitive match and g for global replace)
        //     replace(mixed val, text from_str, text to_str)
        //     reverse(mixed val)
        //     right(mixed val, integer count)
        //     rpad(mixed, integer length [, text fill_chars])
        //     rtrim(mixed val [, text trim_chars])
        //     strpart(mixed, text search_str, integer index)
        //     strpos(mixed, text search_str)
        //     substr(mixed, integer from [, integer count])
        //     trim(mixed val [, text chars])
        //     upper(mixed val)

        // TODO:
        //     - implicit type conversion supported for numeric and boolean types;
        //       add implicit type conversion for date and datetime


        // TEST: text function: ascii()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.1', 'Expression; ascii() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("Apples","Oranges")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.2', 'Expression; ascii() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("")');
        $expected = 0;
        TestCheck::assertString('A.3', 'Expression; ascii() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("A")');
        $expected = 65;
        TestCheck::assertString('A.4', 'Expression; ascii() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("a")');
        $expected = 97;
        TestCheck::assertString('A.5', 'Expression; ascii() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("Apples")');
        $expected = 65;
        TestCheck::assertString('A.6', 'Expression; ascii() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii(1)');
        $expected = 49;
        TestCheck::assertString('A.7', 'Expression; ascii() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("á")');
        $expected = 225;
        TestCheck::assertString('A.8', 'Expression; ascii() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("à")');
        $expected = 224;
        TestCheck::assertString('A.9', 'Expression; ascii() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("ä")');
        $expected = 228;
        TestCheck::assertString('A.10', 'Expression; ascii() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("â")');
        $expected = 226;
        TestCheck::assertString('A.11', 'Expression; ascii() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ascii("ã")');
        $expected = 227;
        TestCheck::assertString('A.12', 'Expression; ascii() text function',  $actual, $expected, $results);



        // TEST: text function: concat()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.1', 'Expression; concat() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a")');
        $expected = "a";
        TestCheck::assertString('B.2', 'Expression; concat() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a","b")');
        $expected = "ab";
        TestCheck::assertString('B.3', 'Expression; concat() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("A","b")');
        $expected = "Ab";
        TestCheck::assertString('B.4', 'Expression; concat() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a","B")');
        $expected = "aB";
        TestCheck::assertString('B.5', 'Expression; concat() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(" A "," B ")');
        $expected = " A  B ";
        TestCheck::assertString('B.6', 'Expression; concat() text function; make sure concat preserves whitespace in string literals',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("abc","def")');
        $expected = "abcdef";
        TestCheck::assertString('B.7', 'Expression; concat() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z")');
        $expected = "abcdefghijklmnopqrstuvwxyz";
        TestCheck::assertString('B.8', 'Expression; concat() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(1)');
        $expected = "1";
        TestCheck::assertString('B.9', 'Expression; concat() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(1,2)');
        $expected = "12";
        TestCheck::assertString('B.10', 'Expression; concat() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(1.1)');
        $expected = "1.1";
        TestCheck::assertString('B.11', 'Expression; concat() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(1.1,-2.1)');
        $expected = "1.1-2.1";
        TestCheck::assertString('B.12', 'Expression; concat() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(true)');
        $expected = "true";
        TestCheck::assertString('B.13', 'Expression; concat() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(true,false)');
        $expected = "truefalse";
        TestCheck::assertString('B.14', 'Expression; concat() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(null)');
        $expected = null;
        TestCheck::assertNull('B.15', 'Expression; concat() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(null,null)');
        $expected = null;
        TestCheck::assertNull('B.16', 'Expression; concat() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a","")');
        $expected = "a";
        TestCheck::assertString('B.17', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a",0)');
        $expected = "a0";
        TestCheck::assertString('B.18', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a",-1.1)');
        $expected = "a-1.1";
        TestCheck::assertString('B.19', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a",true)');
        $expected = "atrue";
        TestCheck::assertString('B.20', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a",null)');
        $expected = null;
        TestCheck::assertNull('B.21', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(-1,"a")');
        $expected = "-1a";
        TestCheck::assertString('B.22', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(-1,0)');
        $expected = "-10";
        TestCheck::assertString('B.23', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(-1,-1.1)');
        $expected = "-1-1.1";
        TestCheck::assertString('B.24', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(-1,true)');
        $expected = "-1true";
        TestCheck::assertString('B.25', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(-1,null)');
        $expected = null;
        TestCheck::assertNull('B.26', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(1.2,"a")');
        $expected = "1.2a";
        TestCheck::assertString('B.27', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(1.2,0)');
        $expected = "1.20";
        TestCheck::assertString('B.28', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(1.2,-1.1)');
        $expected = "1.2-1.1";
        TestCheck::assertString('B.29', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(1.2,true)');
        $expected = "1.2true";
        TestCheck::assertString('B.30', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(1.2,null)');
        $expected = null;
        TestCheck::assertNull('B.31', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(false,"a")');
        $expected = "falsea";
        TestCheck::assertString('B.32', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(false,0)');
        $expected = "false0";
        TestCheck::assertString('B.33', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(false,-1.1)');
        $expected = "false-1.1";
        TestCheck::assertString('B.34', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(false,true)');
        $expected = "falsetrue";
        TestCheck::assertString('B.35', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(false,null)');
        $expected = null;
        TestCheck::assertNull('B.36', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(null,"a")');
        $expected = null;
        TestCheck::assertNull('B.37', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(null,0)');
        $expected = null;
        TestCheck::assertNull('B.38', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(null,-1.1)');
        $expected = null;
        TestCheck::assertNull('B.39', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(null,true)');
        $expected = null;
        TestCheck::assertNull('B.40', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat(null,null)');
        $expected = null;
        TestCheck::assertNull('B.41', 'Expression; concat() text function; mixed types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('concat("a","á","à","ä","â","ã","Á","Ä")');
        $expected = "aáàäâãÁÄ";
        TestCheck::assertString('B.42', 'Expression; concat() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: contains()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.1', 'Expression; contains() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("Apples","",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.2', 'Expression; contains() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("Apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.3', 'Expression; contains() text function; fail if the search string parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("","")'); // equivalant to strpos("","") returning 1
        $expected = true;
        TestCheck::assertBoolean('C.4', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("a","")'); // equivalant to strpos("a","") returning 1
        $expected = true;
        TestCheck::assertBoolean('C.5', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("","a")');
        $expected = false;
        TestCheck::assertBoolean('C.6', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("a","a")');
        $expected = true;
        TestCheck::assertBoolean('C.7', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("A","a")');
        $expected = false;
        TestCheck::assertBoolean('C.8', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("a","A")');
        $expected = false;
        TestCheck::assertBoolean('C.9', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("a","ab")');
        $expected = false;
        TestCheck::assertBoolean('C.10', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("ab","a")');
        $expected = true;
        TestCheck::assertBoolean('C.11', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("ab","b")');
        $expected = true;
        TestCheck::assertBoolean('C.12', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("Apples and Oranges","Apples")');
        $expected = true;
        TestCheck::assertBoolean('C.13', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("Apples and Oranges","and")');
        $expected = true;
        TestCheck::assertBoolean('C.14', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("Apples and Oranges","Oranges")');
        $expected = true;
        TestCheck::assertBoolean('C.15', 'Expression; contains() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains(123,"3")');
        $expected = true;
        TestCheck::assertBoolean('C.16', 'Expression; contains() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains(-123,"12")');
        $expected = true;
        TestCheck::assertBoolean('C.17', 'Expression; contains() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains(-1.23,".")');
        $expected = true;
        TestCheck::assertBoolean('C.18', 'Expression; contains() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains(true,"t")');
        $expected = true;
        TestCheck::assertBoolean('C.19', 'Expression; contains() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains(false,"e")');
        $expected = true;
        TestCheck::assertBoolean('C.20', 'Expression; contains() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains(null,"")');
        $expected = null;
        TestCheck::assertNull('C.21', 'Expression; contains() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("aáàäâãÁÄ", "a")');
        $expected = true;
        TestCheck::assertBoolean('C.22', 'Expression; contains() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("aáàäâãÁÄ", "ä")');
        $expected = true;
        TestCheck::assertBoolean('C.23', 'Expression; contains() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("aáàäâãÁÄ", "Ä")');
        $expected = true;
        TestCheck::assertBoolean('C.24', 'Expression; contains() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("aáàäâãÁÄ", "áÄ")');
        $expected = false;
        TestCheck::assertBoolean('C.25', 'Expression; contains() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('contains("aáàäâãÁÄ", "ÁÄ")');
        $expected = true;
        TestCheck::assertBoolean('C.26', 'Expression; contains() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: initcap()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.1', 'Expression; initcap() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap("apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.2', 'Expression; initcap() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap("")');
        $expected = '';
        TestCheck::assertString('D.3', 'Expression; initcap() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap("apples")');
        $expected = 'Apples';
        TestCheck::assertString('D.4', 'Expression; initcap() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap("oranges")');
        $expected = 'Oranges';
        TestCheck::assertString('D.5', 'Expression; initcap() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap("apples and oranges")');
        $expected = 'Apples And Oranges';
        TestCheck::assertString('D.6', 'Expression; initcap() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap("text separated by delimiters: a b,c, d;e.f_g-h+i/j(k)l!m@n#o$p%q^r&s*t")');
        $expected = 'Text Separated By Delimiters: A B,C, D;E.F_G-H+I/J(K)L!M@N#O$P%Q^R&S*T';
        TestCheck::assertString('D.7', 'Expression; initcap() text function with delimiters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap(123)');
        $expected = '123';
        TestCheck::assertString('D.8', 'Expression; initcap() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap(-123)');
        $expected = '-123';
        TestCheck::assertString('D.9', 'Expression; initcap() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap(-1.23)');
        $expected = '-1.23';
        TestCheck::assertString('D.10', 'Expression; initcap() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap(true)');
        $expected = 'True';
        TestCheck::assertString('D.11', 'Expression; initcap() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap(false)');
        $expected = 'False';
        TestCheck::assertString('D.12', 'Expression; initcap() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap(null)');
        $expected = null;
        TestCheck::assertNull('D.13', 'Expression; initcap() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap("áÁ")');
        $expected = "Áá";
        TestCheck::assertString('D.14', 'Expression; initcap() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap("Áá")');
        $expected = "Áá";
        TestCheck::assertString('D.15', 'Expression; initcap() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('initcap("international characters: â, ê,î,ô,û,ð and Â, Ê,Î,Ô,Û,Ð (are there others?)")');
        $expected = "International Characters: Â, Ê,Î,Ô,Û,Ð And Â, Ê,Î,Ô,Û,Ð (Are There Others?)";
        TestCheck::assertString('D.16', 'Expression; initcap() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: left()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.1', 'Expression; left() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("Apples and Oranges")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.2', 'Expression; left() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("Apples and Oranges",1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.3', 'Expression; left() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("Apples and Oranges","1")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.4', 'Expression; left() text function; fail if the length parameter isn\'t an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("Apples and Oranges",1.1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.5', 'Expression; left() text function; fail if the length parameter isn\'t an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("",1)');
        $expected = '';
        TestCheck::assertString('E.6', 'Expression; left() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("Apples and Oranges",5)');
        $expected = 'Apple';
        TestCheck::assertString('E.7', 'Expression; left() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("Apples and Oranges",10)');
        $expected = 'Apples and';
        TestCheck::assertString('E.8', 'Expression; left() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("Apples and Oranges",20)');
        $expected = 'Apples and Oranges';
        TestCheck::assertString('E.9', 'Expression; left() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("Apples and Oranges",0)');
        $expected = '';
        TestCheck::assertString('E.10', 'Expression; left() text function; if a zero length param is specified, return the empty string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("Apples and Oranges",-8)');
        $expected = 'Apples and';
        TestCheck::assertString('E.11', 'Expression; left() text function; if a negative length param is specified, remove the specified characters from the end of the string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left(123,1)');
        $expected = '1';
        TestCheck::assertString('E.12', 'Expression; left() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left(-123,2)');
        $expected = '-1';
        TestCheck::assertString('E.13', 'Expression; left() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left(-1.23,4)');
        $expected = '-1.2';
        TestCheck::assertString('E.14', 'Expression; left() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left(true,1)');
        $expected = 't';
        TestCheck::assertString('E.15', 'Expression; left() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left(false,2)');
        $expected = 'fa';
        TestCheck::assertString('E.16', 'Expression; left() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left(null,2)');
        $expected = null;
        TestCheck::assertNull('E.17', 'Expression; left() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("aáàäâãÁÄ",1)');
        $expected = "a";
        TestCheck::assertString('E.18', 'Expression; left() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("aáàäâãÁÄ",2)');
        $expected = "aá";
        TestCheck::assertString('E.19', 'Expression; left() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('left("aáàäâãÁÄ",10)');
        $expected = "aáàäâãÁÄ";
        TestCheck::assertString('E.20', 'Expression; left() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: length()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('F.1', 'Expression; length() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length("Apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('F.2', 'Expression; length() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length("")');
        $expected = 0;
        TestCheck::assertNumber('F.3', 'Expression; length() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length("Apples")');
        $expected = 6;
        TestCheck::assertNumber('F.4', 'Expression; length() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length("Oranges")');
        $expected = 7;
        TestCheck::assertNumber('F.5', 'Expression; length() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length("Apples and Oranges")');
        $expected = 18;
        TestCheck::assertNumber('F.6', 'Expression; length() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length(123)');
        $expected = 3;
        TestCheck::assertNumber('F.7', 'Expression; length() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length(-123)');
        $expected = 4;
        TestCheck::assertNumber('F.8', 'Expression; length() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length(-1.23)');
        $expected = 5;
        TestCheck::assertNumber('F.9', 'Expression; length() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length(true)');
        $expected = 4;
        TestCheck::assertNumber('F.10', 'Expression; length() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length(false)');
        $expected = 5;
        TestCheck::assertNumber('F.11', 'Expression; length() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length(null)');
        $expected = null;
        TestCheck::assertNull('F.12', 'Expression; length() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length("á")');
        $expected = 1;
        TestCheck::assertNumber('F.13', 'Expression; length() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('length("aáàäâãÁÄ")');
        $expected = 8;
        TestCheck::assertNumber('F.14', 'Expression; length() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: lower()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('G.1', 'Expression; lower() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower("Apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('G.2', 'Expression; lower() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower("")');
        $expected = '';
        TestCheck::assertString('G.3', 'Expression; lower() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower("Apples")');
        $expected = 'apples';
        TestCheck::assertString('G.4', 'Expression; lower() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower("Oranges")');
        $expected = 'oranges';
        TestCheck::assertString('G.5', 'Expression; lower() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower("Apples and Oranges")');
        $expected = 'apples and oranges';
        TestCheck::assertString('G.6', 'Expression; lower() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower(123)');
        $expected = '123';
        TestCheck::assertString('G.7', 'Expression; lower() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower(-123)');
        $expected = '-123';
        TestCheck::assertString('G.8', 'Expression; lower() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower(-1.23)');
        $expected = '-1.23';
        TestCheck::assertString('G.9', 'Expression; lower() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower(true)');
        $expected = 'true';
        TestCheck::assertString('G.10', 'Expression; lower() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower(false)');
        $expected = 'false';
        TestCheck::assertString('G.11', 'Expression; lower() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower(null)');
        $expected = null;
        TestCheck::assertNull('G.12', 'Expression; lower() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower("áÁ")');
        $expected = "áá";
        TestCheck::assertString('G.13', 'Expression; lower() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower("Áá")');
        $expected = "áá";
        TestCheck::assertString('G.14', 'Expression; lower() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lower("aáàäâãÁÄ")');
        $expected = "aáàäâãáä";
        TestCheck::assertString('G.15', 'Expression; lower() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: lpad()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.1', 'Expression; lpad() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("Apples",1,"0",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.2', 'Expression; lpad() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("Apples",1.1,"0")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.3', 'Expression; lpad() text function; fail if the length parameter is not an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("Apples",1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.4', 'Expression; lpad() text function; fail if the pad parameter is not a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("",1)');
        $expected = ' ';
        TestCheck::assertString('H.5', 'Expression; lpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("Apples",1)');
        $expected = 'A';
        TestCheck::assertString('H.6', 'Expression; lpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("Oranges",10)');
        $expected = '   Oranges';
        TestCheck::assertString('H.7', 'Expression; lpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("Apples and Oranges",25,"-")');
        $expected = '-------Apples and Oranges';
        TestCheck::assertString('H.8', 'Expression; lpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("",0)');
        $expected = '';
        TestCheck::assertString('H.9', 'Expression; lpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("",-1)');
        $expected = '';
        TestCheck::assertString('H.10', 'Expression; lpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("123",8,"01")');
        $expected = '01010123';
        TestCheck::assertString('H.11', 'Expression; lpad() text function; multiple pad characters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad(123,6,"0")');
        $expected = '000123';
        TestCheck::assertString('H.12', 'Expression; lpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad(-123,6,"0")');
        $expected = '00-123';
        TestCheck::assertString('H.13', 'Expression; lpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad(-1.23,6,"0")');
        $expected = '0-1.23';
        TestCheck::assertString('H.14', 'Expression; lpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad(true,6,"0")');
        $expected = '00true';
        TestCheck::assertString('H.15', 'Expression; lpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad(false,6,"0")');
        $expected = '0false';
        TestCheck::assertString('H.16', 'Expression; lpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad(null,6,"0")');
        $expected = null;
        TestCheck::assertNull('H.17', 'Expression; lpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("á",2,"Á")');
        $expected = "Áá";
        TestCheck::assertString('H.18', 'Expression; lpad() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("Á",2,"á")');
        $expected = "áÁ";
        TestCheck::assertString('H.19', 'Expression; lpad() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('lpad("aá",10,"àäâãÁÄ")');
        $expected = "àäâãÁÄàäaá";
        TestCheck::assertString('H.20', 'Expression; lpad() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: ltrim()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.1', 'Expression; ltrim() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim("Apples","",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.2', 'Expression; ltrim() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim("Apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.3', 'Expression; ltrim() text function; fail if the characters-to-trim parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim("")');
        $expected = '';
        TestCheck::assertString('I.4', 'Expression; ltrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim("Apples")');
        $expected = 'Apples';
        TestCheck::assertString('I.5', 'Expression; ltrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim("   Oranges")');
        $expected = 'Oranges';
        TestCheck::assertString('I.6', 'Expression; ltrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim("-------Apples and Oranges-------","-")');
        $expected = 'Apples and Oranges-------';
        TestCheck::assertString('I.7', 'Expression; ltrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim("-------  Apples and Oranges  -------","- Apsle")');
        $expected = 'and Oranges  -------';
        TestCheck::assertString('I.8', 'Expression; ltrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim(123,"1")');
        $expected = '23';
        TestCheck::assertString('I.9', 'Expression; ltrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim(-123,"-")');
        $expected = '123';
        TestCheck::assertString('I.10', 'Expression; ltrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim(-1.23,"-+0123456789")');
        $expected = '.23';
        TestCheck::assertString('I.11', 'Expression; ltrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim(true,"t")');
        $expected = 'rue';
        TestCheck::assertString('I.12', 'Expression; ltrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim(false,"f")');
        $expected = 'alse';
        TestCheck::assertString('I.13', 'Expression; ltrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim(null)');
        $expected = null;
        TestCheck::assertNull('I.14', 'Expression; ltrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim("aáàäâãÁÄ","ÁÄaáàäâã")');
        $expected = "";
        TestCheck::assertString('I.15', 'Expression; ltrim() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ltrim("aáàäâãÁÄ","ãâäàáa")');
        $expected = "ÁÄ";
        TestCheck::assertString('I.16', 'Expression; ltrim() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: md5()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('J.1', 'Expression; md5() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("","")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('J.2', 'Expression; md5() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("")');
        $expected = 'd41d8cd98f00b204e9800998ecf8427e';
        TestCheck::assertString('J.3', 'Expression; md5() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("a")');
        $expected = '0cc175b9c0f1b6a831c399e269772661';
        TestCheck::assertString('J.4', 'Expression; md5() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("A")');
        $expected = '7fc56270e7a70fa81a5935b72eacbe29';
        TestCheck::assertString('J.5', 'Expression; md5() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("a ")');
        $expected = '99020cb24bd13238d907c65cc2b57c03';
        TestCheck::assertString('J.6', 'Expression; md5() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5(" a")');
        $expected = '952988da97fbd8f2ea65990c03eac425';
        TestCheck::assertString('J.7', 'Expression; md5() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("ab")');
        $expected = '187ef4436122d1cc2f40dc2b92f0eba0';
        TestCheck::assertString('J.8', 'Expression; md5() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5(123)');
        $expected = '202cb962ac59075b964b07152d234b70';
        TestCheck::assertString('J.9', 'Expression; md5() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5(-123)');
        $expected = 'ca044838c4145364b91f5041e846f483';
        TestCheck::assertString('J.10', 'Expression; md5() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5(-1.23)');
        $expected = '0d0846dad241687326d58a08621a95e0';
        TestCheck::assertString('J.11', 'Expression; md5() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5(true)');
        $expected = 'b326b5062b2f0e69046810717534cb09';
        TestCheck::assertString('J.12', 'Expression; md5() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5(false)');
        $expected = '68934a3e9455fa72420237eb05902327';
        TestCheck::assertString('J.13', 'Expression; md5() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5(null)');
        $expected = null;
        TestCheck::assertNull('J.14', 'Expression; md5() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("ä")');
        $expected = "8419b71c87a225a2c70b50486fbee545";
        TestCheck::assertString('J.15', 'Expression; md5() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("Ä")');
        $expected = "b66491b03046f0846fe4206bc6a0f3c0";
        TestCheck::assertString('J.16', 'Expression; md5() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("aáàäâã")');
        $expected = "e0952b76af57632d805cd32f2ddb0cf8";
        TestCheck::assertString('J.17', 'Expression; md5() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('md5("aáàÄâã")');
        $expected = "a43eca87b4eb12b4efcbc04b4c9e120f";
        TestCheck::assertString('J.18', 'Expression; md5() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: regexp_replace()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('K.1', 'Expression; regexp_replace() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Apples","")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('K.2', 'Expression; regexp_replace() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Apples","","","","")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('K.3', 'Expression; regexp_replace() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Apples","+","")');
        $expected = null;
        TestCheck::assertNull('K.4', 'Expression; regexp_replace() text function; return null if a bad regex is passed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Apples","\","")');
        $expected = null;
        TestCheck::assertNull('K.5', 'Expression; regexp_replace() text function; return null if a bad regex is passed',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Apples","A","a")');
        $expected = 'apples';
        TestCheck::assertString('K.6', 'Expression; regexp_replace() text function; fail if the search string parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Apples","s","")');
        $expected = 'Apple';
        TestCheck::assertString('K.7', 'Expression; regexp_replace() text function; fail if the replace string parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("","","")');
        $expected = '';
        TestCheck::assertString('K.8', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("a","","")');
        $expected = 'a';
        TestCheck::assertString('K.9', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("","a","")');
        $expected = '';
        TestCheck::assertString('K.10', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("","","a")');
        $expected = 'a';
        TestCheck::assertString('K.11', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("a","","a")');
        $expected = 'aa';
        TestCheck::assertString('K.12', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("a","a","")');
        $expected = '';
        TestCheck::assertString('K.13', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("","a","a")');
        $expected = '';
        TestCheck::assertString('K.14', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("a","a","a")');
        $expected = 'a';
        TestCheck::assertString('K.15', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ab","a","")');
        $expected = 'b';
        TestCheck::assertString('K.16', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Apples and Oranges","Apples","Oranges")');
        $expected = 'Oranges and Oranges';
        TestCheck::assertString('K.17', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Apples and Oranges","and","or")');
        $expected = 'Apples or Oranges';
        TestCheck::assertString('K.18', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Apples and Oranges","Oranges","Apples")');
        $expected = 'Apples and Apples';
        TestCheck::assertString('K.19', 'Expression; regexp_replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace(123,"3","0")');
        $expected = '120';
        TestCheck::assertString('K.20', 'Expression; regexp_replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace(-123,"-","")');
        $expected = '123';
        TestCheck::assertString('K.21', 'Expression; regexp_replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace(-1.23,".",",")');
        $expected = ',1.23';
        TestCheck::assertString('K.22', 'Expression; regexp_replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace(true,"true","null")');
        $expected = 'null';
        TestCheck::assertString('K.23', 'Expression; regexp_replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace(false,"false","F")');
        $expected = 'F';
        TestCheck::assertString('K.24', 'Expression; regexp_replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace(null,"","")');
        $expected = null;
        TestCheck::assertNull('K.25', 'Expression; regexp_replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ä","ä","Ä")');
        $expected = "Ä";
        TestCheck::assertString('K.26', 'Expression; regexp_replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Ä","Ä","ä")');
        $expected = "ä";
        TestCheck::assertString('K.27', 'Expression; regexp_replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Ä","ä","Ä")');
        $expected = "Ä";
        TestCheck::assertString('K.28', 'Expression; regexp_replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Ää","Ä","A")');
        $expected = "Aä";
        TestCheck::assertString('K.29', 'Expression; regexp_replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Ää","ä","A")');
        $expected = "ÄA";
        TestCheck::assertString('K.30', 'Expression; regexp_replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Ää","Ää","A")');
        $expected = "A";
        TestCheck::assertString('K.31', 'Expression; regexp_replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Ää","äÄ","A")');
        $expected = "Ää";
        TestCheck::assertString('K.32', 'Expression; regexp_replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("Ääa","Ää","A")');
        $expected = "Aa";
        TestCheck::assertString('K.33', 'Expression; regexp_replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("aÄä","Ää","ÄäÄä")');
        $expected = "aÄäÄä";
        TestCheck::assertString('K.34', 'Expression; regexp_replace() text function; international chars',  $actual, $expected, $results);



        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","A","")');
        $expected = 'BABAbaBAB';
        TestCheck::assertString('K.35', 'Expression; regexp_replace() text function; varied match criteria',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","^A","")');
        $expected = 'BABAbaBAB';
        TestCheck::assertString('K.36', 'Expression; regexp_replace() text function; varied match criteria',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","A$","")');
        $expected = 'ABABAbaBAB';
        TestCheck::assertString('K.37', 'Expression; regexp_replace() text function; varied match criteria',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","B","")');
        $expected = 'AABAbaBAB';
        TestCheck::assertString('K.38', 'Expression; regexp_replace() text function; varied match criteria',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","^B","")');
        $expected = 'ABABAbaBAB';
        TestCheck::assertString('K.39', 'Expression; regexp_replace() text function; varied match criteria',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","B$","")');
        $expected = 'ABABAbaBA';
        TestCheck::assertString('K.40', 'Expression; regexp_replace() text function; varied match criteria',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","")');
        $expected = 'ABABAbBAB';
        TestCheck::assertString('K.41', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","i")');
        $expected = 'BABAbaBAB';
        TestCheck::assertString('K.42', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","g")');
        $expected = 'ABABAbBAB';
        TestCheck::assertString('K.43', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","gi")');
        $expected = 'BBbBB';
        TestCheck::assertString('K.44', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","ig")');
        $expected = 'BBbBB';
        TestCheck::assertString('K.45', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","igc")');
        $expected = 'ABABAbBAB';
        TestCheck::assertString('K.46', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","cgi")');
        $expected = 'BBbBB';
        TestCheck::assertString('K.47', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","icg")');
        $expected = 'ABABAbBAB';
        TestCheck::assertString('K.48', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","cig")');
        $expected = 'BBbBB';
        TestCheck::assertString('K.49', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('regexp_replace("ABABAbaBAB","a","","CIG")');
        $expected = 'BBbBB';
        TestCheck::assertString('K.50', 'Expression; regexp_replace() text function; match parameters',  $actual, $expected, $results);


        // TEST: text function: replace()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('L.1', 'Expression; replace() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Apples","")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('L.2', 'Expression; replace() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Apples","","","")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('L.3', 'Expression; replace() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Apples",1,"A")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('L.4', 'Expression; replace() text function; fail if the search string parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Apples","A",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('L.5', 'Expression; replace() text function; fail if the replace string parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("","","")');
        $expected = '';
        TestCheck::assertString('L.6', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("a","","")');
        $expected = 'a';
        TestCheck::assertString('L.7', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("","a","")');
        $expected = '';
        TestCheck::assertString('L.8', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("","","a")');
        $expected = '';
        TestCheck::assertString('L.9', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("a","","a")');
        $expected = 'a';
        TestCheck::assertString('L.10', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("a","a","")');
        $expected = '';
        TestCheck::assertString('L.11', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("","a","a")');
        $expected = '';
        TestCheck::assertString('L.12', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("a","a","a")');
        $expected = 'a';
        TestCheck::assertString('L.13', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("ab","a","")');
        $expected = 'b';
        TestCheck::assertString('L.14', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Apples and Oranges","Apples","Oranges")');
        $expected = 'Oranges and Oranges';
        TestCheck::assertString('L.15', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Apples and Oranges","and","or")');
        $expected = 'Apples or Oranges';
        TestCheck::assertString('L.16', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Apples and Oranges","Oranges","Apples")');
        $expected = 'Apples and Apples';
        TestCheck::assertString('L.17', 'Expression; replace() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace(123,"3","0")');
        $expected = '120';
        TestCheck::assertString('L.18', 'Expression; replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace(-123,"-","")');
        $expected = '123';
        TestCheck::assertString('L.19', 'Expression; replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace(-1.23,".",",")');
        $expected = '-1,23';
        TestCheck::assertString('L.20', 'Expression; replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace(true,"true","null")');
        $expected = 'null';
        TestCheck::assertString('L.21', 'Expression; replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace(false,"false","F")');
        $expected = 'F';
        TestCheck::assertString('L.22', 'Expression; replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace(null,"","")');
        $expected = null;
        TestCheck::assertNull('L.23', 'Expression; replace() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("ä","ä","Ä")');
        $expected = "Ä";
        TestCheck::assertString('L.24', 'Expression; replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Ä","Ä","ä")');
        $expected = "ä";
        TestCheck::assertString('L.25', 'Expression; replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Ä","ä","Ä")');
        $expected = "Ä";
        TestCheck::assertString('L.26', 'Expression; replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Ää","Ä","A")');
        $expected = "Aä";
        TestCheck::assertString('L.27', 'Expression; replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Ää","ä","A")');
        $expected = "ÄA";
        TestCheck::assertString('L.28', 'Expression; replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Ää","Ää","A")');
        $expected = "A";
        TestCheck::assertString('L.29', 'Expression; replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Ää","äÄ","A")');
        $expected = "Ää";
        TestCheck::assertString('L.30', 'Expression; replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("Ääa","Ää","A")');
        $expected = "Aa";
        TestCheck::assertString('L.31', 'Expression; replace() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('replace("aÄä","Ää","ÄäÄä")');
        $expected = "aÄäÄä";
        TestCheck::assertString('L.32', 'Expression; replace() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: reverse()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('M.1', 'Expression; reverse() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse("Apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('M.2', 'Expression; reverse() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse("")');
        $expected = '';
        TestCheck::assertString('M.3', 'Expression; reverse() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse("Apples")');
        $expected = 'selppA';
        TestCheck::assertString('M.4', 'Expression; reverse() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse("Oranges")');
        $expected = 'segnarO';
        TestCheck::assertString('M.5', 'Expression; reverse() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse("Apples and Oranges")');
        $expected = 'segnarO dna selppA';
        TestCheck::assertString('M.6', 'Expression; reverse() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse(123)');
        $expected = '321';
        TestCheck::assertString('M.7', 'Expression; reverse() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse(-123)');
        $expected = '321-';
        TestCheck::assertString('M.8', 'Expression; reverse() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse(-1.23)');
        $expected = '32.1-';
        TestCheck::assertString('M.9', 'Expression; reverse() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse(true)');
        $expected = 'eurt';
        TestCheck::assertString('M.10', 'Expression; reverse() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse(false)');
        $expected = 'eslaf';
        TestCheck::assertString('M.11', 'Expression; reverse() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse(null)');
        $expected = null;
        TestCheck::assertNull('M.12', 'Expression; reverse() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse("Ä")');
        $expected = "Ä";
        TestCheck::assertString('M.13', 'Expression; reverse() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('reverse("aáàäâãÁÄ")');
        $expected = "ÄÁãâäàáa";
        TestCheck::assertString('M.14', 'Expression; reverse() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: right()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('N.1', 'Expression; right() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("Apples and Oranges")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('N.2', 'Expression; right() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("Apples and Oranges",1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('N.3', 'Expression; right() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("Apples and Oranges","1")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('N.4', 'Expression; right() text function; fail if the length parameter isn\'t an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("Apples and Oranges",1.1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('N.5', 'Expression; right() text function; fail if the length parameter isn\'t an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("",1)');
        $expected = '';
        TestCheck::assertString('N.6', 'Expression; right() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("Apples and Oranges",5)');
        $expected = 'anges';
        TestCheck::assertString('N.7', 'Expression; right() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("Apples and Oranges",10)');
        $expected = 'nd Oranges';
        TestCheck::assertString('N.8', 'Expression; right() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("Apples and Oranges",20)');
        $expected = 'Apples and Oranges';
        TestCheck::assertString('N.9', 'Expression; right() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("Apples and Oranges",0)');
        $expected = '';
        TestCheck::assertString('N.10', 'Expression; right() text function; if a zero length param is specified, return the empty string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("Apples and Oranges",-7)');
        $expected = 'and Oranges';
        TestCheck::assertString('N.11', 'Expression; right() text function; if a negative length param is specified, remove the specified characters from the beginning of the string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right(123,1)');
        $expected = '3';
        TestCheck::assertString('N.12', 'Expression; right() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right(-123,2)');
        $expected = '23';
        TestCheck::assertString('N.13', 'Expression; right() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right(-1.23,4)');
        $expected = '1.23';
        TestCheck::assertString('N.14', 'Expression; right() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right(true,1)');
        $expected = 'e';
        TestCheck::assertString('N.15', 'Expression; right() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right(false,2)');
        $expected = 'se';
        TestCheck::assertString('N.16', 'Expression; right() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right(null,2)');
        $expected = null;
        TestCheck::assertNull('N.17', 'Expression; right() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("aáàäâãÁÄ",1)');
        $expected = "Ä";
        TestCheck::assertString('N.18', 'Expression; right() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("aáàäâãÁÄ",2)');
        $expected = "ÁÄ";
        TestCheck::assertString('N.19', 'Expression; right() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('right("aáàäâãÁÄ",10)');
        $expected = "aáàäâãÁÄ";
        TestCheck::assertString('N.20', 'Expression; right() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: rpad()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('O.1', 'Expression; rpad() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("Apples",1,"0",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('O.2', 'Expression; rpad() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("Apples",1.1,"0")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('O.3', 'Expression; rpad() text function; fail if the length parameter is not an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("Apples",1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('O.4', 'Expression; rpad() text function; fail if the pad parameter is not a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("",1)');
        $expected = ' ';
        TestCheck::assertString('O.5', 'Expression; rpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("Apples",1)');
        $expected = 'A';
        TestCheck::assertString('O.6', 'Expression; rpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("Oranges",10)');
        $expected = 'Oranges   ';
        TestCheck::assertString('O.7', 'Expression; rpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("Apples and Oranges",25,"-")');
        $expected = 'Apples and Oranges-------';
        TestCheck::assertString('O.8', 'Expression; rpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("Apples",0)');
        $expected = '';
        TestCheck::assertString('O.9', 'Expression; rpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("Apples",-1)');
        $expected = '';
        TestCheck::assertString('O.10', 'Expression; rpad() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("123",8,"01")');
        $expected = '12301010';
        TestCheck::assertString('O.11', 'Expression; rpad() text function; multiple pad characters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad(123,6,"0")');
        $expected = '123000';
        TestCheck::assertString('O.12', 'Expression; rpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad(-123,6,"0")');
        $expected = '-12300';
        TestCheck::assertString('O.13', 'Expression; rpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad(-1.23,6,"0")');
        $expected = '-1.230';
        TestCheck::assertString('O.14', 'Expression; rpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad(true,6,"0")');
        $expected = 'true00';
        TestCheck::assertString('O.15', 'Expression; rpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad(false,6,"0")');
        $expected = 'false0';
        TestCheck::assertString('O.16', 'Expression; rpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad(null,6,"0")');
        $expected = null;
        TestCheck::assertNull('O.17', 'Expression; rpad() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("á",2,"Á")');
        $expected = "áÁ";
        TestCheck::assertString('O.18', 'Expression; rpad() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("Á",2,"á")');
        $expected = "Áá";
        TestCheck::assertString('O.19', 'Expression; rpad() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rpad("aá",10,"àäâãÁÄ")');
        $expected = "aáàäâãÁÄàä";
        TestCheck::assertString('O.20', 'Expression; rpad() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: rtrim()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('P.1', 'Expression; rtrim() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim("Apples","",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('P.2', 'Expression; rtrim() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim("Apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('P.3', 'Expression; rtrim() text function; fail if the characters-to-trim parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim("")');
        $expected = '';
        TestCheck::assertString('P.4', 'Expression; rtrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim("Apples")');
        $expected = 'Apples';
        TestCheck::assertString('P.5', 'Expression; rtrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim("Oranges   ")');
        $expected = 'Oranges';
        TestCheck::assertString('P.6', 'Expression; rtrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim("-------Apples and Oranges-------","-")');
        $expected = '-------Apples and Oranges';
        TestCheck::assertString('P.7', 'Expression; rtrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim("-------  Apples and Oranges  -------","- OOOrrraaannngggeeesss")');
        $expected = '-------  Apples and';
        TestCheck::assertString('P.8', 'Expression; rtrim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim(123,"3")');
        $expected = '12';
        TestCheck::assertString('P.9', 'Expression; rtrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim(-123,"23")');
        $expected = '-1';
        TestCheck::assertString('P.10', 'Expression; rtrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim(-1.23,"0123456789")');
        $expected = '-1.';
        TestCheck::assertString('P.11', 'Expression; rtrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim(true,"eru")');
        $expected = 't';
        TestCheck::assertString('P.12', 'Expression; rtrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim(false,"alse")');
        $expected = 'f';
        TestCheck::assertString('P.13', 'Expression; rtrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim(null)');
        $expected = null;
        TestCheck::assertNull('P.14', 'Expression; rtrim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim("aáàäâãÁÄ","ÁÄaáàäâã")');
        $expected = "";
        TestCheck::assertString('P.15', 'Expression; rtrim() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('rtrim("ÁÄaáàäâã","ãâäàáa")');
        $expected = "ÁÄ";
        TestCheck::assertString('P.16', 'Expression; rtrim() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: strpart()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('Q.1', 'Expression; strpart() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("","")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('Q.2', 'Expression; strpart() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("","",1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('Q.3', 'Expression; strpart() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('Q.4', 'Expression; strpart() text function; fail if the search string parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("","",1)');
        $expected = "";
        TestCheck::assertString('Q.5', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a","",1)');
        $expected = "a";
        TestCheck::assertString('Q.6', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("","a",1)');
        $expected = "";
        TestCheck::assertString('Q.7', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a","a",1)');
        $expected = "";
        TestCheck::assertString('Q.8', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("A","a",1)');
        $expected = "A";
        TestCheck::assertString('Q.9', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a","A",1)');
        $expected = "a";
        TestCheck::assertString('Q.10', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a","ab",1)');
        $expected = "a";
        TestCheck::assertString('Q.11', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("ab","a",1)');
        $expected = "";
        TestCheck::assertString('Q.12', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("ab","b",1)');
        $expected = "a";
        TestCheck::assertString('Q.13', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a,b",",",-1)');
        $expected = "";
        TestCheck::assertString('Q.14', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a,b",",",0)');
        $expected = "";
        TestCheck::assertString('Q.15', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a,b",",",1)');
        $expected = "a";
        TestCheck::assertString('Q.16', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a,b",",",2)');
        $expected = "b";
        TestCheck::assertString('Q.17', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a,b",",",3)');
        $expected = "";
        TestCheck::assertString('Q.18', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a;b,c",",;",-1)');
        $expected = "";
        TestCheck::assertString('Q.19', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a;b,c",",;",0)');
        $expected = "";
        TestCheck::assertString('Q.20', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a;b,c",",;",1)');
        $expected = "a;b,c";
        TestCheck::assertString('Q.21', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a;b;c",",;",2)');
        $expected = "";
        TestCheck::assertString('Q.22', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a;b;c",",;",3)');
        $expected = "";
        TestCheck::assertString('Q.23', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a;b;c",",;",4)');
        $expected = "";
        TestCheck::assertString('Q.24', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a;;c",";",1)');
        $expected = "a";
        TestCheck::assertString('Q.25', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a;;c",";",2)');
        $expected = "";
        TestCheck::assertString('Q.26', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a;;c",";",3)');
        $expected = "c";
        TestCheck::assertString('Q.27', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a,;c",",;",1)');
        $expected = "a";
        TestCheck::assertString('Q.28', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a,;c",",;",2)');
        $expected = "c";
        TestCheck::assertString('Q.29', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("a,;c",",;",3)');
        $expected = "";
        TestCheck::assertString('Q.30', 'Expression; strpart() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart(-5.7,".",0)');
        $expected = "";
        TestCheck::assertString('Q.31', 'Expression; strpart() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart(-5.7,".",1)');
        $expected = "-5";
        TestCheck::assertString('Q.32', 'Expression; strpart() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart(-5.7,".",2)');
        $expected = "7";
        TestCheck::assertString('Q.33', 'Expression; strpart() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart(-5.7,".",3)');
        $expected = "";
        TestCheck::assertString('Q.34', 'Expression; strpart() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart(true,"t",1)');
        $expected = "";
        TestCheck::assertString('Q.35', 'Expression; strpart() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart(true,"t",2)');
        $expected = "rue";
        TestCheck::assertString('Q.36', 'Expression; strpart() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart(false,"e",1)');
        $expected = "fals";
        TestCheck::assertString('Q.37', 'Expression; strpart() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart(false,"e",2)');
        $expected = "";
        TestCheck::assertString('Q.38', 'Expression; strpart() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart(null,"",1)');
        $expected = null;
        TestCheck::assertNull('Q.39', 'Expression; strpart() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("aáàäâãÁÄ","ä",1)');
        $expected = "aáà";
        TestCheck::assertString('Q.40', 'Expression; strpart() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("aáàäâãÁÄ","ä",2)');
        $expected = "âãÁÄ";
        TestCheck::assertString('Q.41', 'Expression; strpart() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("aáàäâãÁÄ","Á",1)');
        $expected = "aáàäâã";
        TestCheck::assertString('Q.42', 'Expression; strpart() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("aáàäâãÁÄ","Á",2)');
        $expected = "Ä";
        TestCheck::assertString('Q.43', 'Expression; strpart() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("aáàäâãÁÄ","Ä",1)');
        $expected = "aáàäâãÁ";
        TestCheck::assertString('Q.44', 'Expression; strpart() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpart("aáàäâãÁÄ","Ä",2)');
        $expected = "";
        TestCheck::assertString('Q.45', 'Expression; strpart() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: strpos()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('R.1', 'Expression; strpos() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("Apples","",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('R.2', 'Expression; strpos() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("Apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('R.3', 'Expression; strpos() text function; fail if the search string parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("","")');
        $expected = 1;
        TestCheck::assertNumber('R.4', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("a","")');
        $expected = 1;
        TestCheck::assertNumber('R.5', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("","a")');
        $expected = 0;
        TestCheck::assertNumber('R.6', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("a","a")');
        $expected = 1;
        TestCheck::assertNumber('R.7', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("A","a")');
        $expected = 0;
        TestCheck::assertNumber('R.8', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("a","A")');
        $expected = 0;
        TestCheck::assertNumber('R.9', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("a","ab")');
        $expected = 0;
        TestCheck::assertNumber('R.10', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("ab","a")');
        $expected = 1;
        TestCheck::assertNumber('R.11', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("ab","b")');
        $expected = 2;
        TestCheck::assertNumber('R.12', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("Apples and Oranges","Apples")');
        $expected = 1;
        TestCheck::assertNumber('R.13', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("Apples and Oranges","and")');
        $expected = 8;
        TestCheck::assertNumber('R.14', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("Apples and Oranges","Oranges")');
        $expected = 12;
        TestCheck::assertNumber('R.15', 'Expression; strpos() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos(123,"3")');
        $expected = 3;
        TestCheck::assertNumber('R.16', 'Expression; strpos() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos(-123,"12")');
        $expected = 2;
        TestCheck::assertNumber('R.17', 'Expression; strpos() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos(-1.23,".")');
        $expected = 3;
        TestCheck::assertNumber('R.18', 'Expression; strpos() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos(true,"t")');
        $expected = 1;
        TestCheck::assertNumber('R.19', 'Expression; strpos() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos(false,"e")');
        $expected = 5;
        TestCheck::assertNumber('R.20', 'Expression; strpos() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos(null,"")');
        $expected = null;
        TestCheck::assertNull('R.21', 'Expression; strpos() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","a")');
        $expected = 1;
        TestCheck::assertNumber('R.22', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","á")');
        $expected = 2;
        TestCheck::assertNumber('R.23', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","à")');
        $expected = 3;
        TestCheck::assertNumber('R.24', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","ä")');
        $expected = 4;
        TestCheck::assertNumber('R.25', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","â")');
        $expected = 5;
        TestCheck::assertNumber('R.26', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","ã")');
        $expected = 6;
        TestCheck::assertNumber('R.27', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","Á")');
        $expected = 7;
        TestCheck::assertNumber('R.28', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","Ä")');
        $expected = 8;
        TestCheck::assertNumber('R.29', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","àä")');
        $expected = 3;
        TestCheck::assertNumber('R.30', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","ÁÄ")');
        $expected = 7;
        TestCheck::assertNumber('R.31', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('strpos("aáàäâãÁÄ","aáàäâãÁÄa")');
        $expected = 0;
        TestCheck::assertNumber('R.32', 'Expression; strpos() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: substr()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('S.1', 'Expression; substr() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples",1,1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('S.2', 'Expression; substr() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples","1",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('S.3', 'Expression; substr() text function; fail if the starting offset isn\'t an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples",1,"1")');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('S.4', 'Expression; substr() text function; fail if the length param isn\'t an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples",1.1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('S.5', 'Expression; substr() text function; fail if the starting offset isn\'t an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples",1,1.1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('S.6', 'Expression; substr() text function; fail if the length param isn\'t an integer',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("",1,6)');
        $expected = '';
        TestCheck::assertString('S.7', 'Expression; substr() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples and Oranges",1,6)');
        $expected = 'Apples';
        TestCheck::assertString('S.8', 'Expression; substr() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples and Oranges",8,3)');
        $expected = 'and';
        TestCheck::assertString('S.9', 'Expression; substr() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples and Oranges",12,7)');
        $expected = 'Oranges';
        TestCheck::assertString('S.10', 'Expression; substr() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples and Oranges",0,0)');
        $expected = '';
        TestCheck::assertString('S.11', 'Expression; substr() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples and Oranges",0,2)');
        $expected = 'A';
        TestCheck::assertString('S.12', 'Expression; substr() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples and Oranges",-1,3)');
        $expected = 'A';
        TestCheck::assertString('S.13', 'Expression; substr() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples and Oranges",1,0)');
        $expected = '';
        TestCheck::assertString('S.14', 'Expression; substr() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples and Oranges",1,-1)');
        $expected = '';
        TestCheck::assertString('S.15', 'Expression; substr() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr(123,1,1)');
        $expected = '1';
        TestCheck::assertString('S.16', 'Expression; substr() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr(-123,1,2)');
        $expected = '-1';
        TestCheck::assertString('S.17', 'Expression; substr() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr(-1.23,3,3)');
        $expected = '.23';
        TestCheck::assertString('S.18', 'Expression; substr() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr(true,1,1)');
        $expected = 't';
        TestCheck::assertString('S.19', 'Expression; substr() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr(false,1,2)');
        $expected = 'fa';
        TestCheck::assertString('S.20', 'Expression; substr() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr(null,1,2)');
        $expected = null;
        TestCheck::assertNull('S.21', 'Expression; substr() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("Apples and Oranges",12)');
        $expected = 'Oranges';
        TestCheck::assertString('S.22', 'Expression; substr() text function; with only offset specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("aáàäâãÁÄ",2,1)');
        $expected = "á";
        TestCheck::assertString('S.23', 'Expression; substr() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("aáàäâãÁÄ",7,1)');
        $expected = "Á";
        TestCheck::assertString('S.24', 'Expression; substr() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("aáàäâãÁÄ",6,6)');
        $expected = "ãÁÄ";
        TestCheck::assertString('S.25', 'Expression; substr() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('substr("aáàäâãÁÄ",1,10)');
        $expected = "aáàäâãÁÄ";
        TestCheck::assertString('S.26', 'Expression; substr() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: trim()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('T.1', 'Expression; trim() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("Apples","",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('T.2', 'Expression; trim() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("Apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('T.3', 'Expression; trim() text function; fail if the characters-to-trim parameter isn\'t a string',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("")');
        $expected = '';
        TestCheck::assertString('T.4', 'Expression; trim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("Apples")');
        $expected = 'Apples';
        TestCheck::assertString('T.5', 'Expression; trim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("   Oranges   ")');
        $expected = 'Oranges';
        TestCheck::assertString('T.6', 'Expression; trim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("-------Apples and Oranges-------","-")');
        $expected = 'Apples and Oranges';
        TestCheck::assertString('T.7', 'Expression; trim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("-------  Apples and Oranges  -------","- OOOrrraaannngggeeesss")');
        $expected = 'Apples and';
        TestCheck::assertString('T.8', 'Expression; trim() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim(123,"3")');
        $expected = '12';
        TestCheck::assertString('T.9', 'Expression; trim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim(-123,"-")');
        $expected = '123';
        TestCheck::assertString('T.10', 'Expression; trim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim(-1.23,"-+0123456789")');
        $expected = '.';
        TestCheck::assertString('T.11', 'Expression; trim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim(true,"ru")');
        $expected = 'true';
        TestCheck::assertString('T.12', 'Expression; trim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim(false,"fae")');
        $expected = 'ls';
        TestCheck::assertString('T.13', 'Expression; trim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim(null)');
        $expected = null;
        TestCheck::assertNull('T.14', 'Expression; trim() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("aáàÁÄäâã","aáàäâãÁÄ")');
        $expected = "";
        TestCheck::assertString('T.15', 'Expression; trim() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("aáàÁÄäâã","äâãaáà")');
        $expected = "ÁÄ";
        TestCheck::assertString('T.15', 'Expression; trim() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trim("aáàÁÄäâã","äáâÄaãà")');
        $expected = "Á";
        TestCheck::assertString('T.16', 'Expression; trim() text function; international chars',  $actual, $expected, $results);



        // TEST: text function: upper()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('U.1', 'Expression; upper() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper("Apples",1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('U.2', 'Expression; upper() text function; fail if an incorrect number of parameters are specified',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper("")');
        $expected = '';
        TestCheck::assertString('U.3', 'Expression; upper() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper("Apples")');
        $expected = 'APPLES';
        TestCheck::assertString('U.4', 'Expression; upper() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper("Oranges")');
        $expected = 'ORANGES';
        TestCheck::assertString('U.5', 'Expression; upper() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper("Apples and Oranges")');
        $expected = 'APPLES AND ORANGES';
        TestCheck::assertString('U.6', 'Expression; upper() text function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper(123)');
        $expected = '123';
        TestCheck::assertString('U.7', 'Expression; upper() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper(-123)');
        $expected = '-123';
        TestCheck::assertString('U.8', 'Expression; upper() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper(-1.23)');
        $expected = '-1.23';
        TestCheck::assertString('U.9', 'Expression; upper() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper(true)');
        $expected = 'TRUE';
        TestCheck::assertString('U.10', 'Expression; upper() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper(false)');
        $expected = 'FALSE';
        TestCheck::assertString('U.11', 'Expression; upper() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper(null)');
        $expected = null;
        TestCheck::assertNull('U.12', 'Expression; upper() text function; implicit type conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper("áÁ")');
        $expected = "ÁÁ";
        TestCheck::assertString('U.13', 'Expression; upper() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper("Áá")');
        $expected = "ÁÁ";
        TestCheck::assertString('U.14', 'Expression; upper() text function; international chars',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('upper("aáàäâãÁÄ")');
        $expected = "AÁÀÄÂÃÁÄ";
        TestCheck::assertString('U.15', 'Expression; upper() text function; international chars',  $actual, $expected, $results);
    }
}
