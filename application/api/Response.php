<?php
/**
 *
 * Copyright (c) 2014, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2014-09-30
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Response
{
    public static function sendContent($content, bool $echo)
    {
        if ($echo === false)
            return $content;

        // set the default headers; note: never cache api calls
        header('Expires: Mon, 15 Mar 2010 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // set the HTTP header content type to json; note: IE's behaves badly if
        // content-type json is returned in response to multi-part uploads
        if (count($_FILES) == 0)
            header('Content-Type: application/json');

        $response = @json_encode($content, JSON_PRETTY_PRINT);
        echo $response;
    }

    public static function sendError(array $error, bool $echo)
    {
        $response = array();
        $response['error'] = $error;
        if ($echo === false)
            return $response;

        // set the default headers; note: never cache api calls
        header('Expires: Mon, 15 Mar 2010 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // set the HTTP header content type to json; note: IE's behaves badly if
        // content-type json is returned in response to multi-part uploads
        if (count($_FILES) == 0)
            header('Content-Type: application/json');

        $error = $response['error'];
        $error_code = $error['code'] ?? '';
        $error_message = $error['message'] ?? '';

        // change the error code to be more specific in case of unauthorized access
        if ($error_code === \Flexio\Base\Error::INSUFFICIENT_RIGHTS && self::sessionAuthExpired() === true)
            $error_code = \Flexio\Base\Error::UNAUTHORIZED;

        // set the specific error header
        switch ($error_code)
        {
            // TODO: for now, map the default and ERROR_GENERAL to 400, which
            // is what we've been using for all error codes up 'till now; however,
            // we may want to switch to 500, in which case we'll need to properly
            // assign ERROR_GENERAL to other categories when appropriate
            default:
            case \Flexio\Base\Error::GENERAL:
                \Flexio\Base\Util::header_error(400);
                break;

            // "UNAUTHORIZED" type errors; the user might have access to the object
            // if they were logged in, but the session is invalid
            case \Flexio\Base\Error::UNAUTHORIZED:
                \Flexio\Base\Util::header_error(401);
                break;

            // "FORBIDDEN" type errors; access not allowed
            case \Flexio\Base\Error::INSUFFICIENT_RIGHTS:
                \Flexio\Base\Util::header_error(403);
                break;

            // "NOT FOUND" type errors; invalid requests, invalid
            // parameters, or valid requests for objects that can't
            // be found
            case \Flexio\Base\Error::UNIMPLEMENTED:
            case \Flexio\Base\Error::DEPRECATED:
            case \Flexio\Base\Error::INVALID_VERSION:
            case \Flexio\Base\Error::INVALID_REQUEST:
            case \Flexio\Base\Error::MISSING_PARAMETER:
            case \Flexio\Base\Error::INVALID_PARAMETER:
            case \Flexio\Base\Error::NO_DATABASE:
            case \Flexio\Base\Error::NO_MODEL:
            case \Flexio\Base\Error::NO_SERVICE:
            case \Flexio\Base\Error::NO_OBJECT:
                \Flexio\Base\Util::header_error(404);
                break;

            // "UNPROCESSABLE ENTITY"; request can't be processed
            // for some reason
            case \Flexio\Base\Error::CREATE_FAILED:
            case \Flexio\Base\Error::DELETE_FAILED:
            case \Flexio\Base\Error::WRITE_FAILED:
            case \Flexio\Base\Error::READ_FAILED:
            case \Flexio\Base\Error::RATE_LIMIT_EXCEEDED:
            case \Flexio\Base\Error::SIZE_LIMIT_EXCEEDED:
                \Flexio\Base\Util::header_error(422);
                break;

            // "INTERNAL SERVER ERROR"; something is wrong internally
            case \Flexio\Base\Error::UNDEFINED:
            case \Flexio\Base\Error::NO_DATABASE:
            case \Flexio\Base\Error::NO_MODEL:
            case \Flexio\Base\Error::NO_SERVICE:
                \Flexio\Base\Util::header_error(500);
                break;
        }

        // if a message isn't specified, supply a default message
        if (strlen($error_message ) == 0)
            $error['message'] = \Flexio\Base\Error::getDefaultMessage($error_code);

        $response['error'] = $error;
        $response = @json_encode($response, JSON_PRETTY_PRINT);
        echo $response;
    }

    private static function sessionAuthExpired() : bool
    {
        return (isset($_COOKIE['FXSESSID']) && strlen($_COOKIE['FXSESSID']) > 0 && $GLOBALS['g_store']->user_eid == '') ? true:false;
    }
}
