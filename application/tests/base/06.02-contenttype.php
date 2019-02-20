<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
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
        // TEST: mime type detection; different input paths

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('','mime');
        $expected = 'mime';
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('');
        $expected = \Flexio\Base\ContentType::STREAM;
        \Flexio\Tests\Check::assertString('A.2', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.xxx');
        $expected = \Flexio\Base\ContentType::STREAM;
        \Flexio\Tests\Check::assertString('A.3', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.csv');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('A.4', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.CSV');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('A.5', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('/test.csv');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('A.6', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('path/test.csv');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('A.7', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('/path/test.csv');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('A.8', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('\path\test.csv');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('A.9', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('http://www.url.com/test.csv');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('A.10', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('csv');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('A.11', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('.csv');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('A.12', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different input paths',  $actual, $expected, $results);



        // TEST: mime type detection; different mime types

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.bmp');
        $expected = \Flexio\Base\ContentType::BMP;
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.css');
        $expected = \Flexio\Base\ContentType::CSS;
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.csv');
        $expected = \Flexio\Base\ContentType::CSV;
        \Flexio\Tests\Check::assertString('B.3', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.gif');
        $expected = \Flexio\Base\ContentType::GIF;
        \Flexio\Tests\Check::assertString('B.4', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.gz');
        $expected = \Flexio\Base\ContentType::GZIP;
        \Flexio\Tests\Check::assertString('B.5', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.html');
        $expected = \Flexio\Base\ContentType::HTML;
        \Flexio\Tests\Check::assertString('B.6', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.htm');
        $expected = \Flexio\Base\ContentType::HTML;
        \Flexio\Tests\Check::assertString('B.7', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.jpeg');
        $expected = \Flexio\Base\ContentType::JPEG;
        \Flexio\Tests\Check::assertString('B.8', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.jpg');
        $expected = \Flexio\Base\ContentType::JPEG;
        \Flexio\Tests\Check::assertString('B.9', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.js');
        $expected = \Flexio\Base\ContentType::JAVASCRIPT;
        \Flexio\Tests\Check::assertString('B.10', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.json');
        $expected = \Flexio\Base\ContentType::JSON;
        \Flexio\Tests\Check::assertString('B.11', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.pdf');
        $expected = \Flexio\Base\ContentType::PDF;
        \Flexio\Tests\Check::assertString('B.12', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.png');
        $expected = \Flexio\Base\ContentType::PNG;
        \Flexio\Tests\Check::assertString('B.13', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.svg');
        $expected = \Flexio\Base\ContentType::SVG;
        \Flexio\Tests\Check::assertString('B.14', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.tiff');
        $expected = \Flexio\Base\ContentType::TIFF;
        \Flexio\Tests\Check::assertString('B.15', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.tif');
        $expected = \Flexio\Base\ContentType::TIFF;
        \Flexio\Tests\Check::assertString('B.16', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.txt');
        $expected = \Flexio\Base\ContentType::TEXT;
        \Flexio\Tests\Check::assertString('B.17', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.xls');
        $expected = \Flexio\Base\ContentType::XLS;
        \Flexio\Tests\Check::assertString('B.18', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.xlsx');
        $expected = \Flexio\Base\ContentType::XLSX;
        \Flexio\Tests\Check::assertString('B.19', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.xml');
        $expected = \Flexio\Base\ContentType::XML;
        \Flexio\Tests\Check::assertString('B.20', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);

        // BEGIN TEST
        $actual = \Flexio\Base\ContentType::getMimeTypeFromExtension('test.zip');
        $expected = \Flexio\Base\ContentType::ZIP;
        \Flexio\Tests\Check::assertString('B.21', '\Flexio\Base\ContentType::getMimeTypeFromExtension() different mime types',  $actual, $expected, $results);
    }
}
