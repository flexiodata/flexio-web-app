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
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('','mime');
        $expected = 'mime';
        TestCheck::assertString('A.1', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_STREAM;
        TestCheck::assertString('A.2', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.xxx');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_STREAM;
        TestCheck::assertString('A.3', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.csv');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.4', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.CSV');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.5', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('/test.csv');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.6', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('path/test.csv');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.7', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('/path/test.csv');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.8', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('\path\test.csv');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.9', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('http://www.url.com/test.csv');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.10', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('csv');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.11', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('.csv');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('A.12', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);



        // TEST: mime type detection; different mime types

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.bmp');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_BMP;
        TestCheck::assertString('B.1', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.css');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSS;
        TestCheck::assertString('B.2', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.csv');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_CSV;
        TestCheck::assertString('B.3', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.gif');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_GIF;
        TestCheck::assertString('B.4', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.gz');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_GZ;
        TestCheck::assertString('B.5', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.html');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_HTML;
        TestCheck::assertString('B.6', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.htm');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_HTML;
        TestCheck::assertString('B.7', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.jpeg');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_JPG;
        TestCheck::assertString('B.8', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.jpg');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_JPG;
        TestCheck::assertString('B.9', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.js');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_JS;
        TestCheck::assertString('B.10', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.json');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_JSON;
        TestCheck::assertString('B.11', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.pdf');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_PDF;
        TestCheck::assertString('B.12', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.png');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_PNG;
        TestCheck::assertString('B.13', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.svg');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_SVG;
        TestCheck::assertString('B.14', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.tiff');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_TIFF;
        TestCheck::assertString('B.15', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.tif');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_TIFF;
        TestCheck::assertString('B.16', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.txt');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_TXT;
        TestCheck::assertString('B.17', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.xls');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_XLS;
        TestCheck::assertString('B.18', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.xlsx');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_XLSX;
        TestCheck::assertString('B.19', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.xml');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_XML;
        TestCheck::assertString('B.20', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.zip');
        $expected = \Flexio\Base\ContentType::MIME_TYPE_ZIP;
        TestCheck::assertString('B.21', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);
    }
}
