<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
    public const UNDEFINED     = '';
    public const BMP           = 'image/x-ms-bmp';
    public const CSS           = 'text/css';
    public const CSV           = 'text/csv';
    public const DOC           = 'application/msword';
    public const DOCX          = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    public const EMPTY         = 'application/x-empty';
    public const GIF           = 'image/gif';
    public const GZIP          = 'application/x-gzip';
    public const HTML          = 'text/html';
    public const JPEG          = 'image/jpeg';
    public const JAVASCRIPT    = 'application/javascript';
    public const JSON          = 'application/json';
    public const MARKDOWN      = 'text/markdown';
    public const PDF           = 'application/pdf';
    public const PNG           = 'image/png';
    public const RSS           = 'application/rss+xml';
    public const ATOM          = 'application/atom+xml';
    public const STREAM        = 'application/octet-stream';
    public const SVG           = 'image/svg+xml';
    public const TIFF          = 'image/tiff';
    public const TEXT          = 'text/plain';
    public const ODS           = 'application/vnd.oasis.opendocument.spreadsheet';
    public const XLS           = 'application/vnd.ms-excel';
    public const XLSX          = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    public const XML           = 'application/xml';
    public const ZIP           = 'application/zip';
    public const FLEXIO_TABLE  = 'application/vnd.flexio.table';
    public const FLEXIO_HTML   = 'application/vnd.flexio.html';
    public const FLEXIO_FOLDER = 'application/vnd.flexio.folder';

    public static function getMimeType($extension, $buffer = '', $content_length = false) : string
    {
        // takes a filename/extension, a buffer, and an option length of content;
        // if a specific mime type is available from the buffer (e.g. from using)
        // magic numbers), then return the mime type from the buffer; however if
        // a buffer isn't available or a general mime type is returned from the
        // buffer (e.g. text/plain), but the filename indicates a specific content
        // type, then the mime is based off the filename

        // handle flexible input types for convenience
        if (!is_string($extension))
            $extension = '';
        if (!is_string($buffer))
            $buffer = '';
        if ($content_length !== false && !is_integer($content_length))
            $content_length = false;

        // if we have an empty buffer and extension, return the empty mime type
        if ($extension === ''  && $buffer === '')
            return ContentType::EMPTY;

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
            case ContentType::EMPTY:
            case ContentType::STREAM:
            case ContentType::TEXT:    // buffer for csv may look like text
                return $file_mime_type;

            // buffer for xlsx, docx looks like a zip; if we have all of the
            // buffer and we still have a zip, it's not xlsx or docx; however,
            // if we only have part of the buffer and it looks like a zip, trust
            // the filename
            case ContentType::ZIP:
                if ($content_length !== false && $buffer_length >= $content_length)
                    return $buffer_mime_type;
                if ($file_mime_type === ContentType::DOCX || $file_mime_type === ContentType::XLSX)
                    return $file_mime_type;
                     else
                    return $buffer_mime_type;

            // if we have specific, known mime types from the buffer, use them
            case ContentType::BMP:
            case ContentType::CSS:
            case ContentType::CSV:
            case ContentType::DOC:
            case ContentType::DOCX:
            case ContentType::GIF:
            case ContentType::GZIP:
            case ContentType::HTML:
            case ContentType::JPEG:
            case ContentType::JAVASCRIPT:
            case ContentType::JSON:
            case ContentType::PDF:
            case ContentType::PNG:
            case ContentType::SVG:
            case ContentType::TIFF:
            case ContentType::XLS:
            case ContentType::XLSX:
            case ContentType::XML:
                return $buffer_mime_type;
        }
    }

    public static function getExtensionFromMimeType(string $mime_type) : string
    {
        switch ($mime_type)
        {
            default:
                return 'txt';

            case ContentType::BMP:        return 'bmp';
            case ContentType::CSS:        return 'css';
            case ContentType::CSV:        return 'csv';
            case ContentType::DOC:        return 'doc';
            case ContentType::DOCX:       return 'docx';
            case ContentType::GIF:        return 'gif';
            case ContentType::GZIP:       return 'gz';
            case ContentType::HTML:       return 'html';
            case ContentType::JPEG:       return 'jpg';
            case ContentType::JAVASCRIPT: return 'js';
            case ContentType::JSON:       return 'json';
            case ContentType::MARKDOWN:   return 'md';
            case ContentType::PDF:        return 'pdf';
            case ContentType::PNG:        return 'png';
            case ContentType::SVG:        return 'svg';
            case ContentType::TIFF:       return 'tif';
            case ContentType::TEXT:       return 'txt';
            case ContentType::XLS:        return 'xls';
            case ContentType::XLSX:       return 'xlsx';
            case ContentType::XML:        return 'xml';
            case ContentType::ZIP:        return 'zip';
        }
    }

    public static function getMimeTypeFromExtension($ext, $def_return = ContentType::STREAM) : string
    {
        // handle flexible input types for convenience
        if (!is_string($ext))
            $ext = '';
        if (!is_string($def_return))
            $def_return = ContentType::STREAM;

        if (strpos($ext, '.') !== false)
            $ext = \Flexio\Base\File::getFileExtension($ext);

        $ext = strtolower($ext);

        switch ($ext)
        {
            default:      return $def_return;

            case "bmp":   return ContentType::BMP;
            case "css":   return ContentType::CSS;
            case "icsv":
            case "csv":   return ContentType::CSV;
            case "doc":   return ContentType::DOC;
            case "docx":  return ContentType::DOCX;
            case "gif":   return ContentType::GIF;
            case "gz":    return ContentType::GZIP;
            case "html":
            case "htm":   return ContentType::HTML;
            case "jpeg":
            case "jpg":   return ContentType::JPEG;
            case "js":    return ContentType::JAVASCRIPT;
            case "json":  return ContentType::JSON;
            case "md":    return ContentType::MARKDOWN;
            case "pdf":   return ContentType::PDF;
            case "png":   return ContentType::PNG;
            case "svg":   return ContentType::SVG;
            case "tiff":
            case "tif":   return ContentType::TIFF;
            case "txt":   return ContentType::TEXT;
            case "xls":   return ContentType::XLS;
            case "xlsx":  return ContentType::XLSX;
            case "xml":   return ContentType::XML;
            case "zip":   return ContentType::ZIP;
        }
    }

    public static function getMimeAndContentType($buffer, &$mime_type, &$content_type) : bool
    {
        // handle flexible input types for convenience
        if (!is_string($buffer))
            $buffer = '';

        // gets the mime and content type from a string; returns true on success
        // and false otherwise

        $mime_type    = '';
        $content_type    = '';

        $finfo = new \finfo(FILEINFO_MIME);
        $result = $finfo->buffer($buffer);
        if ($result === false)
            return false;

        $parts = explode(';', $result);
        $local_mime_type = $parts[0] ?? '';
        $local_content_type = $parts[1] ?? '';

        $mime_type = trim($local_mime_type);
        $content_type = trim($local_content_type);

        return true;
    }
}
