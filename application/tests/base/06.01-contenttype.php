<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
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
        // TEST: standard mime type constant tests

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::BMP;
        $expected = 'image/x-ms-bmp';
        \Flexio\Tests\Check::assertString('A.1', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::CSS;
        $expected = 'text/css';
        \Flexio\Tests\Check::assertString('A.2', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::CSV;
        $expected = 'text/csv';
        \Flexio\Tests\Check::assertString('A.3', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::DOC;
        $expected = 'application/msword';
        \Flexio\Tests\Check::assertString('A.4', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::DOCX;
        $expected = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        \Flexio\Tests\Check::assertString('A.5', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::EMPTY;
        $expected = 'application/x-empty';
        \Flexio\Tests\Check::assertString('A.6', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::GIF;
        $expected = 'image/gif';
        \Flexio\Tests\Check::assertString('A.7', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::GZIP;
        $expected = 'application/x-gzip';
        \Flexio\Tests\Check::assertString('A.8', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::HTML;
        $expected = 'text/html';
        \Flexio\Tests\Check::assertString('A.9', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::JPEG;
        $expected = 'image/jpeg';
        \Flexio\Tests\Check::assertString('A.10', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::JAVASCRIPT;
        $expected = 'application/javascript';
        \Flexio\Tests\Check::assertString('A.11', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::JSON;
        $expected = 'application/json';
        \Flexio\Tests\Check::assertString('A.12', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::PDF;
        $expected = 'application/pdf';
        \Flexio\Tests\Check::assertString('A.13', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::PNG;
        $expected = 'image/png';
        \Flexio\Tests\Check::assertString('A.14', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::STREAM;
        $expected = 'application/octet-stream';
        \Flexio\Tests\Check::assertString('A.15', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::SVG;
        $expected = 'image/svg+xml';
        \Flexio\Tests\Check::assertString('A.16', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::TIFF;
        $expected = 'image/tiff';
        \Flexio\Tests\Check::assertString('A.17', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::TEXT;
        $expected = 'text/plain';
        \Flexio\Tests\Check::assertString('A.18', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::XLS;
        $expected = 'application/vnd.ms-excel';
        \Flexio\Tests\Check::assertString('A.19', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::XLSX;
        $expected = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        \Flexio\Tests\Check::assertString('A.19', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::XML;
        $expected = 'application/xml';
        \Flexio\Tests\Check::assertString('A.20', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::ZIP;
        $expected = 'application/zip';
        \Flexio\Tests\Check::assertString('A.21', '\ContentType mime type constant',  $actual, $expected, $results);



        // TEST: flexio mime type constant tests

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::UNDEFINED;
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', '\ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::FLEXIO_TABLE;
        $expected = 'application/vnd.flexio.table';
        \Flexio\Tests\Check::assertString('B.2', '\ContentType mime type constant',  $actual, $expected, $results);



        // TEST: extensions for mime types

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::BMP);
        $expected = 'bmp';
        \Flexio\Tests\Check::assertString('C.1', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::CSS);
        $expected = 'css';
        \Flexio\Tests\Check::assertString('C.2', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::DOC);
        $expected = 'doc';
        \Flexio\Tests\Check::assertString('C.3', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::DOCX);
        $expected = 'docx';
        \Flexio\Tests\Check::assertString('C.4', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::GIF);
        $expected = 'gif';
        \Flexio\Tests\Check::assertString('C.5', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::GZIP);
        $expected = 'gz';
        \Flexio\Tests\Check::assertString('C.6', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::HTML);
        $expected = 'html';
        \Flexio\Tests\Check::assertString('C.7', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::JPEG);
        $expected = 'jpg';
        \Flexio\Tests\Check::assertString('C.8', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::JAVASCRIPT);
        $expected = 'js';
        \Flexio\Tests\Check::assertString('C.9', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::JSON);
        $expected = 'json';
        \Flexio\Tests\Check::assertString('C.10', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::MARKDOWN);
        $expected = 'md';
        \Flexio\Tests\Check::assertString('C.11', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::PDF);
        $expected = 'pdf';
        \Flexio\Tests\Check::assertString('C.12', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::PNG);
        $expected = 'png';
        \Flexio\Tests\Check::assertString('C.13', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::SVG);
        $expected = 'svg';
        \Flexio\Tests\Check::assertString('C.14', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::TIFF);
        $expected = 'tif';
        \Flexio\Tests\Check::assertString('C.15', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::TEXT);
        $expected = 'txt';
        \Flexio\Tests\Check::assertString('C.16', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::XLS);
        $expected = 'xls';
        \Flexio\Tests\Check::assertString('C.17', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::XLSX);
        $expected = 'xlsx';
        \Flexio\Tests\Check::assertString('C.18', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::XML);
        $expected = 'xml';
        \Flexio\Tests\Check::assertString('C.19', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getExtensionFromMimeType(\Flexio\Base\ContentType::ZIP);
        $expected = 'zip';
        \Flexio\Tests\Check::assertString('C.20', '\Flexio\Base\ContentType::getExtensionFromMimeType();check extension for mime type',  $actual, $expected, $results);
    }
}
