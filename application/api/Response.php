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
    public static function sendContent(array $content) : void
    {
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

    public static function sendError(array $error) : void
    {
        $error_code = $error['code'] ?? \Flexio\Base\Error::GENERAL;
        $error_message = $error['message'] ?? '';

        // set the default headers; note: never cache api calls
        header('Expires: Mon, 15 Mar 2010 05:00:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // set the HTTP header content type to json; note: IE's behaves badly if
        // content-type json is returned in response to multi-part uploads
        if (count($_FILES) == 0)
            header('Content-Type: application/json');

        // set the http error code
        $http_error_code = self::getHttpErrorCode($error_code);
        \Flexio\Base\Util::header_error($http_error_code);

        // if a message isn't specified, supply a default message
        if (strlen($error_message ) == 0)
            $error_message = \Flexio\Base\Error::getDefaultMessage($error_code);

        // make sure the error code and message are updated with defaults
        $error['code'] = $error_code;
        $error['message'] = $error_message;

        // send the response
        $response = array();
        $response['error'] = $error;
        $response = @json_encode($response, JSON_PRETTY_PRINT);
        echo $response;
    }

    public static function getHttpErrorCode(string $error_code) : int
    {
        // change the error code to be more specific in case of unauthorized access
        if ($error_code === \Flexio\Base\Error::INSUFFICIENT_RIGHTS && self::sessionAuthExpired() === true)
            $error_code = \Flexio\Base\Error::UNAUTHORIZED;

        // return the associated http error code
        switch ($error_code)
        {
            // "BAD REQUEST" type errors; there's some type of syntax error
            // in the request (e.g. missing parameter or invalid parameter)
            default:
            case \Flexio\Base\Error::UNDEFINED:
            case \Flexio\Base\Error::GENERAL:
            case \Flexio\Base\Error::INVALID_SYNTAX:
                return 400;

            // "UNAUTHORIZED" type errors; the user might have access to the object
            // if they were logged in, but the session is invalid
            case \Flexio\Base\Error::UNAUTHORIZED:
                return 401;

            // "FORBIDDEN" type errors; access not allowed
            case \Flexio\Base\Error::INSUFFICIENT_RIGHTS:
                return 403;

            // "NOT FOUND" type errors; invalid requests, invalid parameters, or
            // valid requests for objects that can't be found
            case \Flexio\Base\Error::INVALID_VERSION:
            case \Flexio\Base\Error::INVALID_REQUEST:
            case \Flexio\Base\Error::UNAVAILABLE:
                return 404;

            // "UNPROCESSABLE ENTITY"; request can't be processed for some reason
            case \Flexio\Base\Error::INTEGRITY_FAILED:
            case \Flexio\Base\Error::CONNECTION_FAILED:
            case \Flexio\Base\Error::CREATE_FAILED:
            case \Flexio\Base\Error::DELETE_FAILED:
            case \Flexio\Base\Error::OPEN_FAILED:
            case \Flexio\Base\Error::WRITE_FAILED:
            case \Flexio\Base\Error::READ_FAILED:
            case \Flexio\Base\Error::EXECUTE_FAILED:
            case \Flexio\Base\Error::SIZE_LIMIT_EXCEEDED:
            case \Flexio\Base\Error::OBJECT_ALREADY_EXISTS:
                return 422;

            // "TOO MANY REQUESTS"; too many requests in a given
            // period of time
            case \Flexio\Base\Error::RATE_LIMIT_EXCEEDED:
                return 429;

            // "INTERNAL SERVER ERROR"; something is wrong internally
            case \Flexio\Base\Error::UNIMPLEMENTED: // 501 isn't appropriate as that applies to HTTP request methods
            case \Flexio\Base\Error::DEPRECATED:
            case \Flexio\Base\Error::NO_DATABASE:
            case \Flexio\Base\Error::NO_MODEL:
            case \Flexio\Base\Error::NO_SERVICE:
                return 500;
        }
    }

    private static function sessionAuthExpired() : bool
    {
        return (isset($_COOKIE['FXSESSID']) && strlen($_COOKIE['FXSESSID']) > 0 && $GLOBALS['g_store']->user_eid == '') ? true:false;
    }
}
