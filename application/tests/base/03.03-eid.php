<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-04
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
        // TEST: invalid characters; eids should only allow lowercase,
        // consonant, alphanumeric characters

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid(' xxxxxxxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Eid::isValid() leading space', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxx xxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.2', '\Flexio\Base\Eid::isValid() embedded space', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxx ');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.3', '\Flexio\Base\Eid::isValid() trailing space', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxx xxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.4', '\Flexio\Base\Eid::isValid() embedded space', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('axxxxxxxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.5', '\Flexio\Base\Eid::isValid() leading vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xexxxxxxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.6', '\Flexio\Base\Eid::isValid() embedded vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxixxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.7', '\Flexio\Base\Eid::isValid() embedded vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxox');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.8', '\Flexio\Base\Eid::isValid() embedded vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxxu');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.9', '\Flexio\Base\Eid::isValid() trailing vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxXxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.10', '\Flexio\Base\Eid::isValid() embedded uppercase consonant', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxXxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.11', '\Flexio\Base\Eid::isValid() embedded uppercase consonant', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxx.xx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.12', '\Flexio\Base\Eid::isValid() embedded punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xx-xxxxxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.13', '\Flexio\Base\Eid::isValid() embedded punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('/xxxxxxxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.14', '\Flexio\Base\Eid::isValid() leading punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('\nxxxxxxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.15', '\Flexio\Base\Eid::isValid() leading punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('%20xxxxxxxxx');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.16', '\Flexio\Base\Eid::isValid() leading punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxx/');
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('A.17', '\Flexio\Base\Eid::isValid() trailing punctuation', $actual, $expected, $results);



        // TEST: invalid characters; eids should only allow lowercase,
        // consonant, alphanumeric characters

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('0xxxxxxxxxxx');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', '\Flexio\Base\Eid::isValid() leading zero', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxx0xxxxxx');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', '\Flexio\Base\Eid::isValid() embedded zero', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('xxxxxxxxxxx0');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', '\Flexio\Base\Eid::isValid() trailing zero', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('000000000000');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', '\Flexio\Base\Eid::isValid() valid alphanumeric', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('1l1l1l1l1l1l');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', '\Flexio\Base\Eid::isValid() valid alphanumeric', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('0mz1b2qy9cl3');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.6', '\Flexio\Base\Eid::isValid() valid alphanumeric', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('ywqlmsxklsck');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.7', '\Flexio\Base\Eid::isValid() valid alphanumeric', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\Eid::isValid('1skvnlkd92bh');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.8', '\Flexio\Base\Eid::isValid() valid alphanumeric', $actual, $expected, $results);
    }
}
