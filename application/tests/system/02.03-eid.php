<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-04
 *
 * @package flexio
 * @subpackage Tests
 */


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: invalid characters; eids should only allow lowercase,
        // consonant, alphanumeric characters

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid(' xxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.1', '\Flexio\System\Eid::isValid() leading space', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxx xxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.2', '\Flexio\System\Eid::isValid() embedded space', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxxxxxxx ');
        $expected = false;
        TestCheck::assertBoolean('A.3', '\Flexio\System\Eid::isValid() trailing space', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxx xxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.4', '\Flexio\System\Eid::isValid() embedded space', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('axxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.5', '\Flexio\System\Eid::isValid() leading vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xexxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.6', '\Flexio\System\Eid::isValid() embedded vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxixxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.7', '\Flexio\System\Eid::isValid() embedded vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxxxxxxox');
        $expected = false;
        TestCheck::assertBoolean('A.8', '\Flexio\System\Eid::isValid() embedded vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxxxxxxxu');
        $expected = false;
        TestCheck::assertBoolean('A.9', '\Flexio\System\Eid::isValid() trailing vowel', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxxXxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.10', '\Flexio\System\Eid::isValid() embedded uppercase consonant', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxxXxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.11', '\Flexio\System\Eid::isValid() embedded uppercase consonant', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxxxxx.xx');
        $expected = false;
        TestCheck::assertBoolean('A.12', '\Flexio\System\Eid::isValid() embedded punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xx-xxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.13', '\Flexio\System\Eid::isValid() embedded punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('/xxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.14', '\Flexio\System\Eid::isValid() leading punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('\nxxxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.15', '\Flexio\System\Eid::isValid() leading punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('%20xxxxxxxxx');
        $expected = false;
        TestCheck::assertBoolean('A.16', '\Flexio\System\Eid::isValid() leading punctuation', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxxxxxxx/');
        $expected = false;
        TestCheck::assertBoolean('A.17', '\Flexio\System\Eid::isValid() trailing punctuation', $actual, $expected, $results);



        // TEST: invalid characters; eids should only allow lowercase,
        // consonant, alphanumeric characters

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('0xxxxxxxxxxx');
        $expected = true;
        TestCheck::assertBoolean('B.1', '\Flexio\System\Eid::isValid() leading zero', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxx0xxxxxx');
        $expected = true;
        TestCheck::assertBoolean('B.2', '\Flexio\System\Eid::isValid() embedded zero', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('xxxxxxxxxxx0');
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\System\Eid::isValid() trailing zero', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('000000000000');
        $expected = true;
        TestCheck::assertBoolean('B.4', '\Flexio\System\Eid::isValid() valid alphanumeric', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('1l1l1l1l1l1l');
        $expected = true;
        TestCheck::assertBoolean('B.5', '\Flexio\System\Eid::isValid() valid alphanumeric', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('0mz1b2qy9cl3');
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\System\Eid::isValid() valid alphanumeric', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('ywqlmsxklsck');
        $expected = true;
        TestCheck::assertBoolean('B.7', '\Flexio\System\Eid::isValid() valid alphanumeric', $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\Eid::isValid('1skvnlkd92bh');
        $expected = true;
        TestCheck::assertBoolean('B.8', '\Flexio\System\Eid::isValid() valid alphanumeric', $actual, $expected, $results);
    }
}
