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
namespace Flexio\Api2;


class Response
{
    public static function sendContent(array $content)
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

    public static function sendError(array $error)
    {
        $response = array();
        $response['error'] = $error;

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

        // set the http error code
        $http_error_code = self::getHttpErrorCode($error_code);
        \Flexio\Base\Util::header_error($http_error_code);

        // if a message isn't specified, supply a default message
        if (strlen($error_message ) == 0)
            $error['message'] = \Flexio\Base\Error::getDefaultMessage($error_code);

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
            // TODO: for now, map the default and ERROR_GENERAL to 400, which
            // is what we've been using for all error codes up 'till now; however,
            // we may want to switch to 500, in which case we'll need to properly
            // assign ERROR_GENERAL to other categories when appropriate
            default:
            case \Flexio\Base\Error::GENERAL:
                return 400;

            // "UNAUTHORIZED" type errors; the user might have access to the object
            // if they were logged in, but the session is invalid
            case \Flexio\Base\Error::UNAUTHORIZED:
                return 401;

            // "FORBIDDEN" type errors; access not allowed
            case \Flexio\Base\Error::INSUFFICIENT_RIGHTS:
                return 403;

            // "NOT FOUND" type errors; invalid requests, invalid
            // parameters, or valid requests for objects that can't
            // be found
            case \Flexio\Base\Error::UNIMPLEMENTED:
            case \Flexio\Base\Error::DEPRECATED:
            case \Flexio\Base\Error::INVALID_VERSION:
            case \Flexio\Base\Error::INVALID_REQUEST:
            case \Flexio\Base\Error::MISSING_PARAMETER:
            case \Flexio\Base\Error::INVALID_PARAMETER:
            case \Flexio\Base\Error::NO_OBJECT:
                return 404;

            // "UNPROCESSABLE ENTITY"; request can't be processed
            // for some reason
            case \Flexio\Base\Error::INTEGRITY_FAILED:
            case \Flexio\Base\Error::CONNECTION_FAILED:
            case \Flexio\Base\Error::CREATE_FAILED:
            case \Flexio\Base\Error::DELETE_FAILED:
            case \Flexio\Base\Error::WRITE_FAILED:
            case \Flexio\Base\Error::READ_FAILED:
            case \Flexio\Base\Error::RATE_LIMIT_EXCEEDED:
            case \Flexio\Base\Error::SIZE_LIMIT_EXCEEDED:
                return 422;

            // "INTERNAL SERVER ERROR"; something is wrong internally
            case \Flexio\Base\Error::UNDEFINED:
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
