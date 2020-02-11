<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-03-20
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Error
{
    // general error types (400)
    public const UNDEFINED              =  '';
    public const GENERAL                =  'general';
    public const INVALID_VERSION        =  'invalid-version';
    public const INVALID_REQUEST        =  'invalid-request';
    public const INVALID_SYNTAX         =  'invalid-syntax';

    // authorization error types (401)
    public const UNAUTHORIZED           =  'unauthorized';

    // forbidden error types (403)
    public const INSUFFICIENT_RIGHTS    =  'insufficient-rights';

    // not found type errors (404)
    public const UNAVAILABLE            =  'unavailable'; // object isn't found or is outside scope of requesting user

    // processing type errors (422)
    public const INTEGRITY_FAILED       =  'integrity-failed';
    public const CONNECTION_FAILED      =  'connection-failed';
    public const CREATE_FAILED          =  'create-failed';
    public const DELETE_FAILED          =  'delete-failed';
    public const OPEN_FAILED            =  'open-failed';
    public const WRITE_FAILED           =  'write-failed';
    public const READ_FAILED            =  'read-failed';
    public const EXECUTE_FAILED         =  'execute-failed';
    public const OBJECT_ALREADY_EXISTS  =  'object-already-exists';
    public const SIZE_LIMIT_EXCEEDED    =  'size-limit-exceeded';

    // rate limit type errors (429)
    public const RATE_LIMIT_EXCEEDED    =  'rate-limit-exceeded';

    // internal server error types (500)
    public const UNIMPLEMENTED          =  'unimplemented';
    public const DEPRECATED             =  'deprecated';
    public const NO_DATABASE            =  'no-database';
    public const NO_MODEL               =  'no-model';
    public const NO_SERVICE             =  'no-service';

    // note: if additional items are added here; add appropriate
    // http status code in \Flexio\Api\Response

    public static function getDefaultMessage($code) : string
    {
        switch ($code)
        {
            default:
                return 'Operation failed';

            // general error types (400)
            case self::UNDEFINED:              return 'Operation failed';
            case self::GENERAL:                return 'General error';
            case self::INVALID_VERSION:        return 'Invalid version';
            case self::INVALID_REQUEST:        return 'Invalid request';
            case self::INVALID_SYNTAX:         return 'Invalid syntax';

            // authorization error types (401)
            case self::UNAUTHORIZED:           return 'Unauthorized';

            // forbidden error types (403)
            case self::INSUFFICIENT_RIGHTS:    return 'Insufficient rights';

            // not found type errors (404)
            case self::UNAVAILABLE:            return 'Unavailable';

            // processing type errors (422)
            case self::INTEGRITY_FAILED:       return 'Integrity check failed';
            case self::CONNECTION_FAILED:      return 'Could not connect';
            case self::CREATE_FAILED:          return 'Could not create object';
            case self::DELETE_FAILED:          return 'Could not delete object';
            case self::OPEN_FAILED:            return 'Could not open object';
            case self::WRITE_FAILED:           return 'Could not write to object';
            case self::READ_FAILED:            return 'Could not read from object';
            case self::EXECUTE_FAILED:         return 'Could not execute process';
            case self::OBJECT_ALREADY_EXISTS:  return 'Object already exists';
            case self::SIZE_LIMIT_EXCEEDED:    return 'Size limit exceeded';

            // rate limit type errors (429)
            case self::RATE_LIMIT_EXCEEDED:    return 'Rate limit exceeded';

            // internal server error types (500)
            case self::UNIMPLEMENTED:          return 'Unimplemented';
            case self::DEPRECATED:             return 'Deprecated';
            case self::NO_DATABASE:            return 'Database not available';
            case self::NO_MODEL:               return 'Model not available';
            case self::NO_SERVICE:             return 'Service not available';
        }
    }

    public static function getInfo($e) : array
    {
        $type = '';
        $code = '';
        $message = '';

        if ($e instanceof \Flexio\Base\Exception)
        {
            $info = $e->getMessage(); // exception info is packaged up in message
            $info = json_decode($info,true);

            $type = 'flexio exception';
            $code = $info['code'];
            $message = $info['message'];
        }
        elseif ($e instanceof \Exception)
        {
            $type = 'system exception';
            $code = \Flexio\Base\Error::GENERAL;
        }
        elseif ($e instanceof \Error)
        {
            $type = 'system error';
            $code = \Flexio\Base\Error::GENERAL;
        }

        $error = array();
        $error['code'] = $code;
        $error['message'] = $message;

        if (IS_DEBUG())
        {
            $error['type'] = $type;
            $error['module'] = $e->getFile();
            $error['line'] = $e->getLine();
            $error['debug_message'] = $e->getMessage();
            $error['trace'] = $e->getTraceAsString();
        }

        return $error;
    }
}
