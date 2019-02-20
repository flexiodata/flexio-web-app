<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-07-18
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
        // TEST: library installations

        // BEGIN TEST
        $actual = file_exists(\Flexio\System\System::getBinaryPath('php'));
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', 'Configuration; php command line must be installed; please install php7.0-cli', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded("mysqli");
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.2', 'Configuration; php mysql library must be installed; please install php7.0-mysqli', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded("pgsql");
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.3', 'Configuration; php postgres library must be installed; please install postgres', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded("sqlite3");
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.4', 'Configuration; php sqlite library must be installed; please install php7.0-sqlite', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded("pdo");
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.5', 'Configuration; php pdo library must be installed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded('ftp');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.6', 'Configuration; php ftp extension must be installed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded('curl');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.7', 'Configuration; php curl library must be installed; please install php7.0-curl', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded('zlib');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.8', 'Configuration; php zlip extension must be installed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded('gd');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.9', 'Configuration; php gd library must be installed; please install php7.0-gd', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded('mbstring');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.10', 'Configuration; php mbstring extension must be installed', $actual, $expected, $results);

        // BEGIN TEST
        $actual = extension_loaded('intl');
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.11', 'Configuration; php intl extension must be installed', $actual, $expected, $results);



        // TEST: php settings

        // BEGIN TEST
        $val = ini_get('post_max_size');
        $actual = \Flexio\Tests\Util::convertToNumber($val) >= 1048576000;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.1', 'Configuration; post_max_size must be 1000M or greater; current value is ' . $val, $actual, $expected, $results);

        // BEGIN TEST
        $val = ini_get('upload_max_filesize');
        $actual = \Flexio\Tests\Util::convertToNumber($val) >= 1048576000;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.2', 'Configuration; upload_max_filesize must be 1000M or greater; current value is ' . $val, $actual, $expected, $results);

        // BEGIN TEST
        $val = ini_get('memory_limit');
        $actual = \Flexio\Tests\Util::convertToNumber($val) >= 268435456;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.3', 'Configuration; memory_limit must be 256M or greater; current value is ' . $val, $actual, $expected, $results);

        // BEGIN TEST
        $val = ini_get('max_execution_time');
        $actual = \Flexio\Tests\Util::convertToNumber($val) >= 3600;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.4', 'Configuration; max_execution_time must be 3600 or greater; current value is ' . $val, $actual, $expected, $results);

        // BEGIN TEST
        $val = ini_get('max_input_time');
        $actual = \Flexio\Tests\Util::convertToNumber($val) >= 3600;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Configuration; max_input_time must be 3600 or greater; current value is ' . $val, $actual, $expected, $results);

        // BEGIN TEST
        $val = ini_get('magic_quotes_gpc');
        $actual = is_string($val) ? ($val === '0' ? false:true) : $val;
        $expected = false;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Configuration; magic_quotes_gpc must be set to false; current value is ' . $val, $actual, $expected, $results);

        // BEGIN TEST
        $val = ini_get('short_open_tag');
        $actual = is_string($val) ? ($val === '0' ? false:true) : $val;
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('B.5', 'Configuration; short_open_tag must be set to true; current value is ' . $val, $actual, $expected, $results);

    }
}

