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


declare(strict_types=1);
namespace Flexio\Api;


class Stream
{
    public static function create(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid_status'    => array('type' => 'string', 'required' => false),
                'name'          => array('type' => 'string',  'required' => false),
                'size'          => array('type' => 'integer', 'required' => false),
                'mime_type'     => array('type' => 'string',  'required' => false),
                'file_created'  => array('type' => 'string',  'required' => false), // TODO: date type?
                'file_modified' => array('type' => 'string',  'required' => false), // TODO: date type?
                'expires'       => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $stream = \Flexio\Object\Stream::create($validated_params); // the \Flexio\Object\Stream::create() creates a default connection and path
        return $stream->get();
    }

    public static function set(\Flexio\Api\Request $request) : array
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'           => array('type' => 'identifier', 'required' => true),
                'eid_status'    => array('type' => 'string', 'required' => false),
                'name'          => array('type' => 'string',  'required' => false),
                'size'          => array('type' => 'integer', 'required' => false),
                'mime_type'     => array('type' => 'string',  'required' => false),
                'file_created'  => array('type' => 'string',  'required' => false), // TODO: date type?
                'file_modified' => array('type' => 'string',  'required' => false), // TODO: date type?
                'expires'       => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $stream_identifier = $validated_params['eid'];

        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream !== false)
            $stream->set($validated_params);

        return $stream->get();
    }

    public static function get(\Flexio\Api\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $stream_identifier = $validated_params['eid'];

        // check the rights on the object
        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // TODO: re-add
        //if ($stream->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return $stream->get();
    }

    public static function content(\Flexio\Api\Request $request) // TODO: set function return type
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'          => array('type' => 'identifier', 'required' => true),
                'start'        => array('type' => 'integer', 'required' => false),
                'limit'        => array('type' => 'integer', 'required' => false),
                'metadata'     => array('type' => 'string', 'required' => false),
                'content_type' => array('type' => 'string', 'required' => false),
                'encode'       => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $stream_identifier = $validated_params['eid'];
        $start = isset($validated_params['start']) ? (int)$validated_params['start'] : 0;  // start isn't specified, start at the beginning
        $limit = isset($validated_params['limit']) ? (int)$validated_params['limit'] : PHP_INT_MAX;  // if limit isn't specified, choose a large value (TODO: stream output in chunks?)
        $metadata = isset($validated_params['metadata']) ? \toBoolean($validated_params['metadata']) : false;
        $content_type = isset($validated_params['content_type']) ? $validated_params['content_type'] : false;
        $encode = $validated_params['encode'] ?? null;

        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // TODO: re-add
        //if ($stream->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $response_content_type = $stream_info['mime_type'];

        // if the caller wants to override the mime type that will be returned, they may
        if ($content_type !== false)
            $response_content_type = $content_type;

        if ($response_content_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            // return content as-is
            header('Content-Type: ' . $response_content_type);
            $result = \Flexio\Base\Util::getStreamContents($stream, $start, $limit);
            if (isset($encode))
            {
                // user wants us to re-encode the data payload on a preview-only basis
                $encoding = mb_detect_encoding($result, 'UTF-8,ISO-8859-1');
                if ($encoding != 'UTF-8')
                    $result = iconv($encoding, 'UTF-8', $result);
            }
            echo($result);
            exit(0);
        }
         else
        {
            // flexio table; return application/json in place of internal mime
            header('Content-Type: ' . \Flexio\Base\ContentType::JSON);

            $result = array();
            $result['success'] = true;
            $result['total_count'] = $stream->getRowCount(); // TODO: fill out

            if ($metadata === true)
                $result['columns'] = $stream->getStructure()->get();

            $result['rows'] = \Flexio\Base\Util::getStreamContents($stream, $start, $limit);
            $result = json_encode($result);

            echo($result);
            exit(0);
        }
    }

    public static function upload(\Flexio\Api\Request $request)
    {
        $params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'           => array('type' => 'identifier', 'required' => true),
                'name'          => array('type' => 'string',  'required' => false),
                'size'          => array('type' => 'integer', 'required' => false),
                'mime_type'     => array('type' => 'string',  'required' => false),
                'file_created'  => array('type' => 'string',  'required' => false), // TODO: date type?
                'file_modified' => array('type' => 'string',  'required' => false), // TODO: date type?
                'expires'       => array('type' => 'string',  'required' => false),
                'filename_hint' => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $stream_identifier = $validated_params['eid'];

        // get the object
        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // write to the stream
        $filename = '';
        $mime_type = '';
        $streamwriter = $stream->getWriter();
        \Flexio\Base\Util::handleStreamUpload($validated_params, $streamwriter, $filename, $mime_type);

        // set the stream info
        $stream_info = array();
        $stream_info['name'] = $filename;
        $stream_info['mime_type'] = $mime_type;
        $stream->set($stream_info);

        return $stream->get();
    }

    public static function download(\Flexio\Api\Request $request) // TODO: set function return type
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        // note: function adapted from the content function; first part of
        // the function is the same but then changes to convert to the flexio
        // table content into a CSV and then set appropriate http headers

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'      => array('type' => 'identifier', 'required' => true),
                'start'    => array('type' => 'integer', 'required' => false),
                'limit'    => array('type' => 'integer', 'required' => false),
                'name'     => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $stream_identifier = $validated_params['eid'];
        $start = isset($validated_params['start']) ? (int)$validated_params['start'] : 0;  // start isn't specified, start at the beginning
        $limit = isset($validated_params['limit']) ? (int)$validated_params['limit'] : PHP_INT_MAX;  // if limit isn't specified, choose a large value (TODO: stream output in chunks?)

        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // TODO: re-add
        //if ($stream->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $mime_type = $stream_info['mime_type'];
        $http_header_mime_type = ($mime_type === \Flexio\Base\ContentType::FLEXIO_TABLE ? \Flexio\Base\ContentType::CSV : $mime_type);

        // set the headers
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $output_filename = 'data.csv';

        if (isset($stream_info['name']) && strlen($stream_info['name']) > 0)
            $output_filename = $stream_info['name'];
        if (isset($validated_params['name']))
            $output_filename = $validated_params['name'];
        if ($mime_type === \Flexio\Base\ContentType::FLEXIO_TABLE)
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

        if ($mime_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            // get the content in one chunk and return it as-is
            $content = \Flexio\Base\Util::getStreamContents($stream, $start, $limit);
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
                $rowreadsize = (int)min($end_offset - $current_offset, $read_chunk);

                // if there's nothing left to read, we're done
                if ($rowreadsize <= 0)
                    break;

                // get the rows
                $content = \Flexio\Base\Util::getStreamContents($stream, $current_offset, $rowreadsize);

                // if we've run out of rows, we're done
                if (count($content) === 0)
                    break;

                foreach ($content as $row)
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
