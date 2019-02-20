<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-11-01
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


function getStreamFromFile($filename)
{
    // loads a test file with multipart content

    $testfilepath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'phpmimemailparser' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'mails';
    $testfile = $testfilepath . DIRECTORY_SEPARATOR . $filename;
    return file_get_contents($testfile);
}

function getStreamFromString($string)
{
    $stream = fopen('php://memory','r+');
    fwrite($stream, $string);
    rewind($stream);
    return $stream;
}


class Test
{
    public function run(&$results)
    {
        // TEST: \Flexio\Base\MultipartParser::create()

        // BEGIN TEST
        $parser = \Flexio\Base\MultipartParser::create();
        $actual = get_class($parser);
        $expected = 'Flexio\Base\MultipartParser';
        \Flexio\Tests\Check::assertString('A.1', '\Flexio\Base\MultipartParser::create(); empty input', $actual, $expected, $results);


        // TEST: \Flexio\Base\MultipartParser::parse()

        // BEGIN TEST
        $parser = \Flexio\Base\MultipartParser::create();
        $content = '';
        $parser->parse(false, '', function ($type, $name, $data, $filename, $content_type) use (&$content) {
            $content .= $data;
        });
        $actual = $content;
        $expected = '';
        \Flexio\Tests\Check::assertString('B.1', '\Flexio\Base\MultipartParser::create(); make sure parse function checks for valid resource', $actual, $expected, $results);

        // BEGIN TEST
        $test_info = getEmptyUpload();
        $stream = getStreamFromString(base64_decode($test_info['content_base64']));
        $parser = \Flexio\Base\MultipartParser::create();
        $file_content = '';
        $parser->parse($stream, $test_info['content_type'], function ($type, $name, $data, $filename, $content_type) use (&$file_content) {
            $file_content .= $data;
        });
        $actual = substr($file_content, 0, 1);
        $expected = '';
        \Flexio\Tests\Check::assertString('B.2', '\Flexio\Base\MultipartParser: basic test', $actual, $expected, $results);

        // BEGIN TEST
        $test_info = getSingleEmptyTextFileUpload();
        $stream = getStreamFromString(base64_decode($test_info['content_base64']));
        $parser = \Flexio\Base\MultipartParser::create();
        $file_content = '';
        $parser->parse($stream, $test_info['content_type'], function ($type, $name, $data, $filename, $content_type) use (&$file_content) {
            $file_content .= $data;
        });
        $actual = substr($file_content, 0, 1);
        $expected = '';
        \Flexio\Tests\Check::assertString('B.3', '\Flexio\Base\MultipartParser: basic test', $actual, $expected, $results);

        // BEGIN TEST
        $test_info = getSinglePopulatedTextFileUpload();
        $stream = getStreamFromString(base64_decode($test_info['content_base64']));
        $parser = \Flexio\Base\MultipartParser::create();
        $file_content = '';
        $parser->parse($stream, $test_info['content_type'], function ($type, $name, $data, $filename, $content_type) use (&$file_content) {
            $file_content .= $data;
        });
        $actual = substr($file_content, 0, 4);
        $expected = 'This';
        \Flexio\Tests\Check::assertString('B.4', '\Flexio\Base\MultipartParser: simple file upload test', $actual, $expected, $results);


        // BEGIN TEST
        $test_info = getMultiplePopulatedTextFileUpload();
        $stream = getStreamFromString(base64_decode($test_info['content_base64']));
        $parser = \Flexio\Base\MultipartParser::create();
        $names = '';
        $filenames = '';
        $content = '';
        $values = '';
        $parser->parse($stream, $test_info['content_type'], function ($type, $name, $data, $filename, $content_type) use (&$names, &$filenames, &$content, &$values) {
            if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_BEGIN)
            {
                $names .= (strlen($names)>0?',':'') . $name;
                $filenames .= (strlen($filenames)>0?',':'') . $filename;
            }
             else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_DATA)
            {
                $content .= (strlen($content)>0?',':'') . $data;
            }
             else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_END)
            {
                $content .= (strlen($content)>0?',':'') . 'DONE';
            }
             else if ($type == \Flexio\Base\MultipartParser::TYPE_KEY_VALUE)
            {
                $values .= "$name:$data";
            }
        });
        $actual = "$names;$filenames;$content;$values";
        $expected = "file1,file2,file3;basic-1.txt,basic-2.txt,basic-3.txt;This is test 1.,DONE,This is test 2.,DONE,This is test 4.,DONE;simplekeyvalue:This is test 3.";
        \Flexio\Tests\Check::assertString('B.5', '\Flexio\Base\MultipartParser: multiple-file upload', $actual, $expected, $results);

        // BEGIN TEST
        $test_info = getFormUrlEncodedPost();
        $stream = getStreamFromString("first=value&second=test+space&third=%C3%9Cberm%C3%A4%C3%9Fig");
        $parser = \Flexio\Base\MultipartParser::create();

        $values = '';
        $parser->parse($stream, $test_info['content_type'], function ($type, $name, $data, $filename, $content_type) use (&$values) {
            if ($type == \Flexio\Base\MultipartParser::TYPE_KEY_VALUE)
            {
                $values .= (strlen($values)>0?',':'') . "$name:$data";
            }
        });
        $actual = $values;
        $expected = "first:value,second:test space,third:Übermäßig";
        \Flexio\Tests\Check::assertString('B.6', '\Flexio\Base\MultipartParser: should be able to handle normal form url encoded strings', $actual, $expected, $results);
    }
}


function getFormUrlEncodedPost()
{
    $test_info = array();
    $test_info['content_type'] = 'application/x-www-form-urlencoded';
    return $test_info;
}


function getEmptyUpload()
{
    $test_info = array();
    $test_info['content_type'] = 'multipart/form-data; boundary=---------------------------25236431927533';
    $test_info['content_base64'] = 'LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0yNTIzNjQzMTkyNzUzMw0KQ29udGVudC1EaXNwb3NpdGlvbjogZm9ybS1kYXRhOyBuYW1lPSJmaWxlMSI7IGZpbGVuYW1lPSIiDQpDb250ZW50LVR5cGU6IGFwcGxpY2F0aW9uL29jdGV0LXN0cmVhbQ0KDQoNCi0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tMjUyMzY0MzE5Mjc1MzMtLQ0K';
    $test_info['content'] = <<<EOD
-----------------------------25236431927533
Content-Disposition: form-data; name="file1"; filename=""
Content-Type: application/octet-stream


-----------------------------25236431927533--

EOD;
    return $test_info;
}


function getSingleEmptyTextFileUpload()
{
    $test_info = array();
    $test_info['content_type'] = 'multipart/form-data; boundary=---------------------------161441858918778';
    $test_info['content_base64'] = 'LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0xNjE0NDE4NTg5MTg3NzgNCkNvbnRlbnQtRGlzcG9zaXRpb246IGZvcm0tZGF0YTsgbmFtZT0iZmlsZTEiOyBmaWxlbmFtZT0iYmFzaWMtMS50eHQiDQpDb250ZW50LVR5cGU6IHRleHQvcGxhaW4NCg0KDQotLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLTE2MTQ0MTg1ODkxODc3OC0tDQo=';
    $test_info['content'] = <<<EOD
-----------------------------161441858918778
Content-Disposition: form-data; name="file1"; filename="basic-1.txt"
Content-Type: text/plain


-----------------------------161441858918778--

EOD;
    return $test_info;
}


function getSinglePopulatedTextFileUpload()
{
    $test_info = array();
    $test_info['content_type'] = 'multipart/form-data; boundary=---------------------------171271002131529';
    $test_info['content_base64'] = 'LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0xNzEyNzEwMDIxMzE1MjkNCkNvbnRlbnQtRGlzcG9zaXRpb246IGZvcm0tZGF0YTsgbmFtZT0iZmlsZTEiOyBmaWxlbmFtZT0iYmFzaWMtMi50eHQiDQpDb250ZW50LVR5cGU6IHRleHQvcGxhaW4NCg0KVGhpcyBpcyBhIHRlc3QuDQotLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLTE3MTI3MTAwMjEzMTUyOS0tDQo=';
    $test_info['content'] = <<<EOD
-----------------------------171271002131529
Content-Disposition: form-data; name="file1"; filename="basic-2.txt"
Content-Type: text/plain

This is a test.
-----------------------------171271002131529--

EOD;
    return $test_info;
}


function getMultiplePopulatedTextFileUpload()
{
    $test_info = array();
    $test_info['content_type'] = 'multipart/form-data; boundary=---------------------------3222742238275';
    $test_info['content_base64'] = 'LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0zMjIyNzQyMjM4Mjc1DQpDb250ZW50LURpc3Bvc2l0aW9uOiBmb3JtLWRhdGE7IG5hbWU9ImZpbGUxIjsgZmlsZW5hbWU9ImJhc2ljL'.
    'TEudHh0Ig0KQ29udGVudC1UeXBlOiB0ZXh0L3BsYWluDQoNClRoaXMgaXMgdGVzdCAxLg0KLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0zMjIyNzQyMjM4Mjc1DQpDb250ZW50LURpc3Bvc2l0aW9uOiBmb3JtLWRhdGE7I'.
    'G5hbWU9ImZpbGUyIjsgZmlsZW5hbWU9ImJhc2ljLTIudHh0Ig0KQ29udGVudC1UeXBlOiB0ZXh0L3BsYWluDQoNClRoaXMgaXMgdGVzdCAyLg0KLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0zMjIyNzQyMjM4Mjc1DQpDb'.
    '250ZW50LURpc3Bvc2l0aW9uOiBmb3JtLWRhdGE7IG5hbWU9InNpbXBsZWtleXZhbHVlIg0KDQpUaGlzIGlzIHRlc3QgMy4NCi0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tMzIyMjc0MjIzODI3NQ0KQ29udGVudC1EaXNwb'.
    '3NpdGlvbjogZm9ybS1kYXRhOyBuYW1lPSJmaWxlMyI7IGZpbGVuYW1lPSJiYXNpYy0zLnR4dCINCkNvbnRlbnQtVHlwZTogdGV4dC9wbGFpbg0KDQpUaGlzIGlzIHRlc3QgNC4NCi0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tL'.
    'S0tMzIyMjc0MjIzODI3NS0tDQo=';
    $test_info['content'] = <<<EOD
-----------------------------3222742238275
Content-Disposition: form-data; name="file1"; filename="basic-1.txt"
Content-Type: text/plain

This is test 1.
-----------------------------3222742238275
Content-Disposition: form-data; name="file2"; filename="basic-2.txt"
Content-Type: text/plain

This is test 2.
-----------------------------3222742238275
Content-Disposition: form-data; name="simplekeyvalue"

This is test 3.
-----------------------------3222742238275
Content-Disposition: form-data; name="file3"; filename="basic-3.txt"
Content-Type: text/plain

This is test 4.
-----------------------------3222742238275--

EOD;
    return $test_info;
}

