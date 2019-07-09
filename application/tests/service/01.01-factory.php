<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-27
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
        // TEST: service types

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('A.1', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_FLEXIO;
        $expected = 'flexio';
        \Flexio\Tests\Check::assertString('A.2', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_FTP;
        $expected = 'ftp';
        \Flexio\Tests\Check::assertString('A.3', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_SFTP;
        $expected = 'sftp';
        \Flexio\Tests\Check::assertString('A.4', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_MYSQL;
        $expected = 'mysql';
        \Flexio\Tests\Check::assertString('A.5', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_POSTGRES;
        $expected = 'postgres';
        \Flexio\Tests\Check::assertString('A.6', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_ELASTICSEARCH;
        $expected = 'elasticsearch';
        \Flexio\Tests\Check::assertString('A.7', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_DROPBOX;
        $expected = 'dropbox';
        \Flexio\Tests\Check::assertString('A.8', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_GOOGLEDRIVE;
        $expected = 'googledrive';
        \Flexio\Tests\Check::assertString('A.9', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_GOOGLESHEETS;
        $expected = 'googlesheets';
        \Flexio\Tests\Check::assertString('A.10', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_GITHUB;
        $expected = 'github';
        \Flexio\Tests\Check::assertString('A.11', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_AMAZONS3;
        $expected = 'amazons3';
        \Flexio\Tests\Check::assertString('A.12', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_EMAIL;
        $expected = 'email';
        \Flexio\Tests\Check::assertString('A.16', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_HTTP;
        $expected = 'http';
        \Flexio\Tests\Check::assertString('A.17', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_SOCRATA;
        $expected = 'socrata';
        \Flexio\Tests\Check::assertString('A.19', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_PIPELINEDEALS;
        $expected = 'pipelinedeals';
        \Flexio\Tests\Check::assertString('A.20', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_MAILJET;
        $expected = 'mailjet';
        \Flexio\Tests\Check::assertString('A.21', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_TWILIO;
        $expected = 'twilio';
        \Flexio\Tests\Check::assertString('A.22', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_KEYRING;
        $expected = 'keyring';
        \Flexio\Tests\Check::assertString('A.23', 'Type constant',  $actual, $expected, $results);
    }
}
