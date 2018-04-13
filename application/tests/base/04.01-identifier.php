<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-17
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
        // TEST: type and length

        // BEGIN TEST
        $str = \Flexio\Base\Identifier::generate();
        $actual = is_string($str);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Identifier::generate(); test for string return type',  $actual, $expected, $results);

        // BEGIN TEST
        $str = \Flexio\Base\Identifier::generate();
        $actual = strlen($str) >= 10;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Identifier::generate(); test for length',  $actual, $expected, $results);



        // TEST: make sure identifier is valid

        // BEGIN TEST
        $str = \Flexio\Base\Identifier::generate();
        $actual = \Flexio\Base\Identifier::isValid($str);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Identifier::generate(); test for embedded uppercase',  $actual, $expected, $results);



        // TEST: check for some variation within multiple creations

        // BEGIN TEST
        $random_str = array();
        for ($i = 0; $i < 1000; $i++)
        {
            $str = \Flexio\Base\Identifier::generate();
            $random_str[$str] = 1;
        }
        $random_str_count = count($random_str);
        $actual = $random_str_count === 1000;  // we should have enough variation to have 1000 unique values
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Identifier::generate(); test for randomness',  $actual, $expected, $results);
    }
}
