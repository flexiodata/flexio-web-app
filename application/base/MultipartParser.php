<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-11-01
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class MultipartParser
{
    public static function create($params = false) : \Flexio\Base\MultipartParser
    {
        $parser = new self;
        return $parser;
    }

    public const TYPE_FILE_BEGIN = 1;
    public const TYPE_FILE_DATA = 2;
    public const TYPE_FILE_END = 3;
    public const TYPE_KEY_VALUE = 4;

    // callback should be a function like this
    // function callback($type, $name, $data, $filename, $content_type)
    // when type equals TYPE_FILE_BEGIN - file starting
    //                  TYPE_FILE_DATA  - file data
    //                  TYPE_FILE_END   - file finished
    //                  TYPE_KEY_VALUE  - simple key/value (in $name/$data)

    public function parse($resource, $header_content_type, $callback) : bool
    {
        if (is_resource($resource) === false)
            return false;

        // STEP 1: get multipart boundary from the content_type header value
        $matches = [];
        preg_match('/boundary=(.*)$/', $header_content_type, $matches);
        if (count($matches) < 1)
        {
            // no boundary -- do a simple application/x-www-form-urlencoded decode
            $str = stream_get_contents($resource, 65536);

            $arr = array();
            parse_str($str, $arr);
            foreach ($arr as $key => $value)
            {
                $callback(self::TYPE_KEY_VALUE,
                        $key,
                        $value,
                        false,
                        false);
            }
            return true;
        }

        $boundary = "--" . $matches[1];
        $boundary_len = strlen($boundary);

        $current_name = false;
        $current_filename = false;
        $current_mimetype = false;
        $buf = '';
        $first = true;

        while (true)
        {
            // find boundary; if we can't find a boundary, read more data
            $boundary_pos = strpos($buf, $boundary);
            if ($boundary_pos === false)
            {
                $data = fread($resource, 8192);
                //var_dump($data);
                $eof = (strlen($data) == 0 || $data === false);
                $buf .= $data;

                // find boundary
                $boundary_pos = strpos($buf, $boundary);
            }

            if ($boundary_pos === false)
            {
                // no boundary found
                $buf_len = strlen($buf);

                if ($buf_len > 200) // 200 is an arbitrary overlap value
                {
                    // send data to the callback minus overlap
                    if ($current_filename !== false && strlen($current_filename) > 0)
                    {
                        $callback(self::TYPE_FILE_DATA, $current_name, substr($buf, 0, $buf_len - 200), $current_filename, $current_mimetype);
                    }

                    $buf = substr($buf, $buf_len - 200);
                }
                else
                {
                    if ($eof)
                    {
                        // end reached -- send rest
                        if ($current_filename !== false && strlen($current_filename) > 0)
                        {
                            $callback(self::TYPE_FILE_DATA, $current_name, $buf, $current_filename, $current_mimetype);
                            $buf = '';
                            $callback(self::TYPE_FILE_END, $current_name, '', $current_filename, $current_mimetype);
                        }
                        return true;
                    }
                    else
                    {
                        // wait for more data?
                    }
                }

                continue;
            }


            // boundary found
            if ($first)
            {
                $first = false; // put \r\n at the front for all subsequent boundary searches
                $boundary = "\r\n" . $boundary;
            }

            // first, flush the buffer up to the boundary

            if ($current_name !== false)
            {
                $is_file = $current_filename !== false && strlen($current_filename) > 0;
                $callback($is_file ? self::TYPE_FILE_DATA : self::TYPE_KEY_VALUE,
                        $current_name,
                        substr($buf, 0, $boundary_pos),
                        $current_filename,
                        $current_mimetype);

                if ($is_file)
                {
                    $callback(self::TYPE_FILE_END, $current_name, '', $current_filename, $current_mimetype);
                }
            }

            $buf = substr($buf, $boundary_pos);

            $header_len = strpos($buf, "\r\n\r\n");
            if ($header_len === false)
                return false; // couldn't find end of headers

            $header = substr($buf, 0, $header_len);
            $buf = substr($buf, $header_len + 4);


            $name = false;
            $filename = false;
            $mimetype = false;

            $matches = array();
            if (preg_match('/name="(.*?)"/i', $header, $matches))
                $name = trim($matches[1]);
            $matches = array();
            if (preg_match('/filename="(.*?)"/i', $header, $matches))
                $filename = trim($matches[1]);
            $matches = array();
            if (preg_match('/Content-Type:\s*([^;\s]+)/i', $header, $matches))
                $mimetype = trim($matches[1]);

            $current_name = $name;
            $current_filename = $filename;
            $current_mimetype = $mimetype;

           // echo "name:$name filename:$filename mime_type:$mimetype<br>";
            if ($filename !== false && strlen($filename) > 0)
                $callback(self::TYPE_FILE_BEGIN, $name, '', $filename, $mimetype);
        }

        return true;
    }



/*
    public function parse($resource, $header_content_type, $callback)
    {
        // TODO: following parse code assumes form data with a single file
        // and no other post parameters; these needs to be adapted to handle
        // multiple files and post parameters

        if (is_resource($resource) === false)
            return false;

        // STEP 1: get multipart boundary from the content_type header value
        $matches = [];
        preg_match('/boundary=(.*)$/', $header_content_type, $matches);
        if (count($matches) < 1)
            return false;
        $boundary = "\r\n--" . $matches[1];

        // STEP 2: get the file info from the post parameters
        $data = fread($resource, 32768);

        $name = false;
        $filename = false;
        $mime_type = false;

        $matches = array();
        if (preg_match('/name="(.*?)"/i', $data, $matches))
            $name = trim($matches[1]);
        $matches = array();
        if (preg_match('/filename="(.*?)"/i', $data, $matches))
            $filename = trim($matches[1]);
        $matches = array();
        if (preg_match('/Content-Type:\s*(.*?)/i', $data, $matches))
            $mime_type = trim($matches[1]);

        // we at least need to have a filename
        if ($filename === false)
            return false;

        // get the file data
        $pos = strpos($data, 'filename="');
        if ($pos !== false)
            $pos = strpos($data, "\r\n\r\n", $pos);
        if ($pos === false)
            return false;
        $data = substr($data, $pos+4);


        // STEP 3: write out the data to the callback
        while ($data)
        {
            // look for boundary
            $pos = strpos($data, $boundary);
            if ($pos !== false)
                $data = substr($data, 0, $pos);

            if (strlen($data) > 0)
                $callback($data);

            if ($pos !== false)
                break; // end of stream found

            $data = fread($resource, 32768);
        }

        $file_info = array();
        $file_info['name'] = $name !== false ? $name : '';
        $file_info['filename'] = $filename !== false ? $filename : '';
        $file_info['mime_type'] = $mime_type !== false ? $mime_type : '';

        return $file_info;
    }
*/

}
