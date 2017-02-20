<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-02-08
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // note: the following numeric functions are currently supported:
        //     abs(number val)
        //     acos(number val)
        //     asin(number val)
        //     atan(number val)
        //     ceiling(number val)
        //     cos(number val)
        //     exp(number val)
        //     floor(number val)
        //     ln(number val)
        //     log(number val)
        //     mod(number val, number val)
        //     pi()
        //     pow(number val, number val)
        //     round(number val)
        //     sign(number val)
        //     sin(number val)
        //     tan(number val)
        //     trunc(number val)

        // TODO:
        //     - add additional function variations:
        //       log(number,number) (i.e., a second optional parameter)
        //       round(number,number) (i.e., a second optional parameter)


        // TEST: numeric function: abs()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(0)');
        $expected = 0;
        TestCheck::assertNumber('A.3', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(0.0000000001)');
        $expected = 0.0000000001;
        TestCheck::assertNumber('A.4', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(0.01)');
        $expected = 0.01;
        TestCheck::assertNumber('A.5', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(1)');
        $expected = 1;
        TestCheck::assertNumber('A.6', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(1.4)');
        $expected = 1.4;
        TestCheck::assertNumber('A.7', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(1.5)');
        $expected = 1.5;
        TestCheck::assertNumber('A.8', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(1.6)');
        $expected = 1.6;
        TestCheck::assertNumber('A.9', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(2.0)');
        $expected = 2.0;
        TestCheck::assertNumber('A.10', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(1000000000.01)');
        $expected = 1000000000.01;
        TestCheck::assertNumber('A.12', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(-0)');
        $expected = 0;
        TestCheck::assertNumber('A.13', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(-0.0000000001)');
        $expected = 0.0000000001;
        TestCheck::assertNumber('A.14', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(-0.01)');
        $expected = 0.01;
        TestCheck::assertNumber('A.15', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(-1)');
        $expected = 1;
        TestCheck::assertNumber('A.16', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(-1.4)');
        $expected = 1.4;
        TestCheck::assertNumber('A.17', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(-1.5)');
        $expected = 1.5;
        TestCheck::assertNumber('A.18', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(-1.6)');
        $expected = 1.6;
        TestCheck::assertNumber('A.19', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(-2.0)');
        $expected = 2.0;
        TestCheck::assertNumber('A.20', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(-1000000000.01)');
        $expected = 1000000000.01;
        TestCheck::assertNumber('A.21', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs("9.9")');
        $expected = 9.9;
        TestCheck::assertNumber('A.22', 'Expression; abs() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs("-9.9")');
        $expected = 9.9;
        TestCheck::assertNumber('A.23', 'Expression; abs() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.24', 'Expression; abs() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('A.25', 'Expression; abs() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('abs(null)');
        $expected = null;
        TestCheck::assertNull('A.26', 'Expression; abs() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: acos(number)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(0)');
        $expected = 1.5707963267948966;
        TestCheck::assertNumber('B.3', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(0.5)');
        $expected = 1.0471975511965979;
        TestCheck::assertNumber('B.4', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(1)');
        $expected = 0.0;
        TestCheck::assertNumber('B.5', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(1.01)');
        $expected = NAN;
        TestCheck::assertNaN('B.6', 'Expression; acos() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(-0)');
        $expected = 1.5707963267948966;
        TestCheck::assertNumber('B.7', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(-0.5)');
        $expected = 2.0943951023931957;
        TestCheck::assertNumber('B.8', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(-1)');
        $expected = 3.141592653589793;
        TestCheck::assertNumber('B.9', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(-1.01)');
        $expected = NAN;
        TestCheck::assertNaN('B.10', 'Expression; acos() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos("1")');
        $expected = 0.0;
        TestCheck::assertNumber('B.11', 'Expression; acos() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos("-1.01")');
        $expected = NAN;
        TestCheck::assertNaN('B.12', 'Expression; acos() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.13', 'Expression; acos() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('B.14', 'Expression; acos() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('acos(null)');
        $expected = null;
        TestCheck::assertNull('B.15', 'Expression; acos() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: asin(number)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(0)');
        $expected = 0.0;
        TestCheck::assertNumber('C.3', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(0.5)');
        $expected = 0.5235987755982989;
        TestCheck::assertNumber('C.4', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(1)');
        $expected = 1.5707963267948966;
        TestCheck::assertNumber('C.5', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(1.01)');
        $expected = NAN;
        TestCheck::assertNaN('C.6', 'Expression; asin() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(-0)');
        $expected = 0.0;
        TestCheck::assertNumber('C.7', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(-0.5)');
        $expected = -0.5235987755982989;
        TestCheck::assertNumber('C.8', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(-1)');
        $expected = -1.5707963267948966;
        TestCheck::assertNumber('C.9', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(-1.01)');
        $expected = NAN;
        TestCheck::assertNaN('C.10', 'Expression; asin() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin("1")');
        $expected = 1.5707963267948966;
        TestCheck::assertNumber('C.11', 'Expression; asin() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin("-1.01")');
        $expected = NAN;
        TestCheck::assertNaN('C.12', 'Expression; asin() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.13', 'Expression; asin() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('C.14', 'Expression; asin() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('asin(null)');
        $expected = null;
        TestCheck::assertNull('C.15', 'Expression; asin() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: atan(number)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(0)');
        $expected = 0.0;
        TestCheck::assertNumber('D.3', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(0.1)');
        $expected = 0.09966865249116204;
        TestCheck::assertNumber('D.4', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(1)');
        $expected = 0.7853981633974483;
        TestCheck::assertNumber('D.5', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(1000000000)');
        $expected = 1.5707963257948967;
        TestCheck::assertNumber('D.6', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(-0)');
        $expected = 0.0;
        TestCheck::assertNumber('D.7', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(-0.1)');
        $expected = -0.09966865249116204;
        TestCheck::assertNumber('D.8', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(-1)');
        $expected = -0.7853981633974483;
        TestCheck::assertNumber('D.9', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(-1000000000)');
        $expected = -1.5707963257948967;
        TestCheck::assertNumber('D.10', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan("1")');
        $expected = 0.7853981633974483;
        TestCheck::assertNumber('D.11', 'Expression; atan() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan("-1000000000")');
        $expected = -1.5707963257948967;
        TestCheck::assertNumber('D.12', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.13', 'Expression; atan() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('D.14', 'Expression; atan() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('atan(null)');
        $expected = null;
        TestCheck::assertNull('D.15', 'Expression; atan() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: ceiling()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(0)');
        $expected = (float)0;
        TestCheck::assertNumber('E.3', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(0.0000000001)');
        $expected = (float)1;
        TestCheck::assertNumber('E.4', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(0.01)');
        $expected = (float)1;
        TestCheck::assertNumber('E.5', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(1)');
        $expected = (float)1;
        TestCheck::assertNumber('E.6', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(1.4)');
        $expected = (float)2;
        TestCheck::assertNumber('E.7', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(1.5)');
        $expected = (float)2;
        TestCheck::assertNumber('E.8', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(1.6)');
        $expected = (float)2;
        TestCheck::assertNumber('E.9', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(2.0)');
        $expected = (float)2;
        TestCheck::assertNumber('E.10', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(1000000000.01)');
        $expected = (float)1000000001;
        TestCheck::assertNumber('E.11', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(-0)');
        $expected = (float)0;
        TestCheck::assertNumber('E.12', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(-0.0000000001)');
        $expected = (float)0;
        TestCheck::assertNumber('E.13', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(-0.01)');
        $expected = (float)0;
        TestCheck::assertNumber('E.14', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(-1)');
        $expected = (float)-1;
        TestCheck::assertNumber('E.15', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(-1.4)');
        $expected = (float)-1;
        TestCheck::assertNumber('E.16', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(-1.5)');
        $expected = (float)-1;
        TestCheck::assertNumber('E.17', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(-1.6)');
        $expected = (float)-1;
        TestCheck::assertNumber('E.18', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(-2.0)');
        $expected = (float)-2;
        TestCheck::assertNumber('E.19', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(-1000000000.01)');
        $expected = (float)-1000000000;
        TestCheck::assertNumber('E.20', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling("9.9")');
        $expected = (float)10;
        TestCheck::assertNumber('E.21', 'Expression; ceiling() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling("-9.9")');
        $expected = (float)-9;
        TestCheck::assertNumber('E.22', 'Expression; ceiling() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.23', 'Expression; ceiling() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('E.24', 'Expression; ceiling() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ceiling(null)');
        $expected = null;
        TestCheck::assertNull('E.25', 'Expression; ceiling() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: cos(number)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('F.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('F.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(0)');
        $expected = 1.0;
        TestCheck::assertNumber('F.3', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(0.01)');
        $expected = 0.9999500004166653;
        TestCheck::assertNumber('F.4', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(0.5)');
        $expected = 0.8775825618903728;
        TestCheck::assertNumber('F.5', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(1)');
        $expected = 0.5403023058681398;
        TestCheck::assertNumber('F.6', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(1.5)');
        $expected = 0.0707372016677029;
        TestCheck::assertNumber('F.7', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(1000000000.01)');
        $expected = 0.8323869491174198;
        TestCheck::assertNumber('F.8', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(-0)');
        $expected = 1.0;
        TestCheck::assertNumber('F.9', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(-0.01)');
        $expected = 0.9999500004166653;
        TestCheck::assertNumber('F.10', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(-0.5)');
        $expected = 0.8775825618903728;
        TestCheck::assertNumber('F.11', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(-1)');
        $expected = 0.5403023058681398;
        TestCheck::assertNumber('F.12', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(-1.5)');
        $expected = 0.0707372016677029;
        TestCheck::assertNumber('F.13', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(-1000000000.01)');
        $expected = 0.8323869491174198;
        TestCheck::assertNumber('F.14', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos("1")');
        $expected = 0.5403023058681398;
        TestCheck::assertNumber('F.15', 'Expression; cos() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('F.16', 'Expression; cos() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('F.17', 'Expression; cos() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('cos(null)');
        $expected = null;
        TestCheck::assertNull('F.18', 'Expression; cos() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: exp(number)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('G.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('G.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(0)');
        $expected = 1.0;
        TestCheck::assertNumber('G.3', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(0.01)');
        $expected = 1.01005016708417;
        TestCheck::assertNumber('G.4', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(0.5)');
        $expected = 1.64872127070013;
        TestCheck::assertNumber('G.5', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(1)');
        $expected = 2.718281828459045;
        TestCheck::assertNumber('G.6', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(2)');
        $expected = 7.38905609893065;
        TestCheck::assertNumber('G.7', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(10)');
        $expected = 22026.4657948067;
        TestCheck::assertNumber('G.8', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(-0)');
        $expected = 1.0;
        TestCheck::assertNumber('G.9', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(-0.01)');
        $expected = 0.990049833749168;
        TestCheck::assertNumber('G.10', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(-0.5)');
        $expected = 0.606530659712633;
        TestCheck::assertNumber('G.11', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(-1)');
        $expected = 0.367879441171442;
        TestCheck::assertNumber('G.12', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(-2)');
        $expected = 0.135335283236613;
        TestCheck::assertNumber('G.13', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(-10)');
        $expected = 0.0000453999297625;
        TestCheck::assertNumber('G.14', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp("1")');
        $expected = 2.718281828459045;
        TestCheck::assertNumber('G.15', 'Expression; exp() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('G.16', 'Expression; exp() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('G.17', 'Expression; exp() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('exp(null)');
        $expected = null;
        TestCheck::assertNull('G.18', 'Expression; exp() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: floor()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(0)');
        $expected = (float)0;
        TestCheck::assertNumber('H.3', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(0.0000000001)');
        $expected = (float)0;
        TestCheck::assertNumber('H.4', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(0.01)');
        $expected = (float)0;
        TestCheck::assertNumber('H.5', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(1)');
        $expected = (float)1;
        TestCheck::assertNumber('H.6', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(1.4)');
        $expected = (float)1;
        TestCheck::assertNumber('H.7', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(1.5)');
        $expected = (float)1;
        TestCheck::assertNumber('H.8', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(1.6)');
        $expected = (float)1;
        TestCheck::assertNumber('H.9', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(2.0)');
        $expected = (float)2;
        TestCheck::assertNumber('H.10', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(1000000000.01)');
        $expected = (float)1000000000;
        TestCheck::assertNumber('H.12', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(-0)');
        $expected = (float)0;
        TestCheck::assertNumber('H.13', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(-0.0000000001)');
        $expected = (float)-1;
        TestCheck::assertNumber('H.14', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(-0.01)');
        $expected = (float)-1;
        TestCheck::assertNumber('H.15', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(-1)');
        $expected = (float)-1;
        TestCheck::assertNumber('H.16', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(-1.4)');
        $expected = (float)-2;
        TestCheck::assertNumber('H.17', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(-1.5)');
        $expected = (float)-2;
        TestCheck::assertNumber('H.18', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(-1.6)');
        $expected = (float)-2;
        TestCheck::assertNumber('H.19', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(-2.0)');
        $expected = (float)-2;
        TestCheck::assertNumber('H.20', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(-1000000000.01)');
        $expected = (float)-1000000001;
        TestCheck::assertNumber('H.21', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor("9.9")');
        $expected = (float)9;
        TestCheck::assertNumber('H.22', 'Expression; floor() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor("-9.9")');
        $expected = (float)-10;
        TestCheck::assertNumber('H.23', 'Expression; floor() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.24', 'Expression; floor() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('H.25', 'Expression; floor() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('floor(null)');
        $expected = null;
        TestCheck::assertNull('H.26', 'Expression; floor() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: ln(number)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(1)');
        $expected = 0.0;
        TestCheck::assertNumber('I.3', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(1.01005016708417)');
        $expected = 0.01;
        TestCheck::assertNumber('I.4', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(1.64872127070013)');
        $expected = 0.5;
        TestCheck::assertNumber('I.5', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(2.718281828459045)');
        $expected = 1.0;
        TestCheck::assertNumber('I.6', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(7.38905609893065)');
        $expected = 2.0;
        TestCheck::assertNumber('I.7', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(22026.4657948067)');
        $expected = 10.0;
        TestCheck::assertNumber('I.8', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(0)');
        $expected = -INF;
        TestCheck::assertNumber('I.9', 'Expression; ln(0) numeric function; return -Infinity',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(-0.01)');
        $expected = NAN;
        TestCheck::assertNaN('I.10', 'Expression; ln() numeric function; return NaN if parameter is < 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln("1")');
        $expected = 0.0;
        TestCheck::assertNumber('I.11', 'Expression; ln() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln("-0.01")');
        $expected = NAN;
        TestCheck::assertNaN('I.12', 'Expression; ln() numeric function; return NaN if parameter is < 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.13', 'Expression; ln() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('I.14', 'Expression; ln() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('ln(null)');
        $expected = null;
        TestCheck::assertNull('I.15', 'Expression; ln() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: log(number)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('J.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('J.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(1)');
        $expected = 0.0;
        TestCheck::assertNumber('J.3', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(0.01)');
        $expected = -2.0;
        TestCheck::assertNumber('J.4', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(0.1)');
        $expected = -1.0;
        TestCheck::assertNumber('J.5', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(0.5)');
        $expected = -0.30102999566398;
        TestCheck::assertNumber('J.6', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(100)');
        $expected = 2.0;
        TestCheck::assertNumber('J.7', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(1000)');
        $expected = 3.0;
        TestCheck::assertNumber('J.8', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(0)');
        $expected = -INF;
        TestCheck::assertNumber('J.9', 'Expression; log() numeric function; return -INF if parameter is 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(-0.01)');
        $expected = NAN;
        TestCheck::assertNaN('J.10', 'Expression; log() numeric function; return NaN if parameter is < 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log("1")');
        $expected = 0.0;
        TestCheck::assertNumber('J.11', 'Expression; log() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log("-0.01")');
        $expected = NAN;
        TestCheck::assertNaN('J.12', 'Expression; log() numeric function; return NaN if parameter is < 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('J.13', 'Expression; log() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('J.14', 'Expression; log() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('log(null)');
        $expected = null;
        TestCheck::assertNull('J.15', 'Expression; log() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: mod(number,number)
        // note: mod(x,y) is equivalent to the x%y and returns the remainder of x divided by y; this
        // is different behavior than the classical mathematical mod(x,y) function (expressed as
        // x - y * FLOOR(x/y)) when x or y are negative

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('K.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(1,1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('K.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(1,0)');
        $expected = null;
        TestCheck::assertNull('K.3', 'Expression; return null when a calculating a modulo with zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(0,1)');
        $expected = 0;
        TestCheck::assertNumber('K.4', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(2,3)');
        $expected = 2;
        TestCheck::assertNumber('K.5', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(3,2)');
        $expected = 1;
        TestCheck::assertNumber('K.6', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(2,-3)');
        $expected = 2;
        TestCheck::assertNumber('K.7', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(-3,2)');
        $expected = -1;
        TestCheck::assertNumber('K.8', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(2.4,3)');
        $expected = 2.4;
        TestCheck::assertNumber('K.9', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(3,2.4)');
        $expected = 0.6;
        TestCheck::assertNumber('K.10', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(15,2.4)');
        $expected = 0.6;
        TestCheck::assertNumber('K.11', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(11,4)');
        $expected = 3;
        TestCheck::assertNumber('K.12', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(11,-4)');
        $expected = 3;
        TestCheck::assertNumber('K.13', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(-11,4)');
        $expected = -3;
        TestCheck::assertNumber('K.14', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(-11,-4)');
        $expected = -3;
        TestCheck::assertNumber('K.15', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod("11",4)');
        $expected = 3;
        TestCheck::assertNumber('K.16', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(11,"4")');
        $expected = 3;
        TestCheck::assertNumber('K.17', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod("11","4")');
        $expected = 3;
        TestCheck::assertNumber('K.18', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(true,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('K.19', 'Expression; mod() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(1,true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('K.20', 'Expression; mod() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(true,true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('K.21', 'Expression; mod() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(null,1)');
        $expected = null;
        TestCheck::assertNull('K.22', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(1,null)');
        $expected = null;
        TestCheck::assertNull('K.23', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('mod(null,null)');
        $expected = null;
        TestCheck::assertNull('K.24', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: pi()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pi(1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('L.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pi()');
        $expected = 3.14159265358979;
        TestCheck::assertNumber('L.2', 'Expression; pi() numeric function',  $actual, $expected, $results);



        // TEST: numeric function: pow(number,number)
        // TODO: add tests

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('M.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(1,1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('M.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(10,1000)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('M.3', 'Expression; fail on numeric overflow',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(100,0)');
        $expected = (float)1;
        TestCheck::assertNumber('M.4', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(0,100)');
        $expected = (float)0;
        TestCheck::assertNumber('M.5', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(2,3)');
        $expected = (float)8;
        TestCheck::assertNumber('M.6', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(3,2)');
        $expected = (float)9;
        TestCheck::assertNumber('M.7', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(4,-2)');
        $expected = (float)0.0625;
        TestCheck::assertNumber('M.8', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(-2,4)');
        $expected = (float)16;
        TestCheck::assertNumber('M.9', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(4,0.5)');
        $expected = (float)2;
        TestCheck::assertNumber('M.10', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(-4,0.5)');
        $expected = NAN;
        TestCheck::assertNaN('M.11', 'Expression; pow() numeric function; return NaN if result is imaginary',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(2.4,3)');
        $expected = (float)13.824;
        TestCheck::assertNumber('M.12', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(4,1.5)');
        $expected = (float)8;
        TestCheck::assertNumber('M.13', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(1.5,4)');
        $expected = (float)5.0625;
        TestCheck::assertNumber('M.14', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow("2",3)');
        $expected = (float)8;
        TestCheck::assertNumber('M.15', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(2,"3")');
        $expected = (float)8;
        TestCheck::assertNumber('M.16', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow("2","3")');
        $expected = (float)8;
        TestCheck::assertNumber('M.17', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow("-4",0.5)');
        $expected = NAN;
        TestCheck::assertNaN('M.18', 'Expression; pow() numeric function; return NaN if result is imaginary',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(true,2)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('M.19', 'Expression; pow() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(2,true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('M.20', 'Expression; pow() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(true,true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('M.21', 'Expression; pow() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(null,1)');
        $expected = null;
        TestCheck::assertNull('M.22', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(1,null)');
        $expected = null;
        TestCheck::assertNull('M.23', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('pow(null,null)');
        $expected = null;
        TestCheck::assertNull('M.24', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: round()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('N.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(1,1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('N.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(0)');
        $expected = (float)0;
        TestCheck::assertNumber('N.3', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(0.0000000001)');
        $expected = (float)0;
        TestCheck::assertNumber('N.4', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(0.01)');
        $expected = (float)0;
        TestCheck::assertNumber('N.5', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(1)');
        $expected = (float)1;
        TestCheck::assertNumber('N.6', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(1.4)');
        $expected = (float)1;
        TestCheck::assertNumber('N.7', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(1.5)');
        $expected = (float)2;
        TestCheck::assertNumber('N.8', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(1.6)');
        $expected = (float)2;
        TestCheck::assertNumber('N.9', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(2.0)');
        $expected = (float)2;
        TestCheck::assertNumber('N.10', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(1000000000.01)');
        $expected = (float)1000000000;
        TestCheck::assertNumber('N.11', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(-0)');
        $expected = (float)0;
        TestCheck::assertNumber('N.12', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(-0.0000000001)');
        $expected = (float)0;
        TestCheck::assertNumber('N.13', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(-0.01)');
        $expected = (float)0;
        TestCheck::assertNumber('N.14', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(-1)');
        $expected = (float)-1;
        TestCheck::assertNumber('N.15', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(-1.4)');
        $expected = (float)-1;
        TestCheck::assertNumber('N.16', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(-1.5)');
        $expected = (float)-2;
        TestCheck::assertNumber('N.17', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(-1.6)');
        $expected = (float)-2;
        TestCheck::assertNumber('N.18', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(-2.0)');
        $expected = (float)-2;
        TestCheck::assertNumber('N.19', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(-1000000000.01)');
        $expected = (float)-1000000000;
        TestCheck::assertNumber('N.20', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round("9.9")');
        $expected = (float)10;
        TestCheck::assertNumber('N.21', 'Expression; round() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round("-9.9")');
        $expected = (float)-10;
        TestCheck::assertNumber('N.22', 'Expression; round() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('N.24', 'Expression; round() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('N.25', 'Expression; round() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('round(null)');
        $expected = null;
        TestCheck::assertNull('N.26', 'Expression; round() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: sign()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('O.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('O.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(0)');
        $expected = (float)0;
        TestCheck::assertNumber('O.3', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(0.0000000001)');
        $expected = (float)1;
        TestCheck::assertNumber('O.4', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(0.01)');
        $expected = (float)1;
        TestCheck::assertNumber('O.5', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(1)');
        $expected = (float)1;
        TestCheck::assertNumber('O.6', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(1.4)');
        $expected = (float)1;
        TestCheck::assertNumber('O.7', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(1.5)');
        $expected = (float)1;
        TestCheck::assertNumber('O.8', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(1.6)');
        $expected = (float)1;
        TestCheck::assertNumber('O.9', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(2.0)');
        $expected = (float)1;
        TestCheck::assertNumber('O.10', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(1000000000.01)');
        $expected = (float)1;
        TestCheck::assertNumber('O.12', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(-0)');
        $expected = (float)0;
        TestCheck::assertNumber('O.13', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(-0.0000000001)');
        $expected = (float)-1;
        TestCheck::assertNumber('O.14', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(-0.01)');
        $expected = (float)-1;
        TestCheck::assertNumber('O.15', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(-1)');
        $expected = (float)-1;
        TestCheck::assertNumber('O.16', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(-1.4)');
        $expected = (float)-1;
        TestCheck::assertNumber('O.17', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(-1.5)');
        $expected = (float)-1;
        TestCheck::assertNumber('O.18', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(-1.6)');
        $expected = (float)-1;
        TestCheck::assertNumber('O.19', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(-2.0)');
        $expected = (float)-1;
        TestCheck::assertNumber('O.20', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(-1000000000.01)');
        $expected = (float)-1;
        TestCheck::assertNumber('O.21', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign("9.9")');
        $expected = (float)1;
        TestCheck::assertNumber('O.22', 'Expression; sign() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign("-9.9")');
        $expected = (float)-1;
        TestCheck::assertNumber('O.23', 'Expression; sign() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('O.24', 'Expression; sign() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('O.25', 'Expression; sign() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sign(null)');
        $expected = null;
        TestCheck::assertNull('O.26', 'Expression; sign() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: sin(number)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('P.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('P.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(0)');
        $expected = 0.0;
        TestCheck::assertNumber('P.3', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(0.01)');
        $expected = 0.009999833334166664;
        TestCheck::assertNumber('P.4', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(0.5)');
        $expected = 0.479425538604203;
        TestCheck::assertNumber('P.5', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(1)');
        $expected = 0.8414709848078965;
        TestCheck::assertNumber('P.6', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(1.5)');
        $expected = 0.9974949866040544;
        TestCheck::assertNumber('P.7', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(1000000000.01)');
        $expected = 0.55419488173187;
        TestCheck::assertNumber('P.8', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(-0)');
        $expected = 0.0;
        TestCheck::assertNumber('P.9', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(-0.01)');
        $expected = -0.009999833334166664;
        TestCheck::assertNumber('P.10', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(-0.5)');
        $expected = -0.479425538604203;
        TestCheck::assertNumber('P.11', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(-1)');
        $expected = -0.8414709848078965;
        TestCheck::assertNumber('P.12', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(-1.5)');
        $expected = -0.9974949866040544;
        TestCheck::assertNumber('P.13', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(-1000000000.01)');
        $expected = -0.55419488173187;
        TestCheck::assertNumber('P.14', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin("1")');
        $expected = 0.8414709848078965;
        TestCheck::assertNumber('P.15', 'Expression; sin() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('P.16', 'Expression; sin() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('P.17', 'Expression; sin() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('sin(null)');
        $expected = null;
        TestCheck::assertNull('P.18', 'Expression; sin() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: tan(number)

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('Q.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('Q.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(0)');
        $expected = 0.0;
        TestCheck::assertNumber('Q.3', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(0.01)');
        $expected = 0.010000333346667207;
        TestCheck::assertNumber('Q.4', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(0.5)');
        $expected = 0.5463024898437905;
        TestCheck::assertNumber('Q.5', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(1)');
        $expected = 1.5574077246549023;
        TestCheck::assertNumber('Q.6', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(1.5)');
        $expected = 14.101419947171719;
        TestCheck::assertNumber('Q.7', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(1000000000.01)');
        $expected = 0.66578996981994;
        TestCheck::assertNumber('Q.8', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(-0)');
        $expected = 0.0;
        TestCheck::assertNumber('Q.9', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(-0.01)');
        $expected = -0.010000333346667207;
        TestCheck::assertNumber('Q.10', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(-0.5)');
        $expected = -0.5463024898437905;
        TestCheck::assertNumber('Q.11', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(-1)');
        $expected = -1.5574077246549023;
        TestCheck::assertNumber('Q.12', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(-1.5)');
        $expected = -14.101419947171719;
        TestCheck::assertNumber('Q.13', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(-1000000000.01)');
        $expected = -0.66578996981994;
        TestCheck::assertNumber('Q.14', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan("1")');
        $expected = 1.5574077246549023;
        TestCheck::assertNumber('Q.15', 'Expression; tan() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('Q.16', 'Expression; tan() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('Q.17', 'Expression; tan() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('tan(null)');
        $expected = null;
        TestCheck::assertNull('Q.18', 'Expression; tan() numeric function',  $actual, $expected, $results);



        // TEST: numeric function: trunc()

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc()');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('R.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(1,1)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('R.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(0)');
        $expected = (float)0;
        TestCheck::assertNumber('R.3', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(0.0000000001)');
        $expected = (float)0;
        TestCheck::assertNumber('R.4', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(0.01)');
        $expected = (float)0;
        TestCheck::assertNumber('R.5', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(1)');
        $expected = (float)1;
        TestCheck::assertNumber('R.6', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(1.4)');
        $expected = (float)1;
        TestCheck::assertNumber('R.7', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(1.5)');
        $expected = (float)1;
        TestCheck::assertNumber('R.8', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(1.6)');
        $expected = (float)1;
        TestCheck::assertNumber('R.9', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(2.0)');
        $expected = (float)2;
        TestCheck::assertNumber('R.10', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(1000000000.01)');
        $expected = (float)1000000000;
        TestCheck::assertNumber('R.12', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(-0)');
        $expected = (float)0;
        TestCheck::assertNumber('R.13', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(-0.0000000001)');
        $expected = (float)0;
        TestCheck::assertNumber('R.14', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(-0.01)');
        $expected = (float)0;
        TestCheck::assertNumber('R.15', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(-1)');
        $expected = (float)-1;
        TestCheck::assertNumber('R.16', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(-1.4)');
        $expected = (float)-1;
        TestCheck::assertNumber('R.17', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(-1.5)');
        $expected = (float)-1;
        TestCheck::assertNumber('R.18', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(-1.6)');
        $expected = (float)-1;
        TestCheck::assertNumber('R.19', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(-2.0)');
        $expected = (float)-2;
        TestCheck::assertNumber('R.20', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(-1000000000.01)');
        $expected = (float)-1000000000;
        TestCheck::assertNumber('R.21', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc("9.9")');
        $expected = (float)9;
        TestCheck::assertNumber('R.22', 'Expression; trunc() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc("-9.9")');
        $expected = (float)-9;
        TestCheck::assertNumber('R.23', 'Expression; trunc() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(true)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('R.24', 'Expression; trunc() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(false)');
        $expected = TestError::ERROR_BAD_PARSE;
        TestCheck::assertString('R.25', 'Expression; trunc() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = TestUtil::evalExpression('trunc(null)');
        $expected = null;
        TestCheck::assertNull('R.26', 'Expression; trunc() numeric function; implicit conversion',  $actual, $expected, $results);
    }
}
