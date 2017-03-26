<?php
/**
 *
 * Copyright (c) 2014-2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-25
 *
 * @package flexio
 * @subpackage Api
 */


namespace Flexio\Api;


class Stream
{
    public static function create($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid_status'    => array('type' => 'string', 'required' => false),
                'name'          => array('type' => 'string',  'required' => false),
                'size'          => array('type' => 'integer', 'required' => false),
                'mime_type'     => array('type' => 'string',  'required' => false),
                'file_created'  => array('type' => 'string',  'required' => false), // TODO: date type?
                'file_modified' => array('type' => 'string',  'required' => false)  // TODO: date type?
            ))) === false)
            return $request->getValidator()->fail();

        // create the stream
        $stream = \Flexio\Object\Stream::create($params); // the \Flexio\Object\Stream::create() creates a default connection and path

        // return the stream info
        return $stream->get();
    }

    public static function set($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid'           => array('type' => 'identifier', 'required' => true),
                'eid_status'    => array('type' => 'string', 'required' => false),
                'name'          => array('type' => 'string',  'required' => false),
                'size'          => array('type' => 'integer', 'required' => false),
                'mime_type'     => array('type' => 'string',  'required' => false),
                'file_created'  => array('type' => 'string',  'required' => false), // TODO: date type?
                'file_modified' => array('type' => 'string',  'required' => false)  // TODO: date type?

            ))) === false)
            return $request->getValidator()->fail();

        $stream_identifier = $params['eid'];

        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream !== false)
            $stream->set($params);

        return $stream->get();
    }

    public static function get($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))) === false)
            return $request->getValidator()->fail();

        $stream_identifier = $params['eid'];
        $requesting_user_eid = $request->getRequestingUser();

        // check the rights on the object
        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream === false)
            return $request->getValidator()->fail(\Flexio\Base\Error::NO_OBJECT);

        // TODO: re-add
        //if ($stream->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
        //    return $request->getValidator()->fail(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return $stream->get();
    }

    public static function content($params, $request)
    {
        if (($params = $request->getValidator()->check($params, array(
                'eid'      => array('type' => 'identifier', 'required' => true),
                'start'    => array('type' => 'integer', 'required' => false),
                'limit'    => array('type' => 'integer', 'required' => false),
                'columns'  => array('type' => 'string', 'array' => true, 'required' => false),
                'metadata' => array('type' => 'string', 'required' => false),
                'handle'   => array('type' => 'string', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $requesting_user_eid = $request->getRequestingUser();

        $stream_identifier = $params['eid'];
        $start = isset($params['start']) ? $params['start'] : 0;  // start isn't specified, start at the beginning
        $limit = isset($params['limit']) ? $params['limit'] : pow(2,24);  // if limit isn't specified, choose a large value (TODO: stream output in chunks?)
        $columns = isset($params['columns']) ? $params['columns'] : true;  // if columns aren't specified, return all columns
        $metadata = isset($params['metadata']) ? $params['metadata'] : false;
        $handle = isset($params['handle']) ? $params['handle'] : 'create';

        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream === false)
            return $request->getValidator()->fail(\Flexio\Base\Error::NO_OBJECT);

        // TODO: re-add
        //if ($stream->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
        //    return $request->getValidator()->fail(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $stream_info = $stream->get();
        if ($stream_info === false)
            return $request->getValidator()->fail(\Flexio\Base\Error::READ_FAILED);

        $mime_type = $stream_info['mime_type'];
        $content = $stream->content($start, $limit, $columns, $metadata, $handle);

        if ($mime_type !== \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            // return content as-is
            header('Content-Type: ' . $mime_type);
        }
         else
        {
            // flexio table; return application/json in place of internal mime
            header('Content-Type: ' . \Flexio\Base\ContentType::MIME_TYPE_JSON);
            $content = json_encode($content);
        }

        echo($content);
        exit(0);
    }

    public static function upload($params, $request)
    {
        var_dump($params);
        die();

        if (($params = $request->getValidator()->check($params, array(
                'eid'           => array('type' => 'identifier', 'required' => true),
                'name'          => array('type' => 'string',  'required' => false),
                'size'          => array('type' => 'integer', 'required' => false),
                'mime_type'     => array('type' => 'string',  'required' => false),
                'file_created'  => array('type' => 'string',  'required' => false), // TODO: date type?
                'file_modified' => array('type' => 'string',  'required' => false), // TODO: date type?
                'filename_hint' => array('type' => 'string',  'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $stream_identifier = $params['eid'];

        // get the object
        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream === false)
            return $request->getValidator()->fail(\Flexio\Base\Error::NO_OBJECT);

        self::handleStreamUpload($params, $stream);

        // return the stream info
        return $stream->get();
    }



    public static function handleStreamUpload($params, $stream)
    {
        // get the stream and the service
        $path = $stream->getPath();
        $service = $stream->getService();
        if ($service === false)
            return false;

        // create the output
        $streamwriter = \Flexio\Object\StreamWriter::create($stream);
        if ($streamwriter === false)
            return false;

        // get the information the parser needs to parse the content
        $post_content_type = $_SERVER['CONTENT_TYPE'] ?? '';


        if (strpos($post_content_type, 'multipart/form-data') !== false)
        {
            // multipart form-data upload
            $php_stream_handle = fopen('php://input', 'rb');

            // parse the content and set the stream info
            $part_data_snippet = false;
            $part_filename = false;
            $part_mimetype = false;
            $part_active = false;
            $part_succeeded = false;

            $parser = \Flexio\Base\MultipartParser::create();

            $parser->parse($php_stream_handle, $post_content_type, function ($type, $name, $data, $filename, $content_type) use (&$streamwriter, &$part_data_snippet, &$part_filename, &$part_mimetype, &$part_active, &$part_succeeded) {
                if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_BEGIN)
                {
                    if ($name == 'media' || $name == 'file') // we're looking for an element named 'media'; 'file' for temporary backward-compatibility
                    {
                        $part_active = true;
                        $part_filename = $filename;
                        $part_mimetype = $content_type;
                        $part_succeeded = true;
                    }
                }
                else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_DATA && $part_active)
                {
                    // get a sample of the data for mime sensing
                    if ($part_data_snippet === false)
                        $part_data_snippet = $data;

                    // write out the data
                    $streamwriter->write($data);
                }
                else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_END)
                {
                    $part_active = false;
                }
            });
            fclose($php_stream_handle);

            // make sure the parse was successful
            if (!$part_succeeded)
                return false;

            // determine the filename, stripping off the leading path info;
            // use a default if one wasn't supplied
            $default_name = \Flexio\Base\Util::generateHandle() . '.dat';
            $filename = strlen($part_filename) > 0 ? $part_filename : $default_name;
            $name = \Flexio\Base\Util::getFilename($filename);
            $ext = \Flexio\Base\Util::getFileExtension($filename);
            $filename = $name . (strlen($ext) > 0 ? ".$ext" : '');

            // sense the mime type, but go with what is declared if it's available
            $mime_type = \Flexio\Base\ContentType::MIME_TYPE_STREAM;
            $declared_mime_type = $part_mimetype;

            if ($part_data_snippet === false)
                $part_data_snippet = '';

            $filename_hint = $params['filename_hint'] ?? $filename;
        }
         else
        {
            $declared_mime_type = $_SERVER["CONTENT_TYPE"] ?? '';

            $php_stream_handle = fopen('php://input', 'rb');
            $part_data_snippet = false;

            while (true)
            {
                $data = fread($php_stream_handle, 32768);
                if ($data === false || strlen($data) == 0)
                    break;
                if ($part_data_snippet === false)
                    $part_data_snippet = $data;
                $streamwriter->write($data);
            }

            fclose($php_stream_handle);

            if ($part_data_snippet === false)
                $part_data_snippet = '';

            $filename = $_GET['name'] ?? \Flexio\Base\Util::generateHandle() . '.dat';

            $filename_hint = $_GET['filename_hint'] ?? $filename;
        }


        if (isset($_GET['filename_hint']))
        {
            // a hint filename was passed -- use that to try to determine the content type
            $mime_type = \Flexio\Base\ContentType::getMimeType($filename_hint, $part_data_snippet);
        }
        else
        {
            // use the Content-Type header passed to us
            if ($declared_mime_type == "application/x-www-form-urlencoded")
                $declared_mime_type = "application/octet-stream";

            if (strlen($declared_mime_type) == 0 || $declared_mime_type == "autosense")
                $mime_type = \Flexio\Base\ContentType::getMimeType($filename_hint, $part_data_snippet);
                    else
                $mime_type = $declared_mime_type;
        }


        // set the stream info
        $stream_info = array();
        $stream_info['name'] = $filename;
        $stream_info['mime_type'] = $mime_type;
        $stream->set($stream_info);
    }


    public static function download($params, $request)
    {
        // note: function adapted from the content function; first part of
        // the function is the same but then changes to convert to the flexio
        // table content into a CSV and then set appropriate http headers

        if (($params = $request->getValidator()->check($params, array(
                'eid'      => array('type' => 'identifier', 'required' => true),
                'start'    => array('type' => 'integer', 'required' => false),
                'limit'    => array('type' => 'integer', 'required' => false),
                'columns'  => array('type' => 'string', 'array' => true, 'required' => false),
                'metadata' => array('type' => 'string', 'required' => false),
                'handle'   => array('type' => 'string', 'required' => false)
            ))) === false)
            return $request->getValidator()->fail();

        $requesting_user_eid = $request->getRequestingUser();

        $stream_identifier = $params['eid'];
        $start = isset($params['start']) ? $params['start'] : 0;  // start isn't specified, start at the beginning
        $limit = isset($params['limit']) ? $params['limit'] : pow(2,24);  // if limit isn't specified, choose a large value (TODO: stream output in chunks?)
        $columns = isset($params['columns']) ? $params['columns'] : true;  // if columns aren't specified, return all columns
        $metadata = isset($params['metadata']) ? $params['metadata'] : false;
        $handle = isset($params['handle']) ? $params['handle'] : 'create';

        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream === false)
            return $request->getValidator()->fail(\Flexio\Base\Error::NO_OBJECT);

        // TODO: re-add
        //if ($stream->allows($requesting_user_eid, \Flexio\Object\Rights::ACTION_READ) === false)
        //    return $request->getValidator()->fail(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $stream_info = $stream->get();
        if ($stream_info === false)
            return $request->getValidator()->fail(\Flexio\Base\Error::READ_FAILED);

        $mime_type = $stream_info['mime_type'];
        $http_header_mime_type = ($mime_type === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE ? \Flexio\Base\ContentType::MIME_TYPE_CSV : $mime_type);

        // set the headers
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $output_filename = 'data.csv';

        if (isset($stream_info['name']) && strlen($stream_info['name']) > 0)
            $output_filename = $stream_info['name'];
        if (isset($params['name']))
            $output_filename = $params['name'];
        if ($mime_type === \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            // Flexio tables are exported as csv, so add an appropriate extension
            $filename_parts = pathinfo($output_filename);
            if (isset($filename_parts['filename']))
            {
                $filename_base = $filename_parts['filename'];
                $filename_ext = 'csv';
                $output_filename = $filename_base . '.' . $filename_ext;
            }
        }

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $http_header_mime_type);

        if (stripos($agent, 'win') !== false && stripos($agent, 'msie') !== false)
            header('Content-Disposition: filename="' . $output_filename . '"');
                else
            header('Content-Disposition: attachment; filename="' . $output_filename . '"');

        if ($mime_type !== \Flexio\Base\ContentType::MIME_TYPE_FLEXIO_TABLE)
        {
            // get the content in one chunk and return it as-is
            $content = $stream->content($start, $limit, $columns, $metadata, $handle);
            $out = fopen('php://output', 'w');
            fwrite($out, $content);
            fclose($out);
        }
         else
        {
            $out = fopen('php://output', 'w');
            $first = true;

            $read_chunk = 1000;
            $current_offset = $start;
            $end_offset = $start + $limit;

            while (true)
            {
                // find out how many rows to read; either the read chunk, or what's
                // left over if it's less than the read chunk size
                $rowreadsize = min($end_offset - $current_offset, $read_chunk);

                // if there's nothing left to read, we're done
                if ($rowreadsize <= 0)
                    break;

                // get the rows
                $content = $stream->content($current_offset, $rowreadsize, $metadata, $handle);
                $rows = $content['rows'];

                // if we've run out of rows, we're done
                if (count($rows) === 0)
                    break;

                foreach ($rows as $row)
                {
                    if ($first)
                    {
                        $keys = array_keys($row);
                        fputcsv($out, $keys);
                        $first = false;
                    }

                    fputcsv($out, $row);
                }

                // update the offset
                $current_offset += $rowreadsize;
            }

            fclose($out);
        }

        exit(0);
    }
}
