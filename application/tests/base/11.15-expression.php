<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-09-16
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
        // note: numeric accuracy; see information and solutions here:
        //     https://randomascii.wordpress.com/2012/02/11/they-sure-look-equal/
        //     https://randomascii.wordpress.com/2012/02/25/comparing-floating-point-numbers-2012-edition/


        // TEST: equality comparison with zero and numbers near zero

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0 = -0');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0 = 0');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0 = -0.0');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.0 = 0.0');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0 = -0.0');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.0 = 0.0');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.6', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0 = 0.0000000000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.7', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0000000000000001 = 0');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.8', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0 = -0.0000000000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.9', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.0000000000000001 = 0');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.10', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0 = 0.0000000000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.11', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0000000000000001 = 0.0');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.12', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0 = -0.0000000000000001');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.13', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-0.0000000000000001 = 0.0');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.14', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0 = 1e-308');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.15', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-308 = 0');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.16', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0 = -1e-308');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.17', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1e-308 = 0');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.18', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0 = 1e-308');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.19', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1e-308 = 0.0');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.20', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.0 = -1e-308');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.21', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('-1e-308 = 0.0');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.22', 'Expression; check for numeric accuracy with values near zero',  $actual, $expected, $results);



        // TEST: equality comparison involving calculations

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.1 + 0.2 = 0.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 - 0.2 = 0.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.2 + 0.3 = 0.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 - 0.3 = 0.2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 + 0.4 = 0.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 - 0.4 = 0.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.6', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.4 + 0.5 = 0.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.7', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 - 0.5 = 0.4');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.8', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 + 0.6 = 1.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.9', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1 - 0.6 = 0.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.10', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.6 + 0.7 = 1.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.11', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.3 - 0.7 = 0.6');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.12', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 + 0.8 = 1.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.13', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.5 - 0.8 = 0.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.14', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.8 + 0.9 = 1.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.15', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.7 - 0.9 = 0.8');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.16', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 + 1.0 = 1.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.17', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.9 - 1.0 = 0.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.18', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.1 + 9999.2 = 19998.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.19', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.3 - 9999.2 = 9999.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.20', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.2 + 9999.3 = 19998.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.21', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.5 - 9999.3 = 9999.2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.22', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.3 + 9999.4 = 19998.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.23', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.7 - 9999.4 = 9999.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.24', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.4 + 9999.5 = 19998.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.25', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.9 - 9999.5 = 9999.4');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.26', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.5 + 9999.6 = 19999.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.27', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.1 - 9999.6 = 9999.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.28', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.6 + 9999.7 = 19999.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.29', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.3 - 9999.7 = 9999.6');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.30', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.7 + 9999.8 = 19999.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.31', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.5 - 9999.8 = 9999.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.32', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.8 + 9999.9 = 19999.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.33', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.7 - 9999.9 = 9999.8');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.34', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.9 + 10000.0 = 19999.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.35', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.9 - 10000.0 = 9999.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.36', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);



        // TEST: non-equality comparison involving calculations

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.1 + 0.2 != 0.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.1', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 - 0.2 != 0.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.2', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.2 + 0.3 != 0.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.3', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 - 0.3 != 0.2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.4', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 + 0.4 != 0.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.5', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 - 0.4 != 0.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.6', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.4 + 0.5 != 0.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.7', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 - 0.5 != 0.4');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.8', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 + 0.6 != 1.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.9', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1 - 0.6 != 0.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.10', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.6 + 0.7 != 1.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.11', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.3 - 0.7 != 0.6');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.12', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 + 0.8 != 1.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.13', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.5 - 0.8 != 0.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.14', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.8 + 0.9 != 1.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.15', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.7 - 0.9 != 0.8');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.16', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 + 1.0 != 1.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.17', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.9 - 1.0 != 0.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.18', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.1 + 9999.2 != 19998.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.19', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.3 - 9999.2 != 9999.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.20', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.2 + 9999.3 != 19998.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.21', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.5 - 9999.3 != 9999.2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.22', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.3 + 9999.4 != 19998.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.23', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.7 - 9999.4 != 9999.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.24', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.4 + 9999.5 != 19998.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.25', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.9 - 9999.5 != 9999.4');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.26', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.5 + 9999.6 != 19999.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.27', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.1 - 9999.6 != 9999.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.28', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.6 + 9999.7 != 19999.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.29', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.3 - 9999.7 != 9999.6');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.30', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.7 + 9999.8 != 19999.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.31', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.5 - 9999.8 != 9999.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.32', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.8 + 9999.9 != 19999.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.33', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.7 - 9999.9 != 9999.8');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.34', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.9 + 10000.0 != 19999.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.35', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.9 - 10000.0 != 9999.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('C.36', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);



        // TEST: non-equality comparison involving calculations

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.1 + 0.2 <> 0.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.1', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 - 0.2 <> 0.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.2', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.2 + 0.3 <> 0.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.3', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 - 0.3 <> 0.2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.4', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 + 0.4 <> 0.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.5', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 - 0.4 <> 0.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.6', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.4 + 0.5 <> 0.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.7', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 - 0.5 <> 0.4');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.8', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 + 0.6 <> 1.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.9', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1 - 0.6 <> 0.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.10', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.6 + 0.7 <> 1.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.11', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.3 - 0.7 <> 0.6');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.12', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 + 0.8 <> 1.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.13', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.5 - 0.8 <> 0.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.14', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.8 + 0.9 <> 1.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.15', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.7 - 0.9 <> 0.8');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.16', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 + 1.0 <> 1.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.17', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.9 - 1.0 <> 0.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.18', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.1 + 9999.2 <> 19998.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.19', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.3 - 9999.2 <> 9999.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.20', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.2 + 9999.3 <> 19998.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.21', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.5 - 9999.3 <> 9999.2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.22', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.3 + 9999.4 <> 19998.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.23', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.7 - 9999.4 <> 9999.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.24', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.4 + 9999.5 <> 19998.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.25', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.9 - 9999.5 <> 9999.4');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.26', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.5 + 9999.6 <> 19999.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.27', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.1 - 9999.6 <> 9999.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.28', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.6 + 9999.7 <> 19999.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.29', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.3 - 9999.7 <> 9999.6');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.30', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.7 + 9999.8 <> 19999.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.31', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.5 - 9999.8 <> 9999.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.32', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.8 + 9999.9 <> 19999.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.33', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.7 - 9999.9 <> 9999.8');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.34', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.9 + 10000.0 <> 19999.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.35', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.9 - 10000.0 <> 9999.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('D.36', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);



        // TEST: greater-than comparison involving calculations

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.1 + 0.2 > 0.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.1', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 - 0.2 > 0.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.2', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.2 + 0.3 > 0.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.3', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 - 0.3 > 0.2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.4', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 + 0.4 > 0.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.5', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 - 0.4 > 0.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.6', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.4 + 0.5 > 0.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.7', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 - 0.5 > 0.4');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.8', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 + 0.6 > 1.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.9', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1 - 0.6 > 0.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.10', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.6 + 0.7 > 1.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.11', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.3 - 0.7 > 0.6');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.12', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 + 0.8 > 1.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.13', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.5 - 0.8 > 0.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.14', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.8 + 0.9 > 1.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.15', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.7 - 0.9 > 0.8');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.16', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 + 1.0 > 1.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.17', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.9 - 1.0 > 0.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.18', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.1 + 9999.2 > 19998.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.19', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.3 - 9999.2 > 9999.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.20', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.2 + 9999.3 > 19998.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.21', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.5 - 9999.3 > 9999.2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.22', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.3 + 9999.4 > 19998.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.23', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.7 - 9999.4 > 9999.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.24', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.4 + 9999.5 > 19998.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.25', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.9 - 9999.5 > 9999.4');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.26', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.5 + 9999.6 > 19999.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.27', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.1 - 9999.6 > 9999.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.28', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.6 + 9999.7 > 19999.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.29', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.3 - 9999.7 > 9999.6');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.30', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.7 + 9999.8 > 19999.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.31', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.5 - 9999.8 > 9999.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.32', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.8 + 9999.9 > 19999.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.33', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.7 - 9999.9 > 9999.8');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.34', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.9 + 10000.0 > 19999.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.35', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.9 - 10000.0 > 9999.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('E.36', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);



        // TEST: greater-than-or-equal-to comparison involving calculations

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.1 + 0.2 >= 0.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.1', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 - 0.2 >= 0.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.2', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.2 + 0.3 >= 0.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.3', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 - 0.3 >= 0.2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.4', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 + 0.4 >= 0.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.5', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 - 0.4 >= 0.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.6', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.4 + 0.5 >= 0.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.7', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 - 0.5 >= 0.4');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.8', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 + 0.6 >= 1.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.9', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1 - 0.6 >= 0.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.10', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.6 + 0.7 >= 1.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.11', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.3 - 0.7 >= 0.6');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.12', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 + 0.8 >= 1.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.13', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.5 - 0.8 >= 0.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.14', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.8 + 0.9 >= 1.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.15', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.7 - 0.9 >= 0.8');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.16', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 + 1.0 >= 1.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.17', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.9 - 1.0 >= 0.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.18', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.1 + 9999.2 >= 19998.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.19', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.3 - 9999.2 >= 9999.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.20', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.2 + 9999.3 >= 19998.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.21', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.5 - 9999.3 >= 9999.2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.22', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.3 + 9999.4 >= 19998.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.23', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.7 - 9999.4 >= 9999.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.24', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.4 + 9999.5 >= 19998.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.25', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.9 - 9999.5 >= 9999.4');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.26', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.5 + 9999.6 >= 19999.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.27', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.1 - 9999.6 >= 9999.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.28', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.6 + 9999.7 >= 19999.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.29', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.3 - 9999.7 >= 9999.6');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.30', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.7 + 9999.8 >= 19999.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.31', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.5 - 9999.8 >= 9999.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.32', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.8 + 9999.9 >= 19999.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.33', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.7 - 9999.9 >= 9999.8');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.34', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.9 + 10000.0 >= 19999.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.35', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.9 - 10000.0 >= 9999.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('F.36', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);



        // TEST: less-than comparison involving calculations

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.1 + 0.2 < 0.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.1', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 - 0.2 < 0.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.2', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.2 + 0.3 < 0.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.3', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 - 0.3 < 0.2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.4', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 + 0.4 < 0.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.5', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 - 0.4 < 0.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.6', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.4 + 0.5 < 0.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.7', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 - 0.5 < 0.4');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.8', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 + 0.6 < 1.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.9', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1 - 0.6 < 0.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.10', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.6 + 0.7 < 1.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.11', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.3 - 0.7 < 0.6');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.12', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 + 0.8 < 1.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.13', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.5 - 0.8 < 0.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.14', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.8 + 0.9 < 1.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.15', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.7 - 0.9 < 0.8');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.16', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 + 1.0 < 1.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.17', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.9 - 1.0 < 0.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.18', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.1 + 9999.2 < 19998.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.19', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.3 - 9999.2 < 9999.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.20', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.2 + 9999.3 < 19998.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.21', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.5 - 9999.3 < 9999.2');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.22', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.3 + 9999.4 < 19998.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.23', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.7 - 9999.4 < 9999.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.24', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.4 + 9999.5 < 19998.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.25', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.9 - 9999.5 < 9999.4');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.26', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.5 + 9999.6 < 19999.1');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.27', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.1 - 9999.6 < 9999.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.28', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.6 + 9999.7 < 19999.3');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.29', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.3 - 9999.7 < 9999.6');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.30', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.7 + 9999.8 < 19999.5');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.31', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.5 - 9999.8 < 9999.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.32', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.8 + 9999.9 < 19999.7');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.33', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.7 - 9999.9 < 9999.8');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.34', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.9 + 10000.0 < 19999.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.35', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.9 - 10000.0 < 9999.9');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('G.36', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);



        // TEST: less-than-or-equal-to comparison involving calculations

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.1 + 0.2 <= 0.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.1', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 - 0.2 <= 0.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.2', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.2 + 0.3 <= 0.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.3', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 - 0.3 <= 0.2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.4', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.3 + 0.4 <= 0.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.5', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 - 0.4 <= 0.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.6', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.4 + 0.5 <= 0.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.7', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 - 0.5 <= 0.4');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.8', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.5 + 0.6 <= 1.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.9', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.1 - 0.6 <= 0.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.10', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.6 + 0.7 <= 1.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.11', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.3 - 0.7 <= 0.6');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.12', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.7 + 0.8 <= 1.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.13', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.5 - 0.8 <= 0.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.14', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.8 + 0.9 <= 1.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.15', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.7 - 0.9 <= 0.8');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.16', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('0.9 + 1.0 <= 1.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.17', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('1.9 - 1.0 <= 0.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.18', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.1 + 9999.2 <= 19998.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.19', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.3 - 9999.2 <= 9999.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.20', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.2 + 9999.3 <= 19998.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.21', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.5 - 9999.3 <= 9999.2');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.22', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.3 + 9999.4 <= 19998.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.23', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.7 - 9999.4 <= 9999.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.24', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.4 + 9999.5 <= 19998.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.25', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19998.9 - 9999.5 <= 9999.4');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.26', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.5 + 9999.6 <= 19999.1');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.27', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.1 - 9999.6 <= 9999.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.28', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.6 + 9999.7 <= 19999.3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.29', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.3 - 9999.7 <= 9999.6');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.30', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.7 + 9999.8 <= 19999.5');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.31', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.5 - 9999.8 <= 9999.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.32', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.8 + 9999.9 <= 19999.7');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.33', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.7 - 9999.9 <= 9999.8');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.34', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('9999.9 + 10000.0 <= 19999.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.35', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Tests\Util::evalExpression('19999.9 - 10000.0 <= 9999.9');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('H.36', 'Expression; check for numeric accuracy when performing comparison invovling calculations',  $actual, $expected, $results);
    }
}
