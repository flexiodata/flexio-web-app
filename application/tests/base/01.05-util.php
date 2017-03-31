<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-05
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
        // TEST: encryption

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV1KcusVh1gSlH3FlRIf052sQ==';
        $str = \Flexio\Base\Util::oldEncrypt($unencrypted_str, 'abcdefg');
        $actual = ($str === $encrypted_str_key);
        $expected = true;
        TestCheck::assertBoolean('A.1', '\Flexio\Base\Util::encrypt() test for string encryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV1XXyq+LSvbPvbTkMQ4h1BcQ==';
        $str = \Flexio\Base\Util::oldEncrypt($unencrypted_str, 'hijklmn');
        $actual = ($str === $encrypted_str_key);
        $expected = true;
        TestCheck::assertBoolean('A.2', '\Flexio\Base\Util::encrypt() test for string encryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV2/RGvUbYrs9C+KR98EAOufKZT6UuK45HYzLqLjYprI1w==';
        $str = \Flexio\Base\Util::encrypt($unencrypted_str, 'abcdefg');
        $actual = ($str === $encrypted_str_key);
        $expected = true;
        TestCheck::assertBoolean('A.3', '\Flexio\Base\Util::encrypt() test for string encryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV2/NRVm93HeCeUxYV8UI7qx4k1jEWyZUr5ZezMHrDvtmw==';
        $str = \Flexio\Base\Util::encrypt($unencrypted_str, 'hijklmn');
        $actual = ($str === $encrypted_str_key);
        $expected = true;
        TestCheck::assertBoolean('A.4', '\Flexio\Base\Util::encrypt() test for string encryption',  $actual, $expected, $results);



        // TEST: decryption

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV1KcusVh1gSlH3FlRIf052sQ==';
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, ''); // encrypted with key 'abcdefg' using mycrypt implementation
        $actual = ($str === $unencrypted_str);
        $expected = false;
        TestCheck::assertBoolean('B.1', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV1KcusVh1gSlH3FlRIf052sQ==';
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, 'a'); // encrypted with key 'abcdefg' using mycrypt implementation
        $actual = ($str === $unencrypted_str);
        $expected = false;
        TestCheck::assertBoolean('B.2', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV1KcusVh1gSlH3FlRIf052sQ==';
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, 'abcdefg'); // encrypted with key 'abcdefg' using mycrypt implementation
        $actual = ($str === $unencrypted_str);
        $expected = true;
        TestCheck::assertBoolean('B.3', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV1XXyq+LSvbPvbTkMQ4h1BcQ==';
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, ''); // encrypted with key 'hijklmn' using mycrypt implementation
        $actual = ($str === $unencrypted_str);
        $expected = false;
        TestCheck::assertBoolean('B.4', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV1XXyq+LSvbPvbTkMQ4h1BcQ=='; // encrypted with key 'hijklmn' using mycrypt implementation
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, 'h');
        $actual = ($str === $unencrypted_str);
        $expected = false;
        TestCheck::assertBoolean('B.5', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV1XXyq+LSvbPvbTkMQ4h1BcQ=='; // encrypted with key 'hijklmn' using mycrypt implementation
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, 'hijklmn');
        $actual = ($str === $unencrypted_str);
        $expected = true;
        TestCheck::assertBoolean('B.6', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);



        // TEST: decryption

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV2/RGvUbYrs9C+KR98EAOufKZT6UuK45HYzLqLjYprI1w==';
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, ''); // encrypted with key 'abcdefg' using sodium implementation
        $actual = ($str === $unencrypted_str);
        $expected = false;
        TestCheck::assertBoolean('C.1', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV2/RGvUbYrs9C+KR98EAOufKZT6UuK45HYzLqLjYprI1w==';
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, 'a'); // encrypted with key 'abcdefg' using sodium implementation
        $actual = ($str === $unencrypted_str);
        $expected = false;
        TestCheck::assertBoolean('C.2', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV2/RGvUbYrs9C+KR98EAOufKZT6UuK45HYzLqLjYprI1w==';
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, 'abcdefg'); // encrypted with key 'abcdefg' using sodium implementation
        $actual = ($str === $unencrypted_str);
        $expected = true;
        TestCheck::assertBoolean('C.3', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV2/NRVm93HeCeUxYV8UI7qx4k1jEWyZUr5ZezMHrDvtmw==';
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, ''); // encrypted with key 'hijklmn' using sodium implementation
        $actual = ($str === $unencrypted_str);
        $expected = false;
        TestCheck::assertBoolean('C.4', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV2/NRVm93HeCeUxYV8UI7qx4k1jEWyZUr5ZezMHrDvtmw=='; // encrypted with key 'hijklmn' using sodium implementation
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, 'h');
        $actual = ($str === $unencrypted_str);
        $expected = false;
        TestCheck::assertBoolean('C.5', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);

        // BEGIN TEST
        $unencrypted_str = 'ENCRYPTION TEST';
        $encrypted_str_key = 'ZZXV2/NRVm93HeCeUxYV8UI7qx4k1jEWyZUr5ZezMHrDvtmw=='; // encrypted with key 'hijklmn' using sodium implementation
        $str = \Flexio\Base\Util::decrypt($encrypted_str_key, 'hijklmn');
        $actual = ($str === $unencrypted_str);
        $expected = true;
        TestCheck::assertBoolean('C.6', '\Flexio\Base\Util::decrypt() test for string decryption',  $actual, $expected, $results);
    }
}
