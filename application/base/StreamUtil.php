<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; David Z. Williams; Aaron L. Williams
 * Created:  2019-08-19
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class StreamUtil
{
    public static function getStreamContents($stream, int $start = 0, int $limit = PHP_INT_MAX, int $readsize = 1024 /* testing */) // returns array for table or string for data buffer
    {
        if ($start < 0 )
            $start = 0;
        if ($limit < 0)
            $limit = 0;
        if ($readsize <= 0)
            $readsize = 1;

        $mime_type = $stream->getMimeType();
        if ($mime_type === \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            return $stream->getReader()->getRows($start,$limit);
        }
        else
        {
            // read table content
            $offset1 = 0;
            $offset2 = 0;

            // the starting and ending position we want
            $range1 = $start;
            $range2 = $start + $limit;

            $result = '';
            $reader = $stream->getReader();

            while (true)
            {
                $chunk = $reader->read($readsize);
                if ($chunk === false)
                    break;

                $offset2 = $offset1 + strlen($chunk);

                // if we haven't reached the part we want, keep reading
                if ($offset2 < $range1)
                {
                    $offset1 = $offset2;
                    continue;
                }

                // if we're past the part we want, we're done
                if ($offset1 > $range2)
                    break;

                // case 1: chunk read is contained entirely in the range we want
                if ($offset1 >= $range1 && $offset2 <= $range2)
                    $result .= $chunk;

                // case 2: chunk read covers the range we want
                if ($offset1 < $range1 && $offset2 > $range2)
                    $result .= substr($chunk, $range1 - $offset1, $range2 - $range1);

                // case 3: chunk read covers first part of the range we want
                if ($offset1 < $range1 && $offset2 <= $range2)
                    $result .= substr($chunk, $range1 - $offset1);

                // case 4: chunk read covers second part of the range we want
                if ($offset1 >= $range1 && $offset2 > $range2)
                    $result .= substr($chunk, 0, $range2 - $offset1);

                // set the new starting offset position
                $offset1 = $offset2;
            }

            return $result;
        }
    }

    public static function addProcessInputFromStream($php_stream_handle, string $post_content_type, $process) : void
    {
        $stream = false;
        $streamwriter = false;
        $form_params = array();
        $post_streams = array();

        // first fetch query string parameters
        foreach ($_GET as $key => $value)
        {
            $form_params["query.$key"] = $value;
        }

        // \Flexio\Base\MultipartParser can handle application/x-www-form-urlencoded and /multipart/form-data
        // for all other types, we will take the post body and make it into the stdin

        $mime_type = $post_content_type;
        $semicolon_pos = strpos($mime_type, ';');
        if ($semicolon_pos !== false)
            $mime_type = substr($mime_type, 0, $semicolon_pos);
        if ($mime_type != 'application/x-www-form-urlencoded' && $mime_type != 'multipart/form-data')
        {
            $stream = \Flexio\Base\Stream::create();

            // post body is a data stream, post it as our pipe's stdin
            $first = true;
            $done = false;
            $streamwriter = null;
            while (!$done)
            {
                $data = fread($php_stream_handle, 32768);

                if ($data === false || strlen($data) != 32768)
                    $done = true;

                if ($first && $data !== false && strlen($data) > 0)
                {
                    $stream_info = array();
                    $stream_info['mime_type'] = $mime_type;
                    $stream->set($stream_info);
                    $streamwriter = $stream->getWriter();
                }

                if ($streamwriter)
                    $streamwriter->write($data);
            }

            $process->setParams($form_params);
            $process->setStdin($stream);

            return;
        }

        $size = 0;

        $parser = \Flexio\Base\MultipartParser::create();
        $parser->parse($php_stream_handle, $post_content_type, function ($type, $name, $data, $filename, $content_type) use (&$stream, &$streamwriter, &$process, &$form_params, &$size, &$post_streams) {
            if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_BEGIN)
            {
                $stream = \Flexio\Base\Stream::create();

                if ($content_type === false)
                    $content_type = \Flexio\Base\ContentType::getMimeType($filename, '');

                $size = 0;

                $stream_info = array();
                $stream_info['name'] = $filename;
                $stream_info['mime_type'] = $content_type;

                $stream->set($stream_info);
                $streamwriter = $stream->getWriter();
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_DATA)
            {
                if ($streamwriter !== false)
                {
                    // write out the data
                    $streamwriter->write($data);
                    $size += strlen($data);
                }
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_FILE_END)
            {
                $streamwriter = false;
                $stream->setSize($size);
                $process->addFile($name, $stream);
                $stream = false;
            }
            else if ($type == \Flexio\Base\MultipartParser::TYPE_KEY_VALUE)
            {
                $form_params['form.' . $name] = $data;
            }
        });
        fclose($php_stream_handle);

        $process->setParams($form_params);
    }

    public static function handleStreamUpload(array $params, \Flexio\IFace\IStreamWriter $streamwriter, string &$filename, string &$mime_type) : void
    {
        $filename = '';
        $mime_type = '';

        // get the information the parser needs to parse the content
        $post_content_type = \Flexio\System\System::getPhpInputStreamContentType();

        if (strpos($post_content_type, 'multipart/form-data') !== false)
        {
            // multipart form-data upload
            $php_stream_handle = \Flexio\System\System::openPhpInputStream();

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
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            // determine the filename, stripping off the leading path info;
            // use a default if one wasn't supplied
            $default_name = \Flexio\Base\Util::generateHandle() . '.dat';
            $filename = strlen($part_filename) > 0 ? $part_filename : $default_name;
            $name = \Flexio\Base\File::getFilename($filename);
            $ext = \Flexio\Base\File::getFileExtension($filename);
            $filename = $name . (strlen($ext) > 0 ? ".$ext" : '');

            // sense the mime type, but go with what is declared if it's available
            $mime_type = \Flexio\Base\ContentType::STREAM;
            $declared_mime_type = $part_mimetype;

            if ($part_data_snippet === false)
                $part_data_snippet = '';

            $filename_hint = $params['filename_hint'] ?? $filename;
        }
         else
        {
            $declared_mime_type = \Flexio\System\System::getPhpInputStreamContentType();
            $php_stream_handle = \Flexio\System\System::openPhpInputStream();
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
    }
}
