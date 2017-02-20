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


class Test
{
    public function run(&$results)
    {
        // TEST: standard mime type constant tests

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_BMP;
        $expected = 'image/x-ms-bmp';
        TestCheck::assertString('A.1', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_CSS;
        $expected = 'text/css';
        TestCheck::assertString('A.2', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_CSV;
        $expected = 'text/csv';
        TestCheck::assertString('A.3', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_DOC;
        $expected = 'application/msword';
        TestCheck::assertString('A.4', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_DOCX;
        $expected = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        TestCheck::assertString('A.5', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_EMPTY;
        $expected = 'application/x-empty';
        TestCheck::assertString('A.6', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_GIF;
        $expected = 'image/gif';
        TestCheck::assertString('A.7', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_GZ;
        $expected = 'application/x-gzip';
        TestCheck::assertString('A.8', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_HTML;
        $expected = 'text/html';
        TestCheck::assertString('A.9', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_JPG;
        $expected = 'image/jpeg';
        TestCheck::assertString('A.10', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_JS;
        $expected = 'application/javascript';
        TestCheck::assertString('A.11', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_JSON;
        $expected = 'application/json';
        TestCheck::assertString('A.12', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_PDF;
        $expected = 'application/pdf';
        TestCheck::assertString('A.13', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_PNG;
        $expected = 'image/png';
        TestCheck::assertString('A.14', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_STREAM;
        $expected = 'application/octet-stream';
        TestCheck::assertString('A.15', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_SVG;
        $expected = 'image/svg+xml';
        TestCheck::assertString('A.16', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_TIFF;
        $expected = 'image/tiff';
        TestCheck::assertString('A.17', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_TXT;
        $expected = 'text/plain';
        TestCheck::assertString('A.18', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_XLS;
        $expected = 'application/vnd.ms-excel';
        TestCheck::assertString('A.19', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_XLSX;
        $expected = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        TestCheck::assertString('A.19', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_XML;
        $expected = 'application/xml';
        TestCheck::assertString('A.20', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_ZIP;
        $expected = 'application/zip';
        TestCheck::assertString('A.21', 'ContentType mime type constant',  $actual, $expected, $results);



        // TEST: flexio mime type constant tests

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_NONE;
        $expected = '';
        TestCheck::assertString('B.1', 'ContentType mime type constant',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = ContentType::MIME_TYPE_FLEXIO_TABLE;
        $expected = 'application/vnd.flexio.table';
        TestCheck::assertString('B.2', 'ContentType mime type constant',  $actual, $expected, $results);
    }
}
