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


namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: mime type detection; different input paths

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('','mime');
        $expected = 'mime';
        TestCheck::assertString('A.1', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('');
        $expected = \ContentType::MIME_TYPE_STREAM;
        TestCheck::assertString('A.2', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.xxx');
        $expected = \ContentType::MIME_TYPE_STREAM;
        TestCheck::assertString('A.3', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.csv');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.4', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.CSV');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.5', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('/test.csv');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.6', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('path/test.csv');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.7', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('/path/test.csv');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.8', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('\path\test.csv');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.9', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('http://www.url.com/test.csv');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.10', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('csv');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.11', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('.csv');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.12', '\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);



        // TEST: mime type detection; different mime types

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.bmp');
        $expected = \ContentType::MIME_TYPE_BMP;
        TestCheck::assertString('B.1', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.css');
        $expected = \ContentType::MIME_TYPE_CSS;
        TestCheck::assertString('B.2', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.csv');
        $expected = \ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('B.3', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.gif');
        $expected = \ContentType::MIME_TYPE_GIF;
        TestCheck::assertString('B.4', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.gz');
        $expected = \ContentType::MIME_TYPE_GZ;
        TestCheck::assertString('B.5', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.html');
        $expected = \ContentType::MIME_TYPE_HTML;
        TestCheck::assertString('B.6', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.htm');
        $expected = \ContentType::MIME_TYPE_HTML;
        TestCheck::assertString('B.7', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.jpeg');
        $expected = \ContentType::MIME_TYPE_JPG;
        TestCheck::assertString('B.8', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.jpg');
        $expected = \ContentType::MIME_TYPE_JPG;
        TestCheck::assertString('B.9', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.js');
        $expected = \ContentType::MIME_TYPE_JS;
        TestCheck::assertString('B.10', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.json');
        $expected = \ContentType::MIME_TYPE_JSON;
        TestCheck::assertString('B.11', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.pdf');
        $expected = \ContentType::MIME_TYPE_PDF;
        TestCheck::assertString('B.12', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.png');
        $expected = \ContentType::MIME_TYPE_PNG;
        TestCheck::assertString('B.13', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.svg');
        $expected = \ContentType::MIME_TYPE_SVG;
        TestCheck::assertString('B.14', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.tiff');
        $expected = \ContentType::MIME_TYPE_TIFF;
        TestCheck::assertString('B.15', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.tif');
        $expected = \ContentType::MIME_TYPE_TIFF;
        TestCheck::assertString('B.16', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.txt');
        $expected = \ContentType::MIME_TYPE_TXT;
        TestCheck::assertString('B.17', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.xls');
        $expected = \ContentType::MIME_TYPE_XLS;
        TestCheck::assertString('B.18', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.xlsx');
        $expected = \ContentType::MIME_TYPE_XLSX;
        TestCheck::assertString('B.19', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.xml');
        $expected = \ContentType::MIME_TYPE_XML;
        TestCheck::assertString('B.20', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \ContentType::getMimeTypeFromExtension('test.zip');
        $expected = \ContentType::MIME_TYPE_ZIP;
        TestCheck::assertString('B.21', '\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);
    }
}
