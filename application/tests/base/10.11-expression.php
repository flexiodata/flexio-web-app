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


declare(strict_types=1);
namespace Flexio\Tests;


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
        $actual = \Flexio\Tests\Util::evalExpression('abs()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(0)');
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('A.3', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(0.0000000001)');
        $expected = 0.0000000001;
        \Flexio\Tests\Check::assertNumber('A.4', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(0.01)');
        $expected = 0.01;
        \Flexio\Tests\Check::assertNumber('A.5', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(1)');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('A.6', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(1.4)');
        $expected = 1.4;
        \Flexio\Tests\Check::assertNumber('A.7', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(1.5)');
        $expected = 1.5;
        \Flexio\Tests\Check::assertNumber('A.8', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(1.6)');
        $expected = 1.6;
        \Flexio\Tests\Check::assertNumber('A.9', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(2.0)');
        $expected = 2.0;
        \Flexio\Tests\Check::assertNumber('A.10', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(1000000000.01)');
        $expected = 1000000000.01;
        \Flexio\Tests\Check::assertNumber('A.12', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(-0)');
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('A.13', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(-0.0000000001)');
        $expected = 0.0000000001;
        \Flexio\Tests\Check::assertNumber('A.14', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(-0.01)');
        $expected = 0.01;
        \Flexio\Tests\Check::assertNumber('A.15', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(-1)');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('A.16', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(-1.4)');
        $expected = 1.4;
        \Flexio\Tests\Check::assertNumber('A.17', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(-1.5)');
        $expected = 1.5;
        \Flexio\Tests\Check::assertNumber('A.18', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(-1.6)');
        $expected = 1.6;
        \Flexio\Tests\Check::assertNumber('A.19', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(-2.0)');
        $expected = 2.0;
        \Flexio\Tests\Check::assertNumber('A.20', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(-1000000000.01)');
        $expected = 1000000000.01;
        \Flexio\Tests\Check::assertNumber('A.21', 'Expression; abs() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs("9.9")');
        $expected = 9.9;
        \Flexio\Tests\Check::assertNumber('A.22', 'Expression; abs() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs("-9.9")');
        $expected = 9.9;
        \Flexio\Tests\Check::assertNumber('A.23', 'Expression; abs() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.24', 'Expression; abs() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('A.25', 'Expression; abs() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('abs(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('A.26', 'Expression; abs() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: acos(number)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(0)');
        $expected = 1.5707963267948966;
        \Flexio\Tests\Check::assertNumber('B.3', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(0.5)');
        $expected = 1.0471975511965979;
        \Flexio\Tests\Check::assertNumber('B.4', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(1)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('B.5', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(1.01)');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('B.6', 'Expression; acos() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(-0)');
        $expected = 1.5707963267948966;
        \Flexio\Tests\Check::assertNumber('B.7', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(-0.5)');
        $expected = 2.0943951023931957;
        \Flexio\Tests\Check::assertNumber('B.8', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(-1)');
        $expected = 3.141592653589793;
        \Flexio\Tests\Check::assertNumber('B.9', 'Expression; acos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(-1.01)');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('B.10', 'Expression; acos() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos("1")');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('B.11', 'Expression; acos() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos("-1.01")');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('B.12', 'Expression; acos() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.13', 'Expression; acos() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('B.14', 'Expression; acos() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('acos(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('B.15', 'Expression; acos() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: asin(number)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(0)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('C.3', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(0.5)');
        $expected = 0.5235987755982989;
        \Flexio\Tests\Check::assertNumber('C.4', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(1)');
        $expected = 1.5707963267948966;
        \Flexio\Tests\Check::assertNumber('C.5', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(1.01)');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('C.6', 'Expression; asin() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(-0)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('C.7', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(-0.5)');
        $expected = -0.5235987755982989;
        \Flexio\Tests\Check::assertNumber('C.8', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(-1)');
        $expected = -1.5707963267948966;
        \Flexio\Tests\Check::assertNumber('C.9', 'Expression; asin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(-1.01)');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('C.10', 'Expression; asin() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin("1")');
        $expected = 1.5707963267948966;
        \Flexio\Tests\Check::assertNumber('C.11', 'Expression; asin() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin("-1.01")');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('C.12', 'Expression; asin() numeric function; return NaN if parameter is out of range',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.13', 'Expression; asin() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('C.14', 'Expression; asin() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('asin(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('C.15', 'Expression; asin() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: atan(number)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(0)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('D.3', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(0.1)');
        $expected = 0.09966865249116204;
        \Flexio\Tests\Check::assertNumber('D.4', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(1)');
        $expected = 0.7853981633974483;
        \Flexio\Tests\Check::assertNumber('D.5', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(1000000000)');
        $expected = 1.5707963257948967;
        \Flexio\Tests\Check::assertNumber('D.6', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(-0)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('D.7', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(-0.1)');
        $expected = -0.09966865249116204;
        \Flexio\Tests\Check::assertNumber('D.8', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(-1)');
        $expected = -0.7853981633974483;
        \Flexio\Tests\Check::assertNumber('D.9', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(-1000000000)');
        $expected = -1.5707963257948967;
        \Flexio\Tests\Check::assertNumber('D.10', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan("1")');
        $expected = 0.7853981633974483;
        \Flexio\Tests\Check::assertNumber('D.11', 'Expression; atan() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan("-1000000000")');
        $expected = -1.5707963257948967;
        \Flexio\Tests\Check::assertNumber('D.12', 'Expression; atan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.13', 'Expression; atan() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('D.14', 'Expression; atan() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('atan(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('D.15', 'Expression; atan() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: ceiling()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('E.3', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(0.0000000001)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('E.4', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(0.01)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('E.5', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(1)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('E.6', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(1.4)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('E.7', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(1.5)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('E.8', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(1.6)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('E.9', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(2.0)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('E.10', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(1000000000.01)');
        $expected = (float)1000000001;
        \Flexio\Tests\Check::assertNumber('E.11', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(-0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('E.12', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(-0.0000000001)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('E.13', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(-0.01)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('E.14', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(-1)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('E.15', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(-1.4)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('E.16', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(-1.5)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('E.17', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(-1.6)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('E.18', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(-2.0)');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('E.19', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(-1000000000.01)');
        $expected = (float)-1000000000;
        \Flexio\Tests\Check::assertNumber('E.20', 'Expression; ceiling() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling("9.9")');
        $expected = (float)10;
        \Flexio\Tests\Check::assertNumber('E.21', 'Expression; ceiling() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling("-9.9")');
        $expected = (float)-9;
        \Flexio\Tests\Check::assertNumber('E.22', 'Expression; ceiling() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.23', 'Expression; ceiling() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('E.24', 'Expression; ceiling() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ceiling(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('E.25', 'Expression; ceiling() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: cos(number)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(0)');
        $expected = 1.0;
        \Flexio\Tests\Check::assertNumber('F.3', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(0.01)');
        $expected = 0.9999500004166653;
        \Flexio\Tests\Check::assertNumber('F.4', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(0.5)');
        $expected = 0.8775825618903728;
        \Flexio\Tests\Check::assertNumber('F.5', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(1)');
        $expected = 0.5403023058681398;
        \Flexio\Tests\Check::assertNumber('F.6', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(1.5)');
        $expected = 0.0707372016677029;
        \Flexio\Tests\Check::assertNumber('F.7', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(1000000000.01)');
        $expected = 0.8323869491174198;
        \Flexio\Tests\Check::assertNumber('F.8', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(-0)');
        $expected = 1.0;
        \Flexio\Tests\Check::assertNumber('F.9', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(-0.01)');
        $expected = 0.9999500004166653;
        \Flexio\Tests\Check::assertNumber('F.10', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(-0.5)');
        $expected = 0.8775825618903728;
        \Flexio\Tests\Check::assertNumber('F.11', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(-1)');
        $expected = 0.5403023058681398;
        \Flexio\Tests\Check::assertNumber('F.12', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(-1.5)');
        $expected = 0.0707372016677029;
        \Flexio\Tests\Check::assertNumber('F.13', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(-1000000000.01)');
        $expected = 0.8323869491174198;
        \Flexio\Tests\Check::assertNumber('F.14', 'Expression; cos() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos("1")');
        $expected = 0.5403023058681398;
        \Flexio\Tests\Check::assertNumber('F.15', 'Expression; cos() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.16', 'Expression; cos() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('F.17', 'Expression; cos() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('cos(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('F.18', 'Expression; cos() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: exp(number)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('G.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('G.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(0)');
        $expected = 1.0;
        \Flexio\Tests\Check::assertNumber('G.3', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(0.01)');
        $expected = 1.01005016708417;
        \Flexio\Tests\Check::assertNumber('G.4', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(0.5)');
        $expected = 1.64872127070013;
        \Flexio\Tests\Check::assertNumber('G.5', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(1)');
        $expected = 2.718281828459045;
        \Flexio\Tests\Check::assertNumber('G.6', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(2)');
        $expected = 7.38905609893065;
        \Flexio\Tests\Check::assertNumber('G.7', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(10)');
        $expected = 22026.4657948067;
        \Flexio\Tests\Check::assertNumber('G.8', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(-0)');
        $expected = 1.0;
        \Flexio\Tests\Check::assertNumber('G.9', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(-0.01)');
        $expected = 0.990049833749168;
        \Flexio\Tests\Check::assertNumber('G.10', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(-0.5)');
        $expected = 0.606530659712633;
        \Flexio\Tests\Check::assertNumber('G.11', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(-1)');
        $expected = 0.367879441171442;
        \Flexio\Tests\Check::assertNumber('G.12', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(-2)');
        $expected = 0.135335283236613;
        \Flexio\Tests\Check::assertNumber('G.13', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(-10)');
        $expected = 0.0000453999297625;
        \Flexio\Tests\Check::assertNumber('G.14', 'Expression; exp() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp("1")');
        $expected = 2.718281828459045;
        \Flexio\Tests\Check::assertNumber('G.15', 'Expression; exp() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('G.16', 'Expression; exp() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('G.17', 'Expression; exp() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('exp(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('G.18', 'Expression; exp() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: floor()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('H.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('H.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('H.3', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(0.0000000001)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('H.4', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(0.01)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('H.5', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(1)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('H.6', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(1.4)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('H.7', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(1.5)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('H.8', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(1.6)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('H.9', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(2.0)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('H.10', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(1000000000.01)');
        $expected = (float)1000000000;
        \Flexio\Tests\Check::assertNumber('H.12', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(-0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('H.13', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(-0.0000000001)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('H.14', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(-0.01)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('H.15', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(-1)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('H.16', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(-1.4)');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('H.17', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(-1.5)');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('H.18', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(-1.6)');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('H.19', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(-2.0)');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('H.20', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(-1000000000.01)');
        $expected = (float)-1000000001;
        \Flexio\Tests\Check::assertNumber('H.21', 'Expression; floor() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor("9.9")');
        $expected = (float)9;
        \Flexio\Tests\Check::assertNumber('H.22', 'Expression; floor() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor("-9.9")');
        $expected = (float)-10;
        \Flexio\Tests\Check::assertNumber('H.23', 'Expression; floor() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('H.24', 'Expression; floor() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('H.25', 'Expression; floor() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('floor(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('H.26', 'Expression; floor() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: ln(number)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('I.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('I.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(1)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('I.3', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(1.01005016708417)');
        $expected = 0.01;
        \Flexio\Tests\Check::assertNumber('I.4', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(1.64872127070013)');
        $expected = 0.5;
        \Flexio\Tests\Check::assertNumber('I.5', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(2.718281828459045)');
        $expected = 1.0;
        \Flexio\Tests\Check::assertNumber('I.6', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(7.38905609893065)');
        $expected = 2.0;
        \Flexio\Tests\Check::assertNumber('I.7', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(22026.4657948067)');
        $expected = 10.0;
        \Flexio\Tests\Check::assertNumber('I.8', 'Expression; ln() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(0)');
        $expected = -INF;
        \Flexio\Tests\Check::assertNumber('I.9', 'Expression; ln(0) numeric function; return -Infinity',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(-0.01)');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('I.10', 'Expression; ln() numeric function; return NaN if parameter is < 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln("1")');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('I.11', 'Expression; ln() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln("-0.01")');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('I.12', 'Expression; ln() numeric function; return NaN if parameter is < 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('I.13', 'Expression; ln() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('I.14', 'Expression; ln() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('ln(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('I.15', 'Expression; ln() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: log(number)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('J.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('J.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(1)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('J.3', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(0.01)');
        $expected = -2.0;
        \Flexio\Tests\Check::assertNumber('J.4', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(0.1)');
        $expected = -1.0;
        \Flexio\Tests\Check::assertNumber('J.5', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(0.5)');
        $expected = -0.30102999566398;
        \Flexio\Tests\Check::assertNumber('J.6', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(100)');
        $expected = 2.0;
        \Flexio\Tests\Check::assertNumber('J.7', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(1000)');
        $expected = 3.0;
        \Flexio\Tests\Check::assertNumber('J.8', 'Expression; log() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(0)');
        $expected = -INF;
        \Flexio\Tests\Check::assertNumber('J.9', 'Expression; log() numeric function; return -INF if parameter is 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(-0.01)');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('J.10', 'Expression; log() numeric function; return NaN if parameter is < 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log("1")');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('J.11', 'Expression; log() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log("-0.01")');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('J.12', 'Expression; log() numeric function; return NaN if parameter is < 0',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('J.13', 'Expression; log() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('J.14', 'Expression; log() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('log(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('J.15', 'Expression; log() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: mod(number,number)
        // note: mod(x,y) is equivalent to the x%y and returns the remainder of x divided by y; this
        // is different behavior than the classical mathematical mod(x,y) function (expressed as
        // x - y * FLOOR(x/y)) when x or y are negative

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('K.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(1,1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('K.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(1,0)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('K.3', 'Expression; return null when a calculating a modulo with zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(0,1)');
        $expected = 0;
        \Flexio\Tests\Check::assertNumber('K.4', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(2,3)');
        $expected = 2;
        \Flexio\Tests\Check::assertNumber('K.5', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(3,2)');
        $expected = 1;
        \Flexio\Tests\Check::assertNumber('K.6', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(2,-3)');
        $expected = 2;
        \Flexio\Tests\Check::assertNumber('K.7', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(-3,2)');
        $expected = -1;
        \Flexio\Tests\Check::assertNumber('K.8', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(2.4,3)');
        $expected = 2.4;
        \Flexio\Tests\Check::assertNumber('K.9', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(3,2.4)');
        $expected = 0.6;
        \Flexio\Tests\Check::assertNumber('K.10', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(15,2.4)');
        $expected = 0.6;
        \Flexio\Tests\Check::assertNumber('K.11', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(11,4)');
        $expected = 3;
        \Flexio\Tests\Check::assertNumber('K.12', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(11,-4)');
        $expected = 3;
        \Flexio\Tests\Check::assertNumber('K.13', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(-11,4)');
        $expected = -3;
        \Flexio\Tests\Check::assertNumber('K.14', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(-11,-4)');
        $expected = -3;
        \Flexio\Tests\Check::assertNumber('K.15', 'Expression; mod() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod("11",4)');
        $expected = 3;
        \Flexio\Tests\Check::assertNumber('K.16', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(11,"4")');
        $expected = 3;
        \Flexio\Tests\Check::assertNumber('K.17', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod("11","4")');
        $expected = 3;
        \Flexio\Tests\Check::assertNumber('K.18', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(true,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('K.19', 'Expression; mod() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(1,true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('K.20', 'Expression; mod() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(true,true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('K.21', 'Expression; mod() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(null,1)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('K.22', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(1,null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('K.23', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('mod(null,null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('K.24', 'Expression; mod() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: pi()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pi(1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('L.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pi()');
        $expected = 3.14159265358979;
        \Flexio\Tests\Check::assertNumber('L.2', 'Expression; pi() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pi');
        $expected = 3.14159265358979;
        \Flexio\Tests\Check::assertNumber('L.3', 'Expression; pi numeric variable',  $actual, $expected, $results);



        // TEST: numeric function: pow(number,number)
        // TODO: add tests

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('M.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(1,1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('M.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(10,1000)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('M.3', 'Expression; fail on numeric overflow',  $actual, $expected, $results, \Flexio\Tests\Base::FLAG_ERROR_SUPPRESS);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(100,0)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('M.4', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(0,100)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('M.5', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(2,3)');
        $expected = (float)8;
        \Flexio\Tests\Check::assertNumber('M.6', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(3,2)');
        $expected = (float)9;
        \Flexio\Tests\Check::assertNumber('M.7', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(4,-2)');
        $expected = (float)0.0625;
        \Flexio\Tests\Check::assertNumber('M.8', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(-2,4)');
        $expected = (float)16;
        \Flexio\Tests\Check::assertNumber('M.9', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(4,0.5)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('M.10', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(-4,0.5)');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('M.11', 'Expression; pow() numeric function; return NaN if result is imaginary',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(2.4,3)');
        $expected = (float)13.824;
        \Flexio\Tests\Check::assertNumber('M.12', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(4,1.5)');
        $expected = (float)8;
        \Flexio\Tests\Check::assertNumber('M.13', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(1.5,4)');
        $expected = (float)5.0625;
        \Flexio\Tests\Check::assertNumber('M.14', 'Expression; pow() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow("2",3)');
        $expected = (float)8;
        \Flexio\Tests\Check::assertNumber('M.15', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(2,"3")');
        $expected = (float)8;
        \Flexio\Tests\Check::assertNumber('M.16', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow("2","3")');
        $expected = (float)8;
        \Flexio\Tests\Check::assertNumber('M.17', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow("-4",0.5)');
        $expected = NAN;
        \Flexio\Tests\Check::assertNaN('M.18', 'Expression; pow() numeric function; return NaN if result is imaginary',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(true,2)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('M.19', 'Expression; pow() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(2,true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('M.20', 'Expression; pow() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(true,true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('M.21', 'Expression; pow() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(null,1)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('M.22', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(1,null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('M.23', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('pow(null,null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('M.24', 'Expression; pow() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: round()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('N.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(1,1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('N.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('N.3', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(0.0000000001)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('N.4', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(0.01)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('N.5', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(1)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('N.6', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(1.4)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('N.7', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(1.5)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('N.8', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(1.6)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('N.9', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(2.0)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('N.10', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(1000000000.01)');
        $expected = (float)1000000000;
        \Flexio\Tests\Check::assertNumber('N.11', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(-0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('N.12', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(-0.0000000001)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('N.13', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(-0.01)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('N.14', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(-1)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('N.15', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(-1.4)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('N.16', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(-1.5)');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('N.17', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(-1.6)');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('N.18', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(-2.0)');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('N.19', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(-1000000000.01)');
        $expected = (float)-1000000000;
        \Flexio\Tests\Check::assertNumber('N.20', 'Expression; round() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round("9.9")');
        $expected = (float)10;
        \Flexio\Tests\Check::assertNumber('N.21', 'Expression; round() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round("-9.9")');
        $expected = (float)-10;
        \Flexio\Tests\Check::assertNumber('N.22', 'Expression; round() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('N.24', 'Expression; round() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('N.25', 'Expression; round() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('round(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('N.26', 'Expression; round() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: sign()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('O.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('O.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('O.3', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(0.0000000001)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('O.4', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(0.01)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('O.5', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(1)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('O.6', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(1.4)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('O.7', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(1.5)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('O.8', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(1.6)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('O.9', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(2.0)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('O.10', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(1000000000.01)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('O.12', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(-0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('O.13', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(-0.0000000001)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('O.14', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(-0.01)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('O.15', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(-1)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('O.16', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(-1.4)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('O.17', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(-1.5)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('O.18', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(-1.6)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('O.19', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(-2.0)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('O.20', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(-1000000000.01)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('O.21', 'Expression; sign() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign("9.9")');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('O.22', 'Expression; sign() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign("-9.9")');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('O.23', 'Expression; sign() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('O.24', 'Expression; sign() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('O.25', 'Expression; sign() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sign(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('O.26', 'Expression; sign() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: sin(number)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('P.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('P.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(0)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('P.3', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(0.01)');
        $expected = 0.009999833334166664;
        \Flexio\Tests\Check::assertNumber('P.4', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(0.5)');
        $expected = 0.479425538604203;
        \Flexio\Tests\Check::assertNumber('P.5', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(1)');
        $expected = 0.8414709848078965;
        \Flexio\Tests\Check::assertNumber('P.6', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(1.5)');
        $expected = 0.9974949866040544;
        \Flexio\Tests\Check::assertNumber('P.7', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(1000000000.01)');
        $expected = 0.55419488173187;
        \Flexio\Tests\Check::assertNumber('P.8', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(-0)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('P.9', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(-0.01)');
        $expected = -0.009999833334166664;
        \Flexio\Tests\Check::assertNumber('P.10', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(-0.5)');
        $expected = -0.479425538604203;
        \Flexio\Tests\Check::assertNumber('P.11', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(-1)');
        $expected = -0.8414709848078965;
        \Flexio\Tests\Check::assertNumber('P.12', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(-1.5)');
        $expected = -0.9974949866040544;
        \Flexio\Tests\Check::assertNumber('P.13', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(-1000000000.01)');
        $expected = -0.55419488173187;
        \Flexio\Tests\Check::assertNumber('P.14', 'Expression; sin() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin("1")');
        $expected = 0.8414709848078965;
        \Flexio\Tests\Check::assertNumber('P.15', 'Expression; sin() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('P.16', 'Expression; sin() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('P.17', 'Expression; sin() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('sin(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('P.18', 'Expression; sin() numeric function; implicit conversion',  $actual, $expected, $results);



        // TEST: numeric function: tan(number)

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('Q.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('Q.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(0)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('Q.3', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(0.01)');
        $expected = 0.010000333346667207;
        \Flexio\Tests\Check::assertNumber('Q.4', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(0.5)');
        $expected = 0.5463024898437905;
        \Flexio\Tests\Check::assertNumber('Q.5', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(1)');
        $expected = 1.5574077246549023;
        \Flexio\Tests\Check::assertNumber('Q.6', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(1.5)');
        $expected = 14.101419947171719;
        \Flexio\Tests\Check::assertNumber('Q.7', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(1000000000.01)');
        $expected = 0.66578996981994;
        \Flexio\Tests\Check::assertNumber('Q.8', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(-0)');
        $expected = 0.0;
        \Flexio\Tests\Check::assertNumber('Q.9', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(-0.01)');
        $expected = -0.010000333346667207;
        \Flexio\Tests\Check::assertNumber('Q.10', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(-0.5)');
        $expected = -0.5463024898437905;
        \Flexio\Tests\Check::assertNumber('Q.11', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(-1)');
        $expected = -1.5574077246549023;
        \Flexio\Tests\Check::assertNumber('Q.12', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(-1.5)');
        $expected = -14.101419947171719;
        \Flexio\Tests\Check::assertNumber('Q.13', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(-1000000000.01)');
        $expected = -0.66578996981994;
        \Flexio\Tests\Check::assertNumber('Q.14', 'Expression; tan() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan("1")');
        $expected = 1.5574077246549023;
        \Flexio\Tests\Check::assertNumber('Q.15', 'Expression; tan() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('Q.16', 'Expression; tan() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('Q.17', 'Expression; tan() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('tan(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('Q.18', 'Expression; tan() numeric function',  $actual, $expected, $results);



        // TEST: numeric function: trunc()

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc()');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('R.1', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(1,1)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('R.2', 'Expression; fail if function has incorrect number of parameters',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('R.3', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(0.0000000001)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('R.4', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(0.01)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('R.5', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(1)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('R.6', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(1.4)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('R.7', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(1.5)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('R.8', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(1.6)');
        $expected = (float)1;
        \Flexio\Tests\Check::assertNumber('R.9', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(2.0)');
        $expected = (float)2;
        \Flexio\Tests\Check::assertNumber('R.10', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(1000000000.01)');
        $expected = (float)1000000000;
        \Flexio\Tests\Check::assertNumber('R.12', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(-0)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('R.13', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(-0.0000000001)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('R.14', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(-0.01)');
        $expected = (float)0;
        \Flexio\Tests\Check::assertNumber('R.15', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(-1)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('R.16', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(-1.4)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('R.17', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(-1.5)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('R.18', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(-1.6)');
        $expected = (float)-1;
        \Flexio\Tests\Check::assertNumber('R.19', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(-2.0)');
        $expected = (float)-2;
        \Flexio\Tests\Check::assertNumber('R.20', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(-1000000000.01)');
        $expected = (float)-1000000000;
        \Flexio\Tests\Check::assertNumber('R.21', 'Expression; trunc() numeric function',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc("9.9")');
        $expected = (float)9;
        \Flexio\Tests\Check::assertNumber('R.22', 'Expression; trunc() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc("-9.9")');
        $expected = (float)-9;
        \Flexio\Tests\Check::assertNumber('R.23', 'Expression; trunc() numeric function; implicit conversion',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(true)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('R.24', 'Expression; trunc() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(false)');
        $expected = \Flexio\Tests\Base::ERROR_BAD_PARSE;
        \Flexio\Tests\Check::assertString('R.25', 'Expression; trunc() numeric function; implicit conversion; TODO: handle this way?',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('trunc(null)');
        $expected = null;
        \Flexio\Tests\Check::assertNull('R.26', 'Expression; trunc() numeric function; implicit conversion',  $actual, $expected, $results);
    }
}
