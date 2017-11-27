<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
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
        TestCheck::assertString('A.1', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_FLEXIO;
        $expected = 'flexio';
        TestCheck::assertString('A.2', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_FTP;
        $expected = 'ftp';
        TestCheck::assertString('A.3', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_SFTP;
        $expected = 'sftp';
        TestCheck::assertString('A.4', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_MYSQL;
        $expected = 'mysql';
        TestCheck::assertString('A.5', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_POSTGRES;
        $expected = 'postgres';
        TestCheck::assertString('A.6', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_ELASTICSEARCH;
        $expected = 'elasticsearch';
        TestCheck::assertString('A.7', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_DROPBOX;
        $expected = 'dropbox';
        TestCheck::assertString('A.8', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_GOOGLEDRIVE;
        $expected = 'googledrive';
        TestCheck::assertString('A.9', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_GOOGLESHEETS;
        $expected = 'googlesheets';
        TestCheck::assertString('A.10', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_GITHUB;
        $expected = 'github';
        TestCheck::assertString('A.11', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_AMAZONS3;
        $expected = 'amazons3';
        TestCheck::assertString('A.12', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_EMAIL;
        $expected = 'email';
        TestCheck::assertString('A.16', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_HTTP;
        $expected = 'http';
        TestCheck::assertString('A.17', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_RSS;
        $expected = 'rss';
        TestCheck::assertString('A.18', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_SOCRATA;
        $expected = 'socrata';
        TestCheck::assertString('A.19', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_PIPELINEDEALS;
        $expected = 'pipelinedeals';
        TestCheck::assertString('A.20', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_MAILJET;
        $expected = 'mailjet';
        TestCheck::assertString('A.21', 'Type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Services\Factory::TYPE_TWILIO;
        $expected = 'twilio';
        TestCheck::assertString('A.22', 'Type constant',  $actual, $expected, $results);
    }
}
