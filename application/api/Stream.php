<?php
/**
 *
 * Copyright (c) 2014-2016, Gold Prairie LLC. All rights reserved.
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
    public static function get(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $stream_eid = $request->getObjectFromUrl();

        // load the object; make sure the eid is associated with the owner
        // as an additional check
        $stream = \Flexio\Object\Stream::load($stream_eid);
        if ($owner_user_eid !== $stream->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($stream->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($stream->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_STREAM_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = $stream->get();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function content(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();
        $stream_eid = $request->getObjectFromUrl();
        $user_agent = $request->getUserAgent();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'start'        => array('type' => 'integer', 'required' => false),
                'limit'        => array('type' => 'integer', 'required' => false),
                'metadata'     => array('type' => 'string',  'required' => false),
                'content_type' => array('type' => 'string',  'required' => false),
                'encode'       => array('type' => 'string',  'required' => false),
                'download'     => array('type' => 'boolean', 'required' => false),
                'filename'     => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();
        $start = isset($validated_query_params['start']) ? (int)$validated_query_params['start'] : 0;  // start isn't specified, start at the beginning
        $limit = isset($validated_query_params['limit']) ? (int)$validated_query_params['limit'] : PHP_INT_MAX;  // if limit isn't specified, choose a large value (TODO: stream output in chunks?)
        $metadata = isset($validated_query_params['metadata']) ? \toBoolean($validated_query_params['metadata']) : false;
        $content_type = isset($validated_query_params['content_type']) ? $validated_query_params['content_type'] : false;
        $encode = $validated_query_params['encode'] ?? null;
        $download = $validated_query_params['download'] ?? false;
        $filename = $validated_query_params['filename'] ?? false;

        // load the object; make sure the eid is associated with the owner
        // as an additional check;
        $stream = \Flexio\Object\Stream::load($stream_eid);
        if ($owner_user_eid !== $stream->getOwner())
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        // check the rights on the object
        if ($stream->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($stream->allows($requesting_user_eid, \Flexio\Api\Action::TYPE_STREAM_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        if ($download === true)
            self::echoDownload($stream, $content_type, $encode, $metadata, $start, $limit, $filename, $user_agent);
              else
            self::echoContent($stream, $content_type, $encode, $metadata, $start, $limit);
    }

    private static function echoDownload(\Flexio\IFace\IStream $stream, $content_type, $encode, $metadata, $start, $limit, $filename, $user_agent) :  void
    {
        // get the stream info
        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // set the response type
        $response_content_type = $stream_info['mime_type'];

        // if the response type is a flexio table, convert it to CSV
        $is_flexio_table = false;
        if ($response_content_type === \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            $is_flexio_table = true;
            $response_content_type = \Flexio\Base\ContentType::CSV;
        }

        // if the user supplies a content type, override the response content type
        if ($content_type !== false)
            $response_content_type = $content_type;

        // if a filename is supplied, use the stream name as the basis for the output filename;
        // otherwise, create a default filename
        if ($filename === false)
        {
            $filename = 'download';
            $filename = $filename . '.' . \Flexio\Base\ContentType::getExtensionFromMimeType($response_content_type);
        }

        if ($is_flexio_table === true)
        {
            // try to set the headers; if we can't (e.g. user agent indicates 'bot', then throw an exception)
            $headers_set = \Flexio\Api\Response::headersDownload($user_agent, $filename, $response_content_type);
            if ($headers_set === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            $handle = fopen('php://output', 'w');
            if (!$handle)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            // write header row
            $row = $stream->getStructure()->getNames();
            fputcsv($handle, $row);

            $streamreader = $stream->getReader();
            while (true)
            {
                $data = $streamreader->readRow();
                if ($data === false)
                    break;

                fputcsv($handle, array_values($data));
            }

            fclose($handle);
        }
         else
        {
            // try to set the headers; if we can't (e.g. user agent indicates 'bot', then throw an exception)
            $headers_set = \Flexio\Api\Response::headersDownload($user_agent, $filename, $response_content_type);
            if ($headers_set === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            $result = \Flexio\Base\StreamUtil::getStreamContents($stream, $start, $limit);
            if (isset($encode))
            {
                // user wants us to re-encode the data payload on a preview-only basis
                $encoding = mb_detect_encoding($result, 'UTF-8,ISO-8859-1');
                if ($encoding != 'UTF-8')
                    $result = iconv($encoding, 'UTF-8', $result);
            }

            \Flexio\Api\Response::sendRaw($result);
        }
    }

    private static function echoContent(\Flexio\IFace\IStream $stream, $content_type, $encode, $metadata, $start, $limit) : void
    {
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
            $result = \Flexio\Base\StreamUtil::getStreamContents($stream, $start, $limit);
            if (isset($encode))
            {
                // user wants us to re-encode the data payload on a preview-only basis
                $encoding = mb_detect_encoding($result, 'UTF-8,ISO-8859-1');
                if ($encoding != 'UTF-8')
                    $result = iconv($encoding, 'UTF-8', $result);
            }

            \Flexio\Api\Response::sendRaw($result);
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

            $result['rows'] = \Flexio\Base\StreamUtil::getStreamContents($stream, $start, $limit);
            $result = json_encode($result, JSON_UNESCAPED_SLASHES);

            \Flexio\Api\Response::sendRaw($result);
        }
    }

/*
    public static function addToZipArchive($arhivefilename, $zippedfilename, $inputfilename) : void
    {
        // create the zip archive if needed and add the temporary file to the zip archive
        $zip = new \ZipArchive();
        $zip->open($arhivefilename, \ZipArchive::CREATE);
        $zip->addFile($inputfilename, $zippedfilename);
        $zip->close();
    }

    public static function echoZipDownload($user_agent, $zip_name) : void
    {
        $content_type = 'application/zip';
        \Flexio\Api\response::headersDownload($user_agent, $zip_name, $content_type);
        @readfile($zip_name);
    }
*/
}

