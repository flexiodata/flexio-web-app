<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-19
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class ContentType
{
    // standard content mime types
    const MIME_TYPE_BMP     = 'image/x-ms-bmp';
    const MIME_TYPE_CSS     = 'text/css';
    const MIME_TYPE_CSV     = 'text/csv';
    const MIME_TYPE_DOC     = 'application/msword';
    const MIME_TYPE_DOCX    = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    const MIME_TYPE_EMPTY   = 'application/x-empty';
    const MIME_TYPE_GIF     = 'image/gif';
    const MIME_TYPE_GZIP    = 'application/x-gzip';
    const MIME_TYPE_HTML    = 'text/html';
    const MIME_TYPE_JPEG    = 'image/jpeg';
    const MIME_TYPE_JS      = 'application/javascript';
    const MIME_TYPE_JSON    = 'application/json';
    const MIME_TYPE_MD      = 'text/markdown';
    const MIME_TYPE_PDF     = 'application/pdf';
    const MIME_TYPE_PNG     = 'image/png';
    const MIME_TYPE_RSS     = 'application/rss+xml';
    const MIME_TYPE_STREAM  = 'application/octet-stream';
    const MIME_TYPE_SVG     = 'image/svg+xml';
    const MIME_TYPE_TIFF    = 'image/tiff';
    const MIME_TYPE_TEXT    = 'text/plain';
    const MIME_TYPE_XLS     = 'application/vnd.ms-excel';
    const MIME_TYPE_XLSX    = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    const MIME_TYPE_XML     = 'application/xml';
    const MIME_TYPE_ZIP     = 'application/zip';

    // flexio mime types
    const MIME_TYPE_NONE         = '';
    const MIME_TYPE_FLEXIO_TABLE = 'application/vnd.flexio.table';
    const MIME_TYPE_FLEXIO_HTML = 'application/vnd.flexio.html';
    const MIME_TYPE_FLEXIO_FOLDER = 'application/vnd.flexio.folder';

    public static function getMimeType($extension, $buffer, $content_length = false) : string
    {
        // takes a filename/extension, a buffer, and an option length of content;
        // if a specific mime type is available from the buffer (e.g. from using)
        // magic numbers), then return the mime type from the buffer; however if
        // a buffer isn't available or a general mime type is returned from the
        // buffer (e.g. text/plain), but the filename indicates a specific content
        // type, then the mime is based off the filename

        // if we have an empty buffer and extension, return the empty mime type
        if ($extension === ''  && $buffer === '')
            return self::MIME_TYPE_EMPTY;

        // the extension may be a path; if it is, get the actual extension
        if (strpos($extension, '.') !== false)
            $extension = \Flexio\Base\File::getFileExtension($extension);
        $extension = strtolower($extension);

        $file_mime_type;
        $buffer_mime_type;
        $buffer_content_type;
        $extension_length = 0;
        $buffer_length = 0;

        $extension_length = strlen($extension);
        $buffer_length = strlen($buffer);
        $file_mime_type = self::getMimeTypeFromExtension($extension);
        self::getMimeAndContentType($buffer, $buffer_mime_type, $buffer_content_type);


        // if the mime type is the same, we're done
        if ($file_mime_type === $buffer_mime_type)
            return $file_mime_type;

        // if we have a buffer, but not an extension, return the mime type for the
        // buffer; if we have an extension, but not a buffer, return the mime type
        // for the extension
        if ($extension_length === 0 && $buffer_length > 0)
            return $buffer_mime_type;
        if ($extension_length > 0 && $buffer_length === 0)
            return $file_mime_type;

        switch ($buffer_mime_type)
        {
            // for some general mime types, use the filename/extension
            default:
            case self::MIME_TYPE_EMPTY:
            case self::MIME_TYPE_STREAM:
            case self::MIME_TYPE_TEXT:    // buffer for csv may look like text
                return $file_mime_type;

            // buffer for xlsx, docx looks like a zip; if we have all of the
            // buffer and we still have a zip, it's not xlsx or docx; however,
            // if we only have part of the buffer and it looks like a zip, trust
            // the filename
            case self::MIME_TYPE_ZIP:
                if ($content_length !== false && $buffer_length >= $content_length)
                    return $buffer_mime_type;
                if ($file_mime_type === self::MIME_TYPE_DOCX || $file_mime_type === self::MIME_TYPE_XLSX)
                    return $file_mime_type;
                     else
                    return $buffer_mime_type;

            // if we have specific, known mime types from the buffer, use them
            case self::MIME_TYPE_BMP:
            case self::MIME_TYPE_CSS:
            case self::MIME_TYPE_CSV:
            case self::MIME_TYPE_DOC:
            case self::MIME_TYPE_DOCX:
            case self::MIME_TYPE_GIF:
            case self::MIME_TYPE_GZIP:
            case self::MIME_TYPE_HTML:
            case self::MIME_TYPE_JPEG:
            case self::MIME_TYPE_JS:
            case self::MIME_TYPE_JSON:
            case self::MIME_TYPE_PDF:
            case self::MIME_TYPE_PNG:
            case self::MIME_TYPE_SVG:
            case self::MIME_TYPE_TIFF:
            case self::MIME_TYPE_XLS:
            case self::MIME_TYPE_XLSX:
            case self::MIME_TYPE_XML:
                return $buffer_mime_type;
        }
    }

    public static function getMimeTypeFromExtension($ext, $def_return = self::MIME_TYPE_STREAM) : string
    {
        if (strpos($ext, '.') !== false)
            $ext = \Flexio\Base\File::getFileExtension($ext);

        $ext = strtolower($ext);

        switch ($ext)
        {
            default:      return $def_return;

            case "bmp":   return self::MIME_TYPE_BMP;
            case "css":   return self::MIME_TYPE_CSS;
            case "icsv":
            case "csv":   return self::MIME_TYPE_CSV;
            case "doc":   return self::MIME_TYPE_DOC;
            case "docx":  return self::MIME_TYPE_DOCX;
            case "gif":   return self::MIME_TYPE_GIF;
            case "gz":    return self::MIME_TYPE_GZIP;
            case "html":
            case "htm":   return self::MIME_TYPE_HTML;
            case "jpeg":
            case "jpg":   return self::MIME_TYPE_JPEG;
            case "js":    return self::MIME_TYPE_JS;
            case "json":  return self::MIME_TYPE_JSON;
            case "md":    return self::MIME_TYPE_MD;
            case "pdf":   return self::MIME_TYPE_PDF;
            case "png":   return self::MIME_TYPE_PNG;
            case "svg":   return self::MIME_TYPE_SVG;
            case "tiff":
            case "tif":   return self::MIME_TYPE_TIFF;
            case "txt":   return self::MIME_TYPE_TEXT;
            case "xls":   return self::MIME_TYPE_XLS;
            case "xlsx":  return self::MIME_TYPE_XLSX;
            case "xml":   return self::MIME_TYPE_XML;
            case "zip":   return self::MIME_TYPE_ZIP;
        }
    }

    public static function getMimeAndContentType($buffer, &$mime_type, &$content_type) : bool
    {
        // gets the mime and content type from a string; returns true on success
        // and false otherwise

        $mime_type = '';
        $content_type = '';

        $finfo = new \finfo(FILEINFO_MIME);
        $result = $finfo->buffer($buffer);
        if ($result === false)
            return false;

        // TODO: set values here
        $parts = explode(';', $result);
        $local_mime_type = $parts[0] ?? '';
        $local_content_type = $parts[1] ?? '';

        $mime_type = trim($local_mime_type);
        $content_type = trim($local_content_type);

        return true;
    }
}
