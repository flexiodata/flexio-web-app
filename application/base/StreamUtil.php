<?php
/**
 *
 * Copyright (c) 2019, Flex Research LLC. All rights reserved.
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
    public static function createStreamFromFile(string $path) : \Flexio\Base\Stream
    {
        $f = @fopen($path, 'rb');
        if (!$f)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $mime_type = false;

        $stream = \Flexio\Base\Stream::create();
        $writer = $stream->getWriter();
        while (!feof($f))
        {
            $buffer = fread($f, 2048);
            $writer->write($buffer);

            if ($mime_type === false)
                $mime_type = \Flexio\Base\ContentType::getMimeType($path, $buffer);
        }

        fclose($f);

        $stream->setMimeType($mime_type);

        return $stream;
    }

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

    public static function handleStreamUpload($php_stream_handle, string $post_content_type, array $params, \Flexio\IFace\IStreamWriter $streamwriter, string &$filename, string &$mime_type) : void
    {
        // function for writing input from the php input stream to a flexio stream

        // example use:
        // $php_stream_handle = \Flexio\System\System::openPhpInputStream();
        // $post_content_type = \Flexio\System\System::getPhpInputStreamContentType();
        // $outstream = \Flexio\Base\Stream::create();
        // $outfilename = '';
        // $outmimetype = '';
        // \Flexio\Base\StreamUtil::handleStreamUpload($php_stream_handle, $post_content_type, array('filename_hint' => 'upload.csv'), $outstream, $outfilename, $outmimetype);

        $filename = '';
        $mime_type = '';
        $determined_mime_type = $post_content_type; // start off with the declared mime type

        if (strpos($post_content_type, 'multipart/form-data') !== false)
        {
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
            $determined_mime_type = $part_mimetype;

            if ($part_data_snippet === false)
                $part_data_snippet = '';

            $filename_hint = $params['filename_hint'] ?? $filename;
        }
         else
        {
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
            if ($determined_mime_type == "application/x-www-form-urlencoded")
                $determined_mime_type = "application/octet-stream";

            if (strlen($determined_mime_type) == 0 || $determined_mime_type == "autosense")
                $mime_type = \Flexio\Base\ContentType::getMimeType($filename_hint, $part_data_snippet);
                    else
                $mime_type = $determined_mime_type;
        }
    }
}
