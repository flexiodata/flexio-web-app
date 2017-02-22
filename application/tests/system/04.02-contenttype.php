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
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('','mime');
        $expected = 'mime';
        TestCheck::assertString('A.1', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('');
        $expected = \Flexio\System\ContentType::MIME_TYPE_STREAM;
        TestCheck::assertString('A.2', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.xxx');
        $expected = \Flexio\System\ContentType::MIME_TYPE_STREAM;
        TestCheck::assertString('A.3', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.csv');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.4', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.CSV');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.5', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('/test.csv');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.6', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('path/test.csv');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.7', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('/path/test.csv');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.8', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('\path\test.csv');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.9', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('http://www.url.com/test.csv');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.10', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('csv');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.11', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('.csv');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.12', '\Flexio\System\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);



        // TEST: mime type detection; different mime types

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.bmp');
        $expected = \Flexio\System\ContentType::MIME_TYPE_BMP;
        TestCheck::assertString('B.1', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.css');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSS;
        TestCheck::assertString('B.2', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.csv');
        $expected = \Flexio\System\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('B.3', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.gif');
        $expected = \Flexio\System\ContentType::MIME_TYPE_GIF;
        TestCheck::assertString('B.4', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.gz');
        $expected = \Flexio\System\ContentType::MIME_TYPE_GZ;
        TestCheck::assertString('B.5', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.html');
        $expected = \Flexio\System\ContentType::MIME_TYPE_HTML;
        TestCheck::assertString('B.6', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.htm');
        $expected = \Flexio\System\ContentType::MIME_TYPE_HTML;
        TestCheck::assertString('B.7', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.jpeg');
        $expected = \Flexio\System\ContentType::MIME_TYPE_JPG;
        TestCheck::assertString('B.8', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.jpg');
        $expected = \Flexio\System\ContentType::MIME_TYPE_JPG;
        TestCheck::assertString('B.9', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.js');
        $expected = \Flexio\System\ContentType::MIME_TYPE_JS;
        TestCheck::assertString('B.10', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.json');
        $expected = \Flexio\System\ContentType::MIME_TYPE_JSON;
        TestCheck::assertString('B.11', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.pdf');
        $expected = \Flexio\System\ContentType::MIME_TYPE_PDF;
        TestCheck::assertString('B.12', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.png');
        $expected = \Flexio\System\ContentType::MIME_TYPE_PNG;
        TestCheck::assertString('B.13', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.svg');
        $expected = \Flexio\System\ContentType::MIME_TYPE_SVG;
        TestCheck::assertString('B.14', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.tiff');
        $expected = \Flexio\System\ContentType::MIME_TYPE_TIFF;
        TestCheck::assertString('B.15', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.tif');
        $expected = \Flexio\System\ContentType::MIME_TYPE_TIFF;
        TestCheck::assertString('B.16', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.txt');
        $expected = \Flexio\System\ContentType::MIME_TYPE_TXT;
        TestCheck::assertString('B.17', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.xls');
        $expected = \Flexio\System\ContentType::MIME_TYPE_XLS;
        TestCheck::assertString('B.18', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.xlsx');
        $expected = \Flexio\System\ContentType::MIME_TYPE_XLSX;
        TestCheck::assertString('B.19', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.xml');
        $expected = \Flexio\System\ContentType::MIME_TYPE_XML;
        TestCheck::assertString('B.20', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\System\ContentType::getMimeTypeFromExtension('test.zip');
        $expected = \Flexio\System\ContentType::MIME_TYPE_ZIP;
        TestCheck::assertString('B.21', '\Flexio\System\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);
    }
}
