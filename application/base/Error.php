<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
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
    public const UNDEFINED              =  '';
    public const GENERAL                =  'general';
    public const UNIMPLEMENTED          =  'unimplemented';
    public const DEPRECATED             =  'deprecated';
    public const INVALID_VERSION        =  'invalid-version';
    public const INVALID_REQUEST        =  'invalid-request';
    public const INVALID_SYNTAX         =  'invalid-syntax';
    public const INVALID_FORMAT         =  'invalid-format';
    public const MISSING_PARAMETER      =  'missing-parameter';
    public const INVALID_PARAMETER      =  'invalid-parameter';
    public const UNAUTHORIZED           =  'unauthorized';
    public const INSUFFICIENT_RIGHTS    =  'insufficient-rights';
    public const RATE_LIMIT_EXCEEDED    =  'rate-limit-exceeded';
    public const SIZE_LIMIT_EXCEEDED    =  'size-limit-exceeded';
    public const INTEGRITY_FAILED       =  'integrity-failed';
    public const CONNECTION_FAILED      =  'connection-failed';
    public const CREATE_FAILED          =  'create-failed';
    public const OPEN_FAILED            =  'open-failed';
    public const READ_FAILED            =  'read-failed';
    public const WRITE_FAILED           =  'write-failed';
    public const DELETE_FAILED          =  'delete-failed';
    public const EXECUTE_FAILED         =  'execute-failed';
    public const NO_DATABASE            =  'no-database';
    public const NO_MODEL               =  'no-model';
    public const NO_SERVICE             =  'no-service';
    public const NO_OBJECT              =  'no-object';
    public const OBJECT_ALREADY_EXISTS  =  'object-already-exists';
    public const NOT_FOUND              =  'not-found';  // generic not found (404)

    public static function getDefaultMessage($code) : string
    {
        switch ($code)
        {
            default:
                return 'Operation failed';

            case self::UNDEFINED:              return 'Operation failed';
            case self::GENERAL:                return 'General error';
            case self::UNIMPLEMENTED:          return 'Unimplemented';
            case self::DEPRECATED:             return 'Deprecated';
            case self::NO_DATABASE:            return 'Database not available';
            case self::NO_MODEL:               return 'Model not available';
            case self::NO_SERVICE:             return 'Service not available';
            case self::NO_OBJECT:              return 'Object not available';
            case self::INTEGRITY_FAILED:       return 'Integrity check failed';
            case self::CONNECTION_FAILED:      return 'Could not connect';
            case self::INVALID_SYNTAX:         return 'Invalid syntax';
            case self::MISSING_PARAMETER:      return 'Missing parameter';
            case self::INVALID_PARAMETER:      return 'Invalid parameter';
            case self::INVALID_FORMAT:         return 'Invalid format';
            case self::CREATE_FAILED:          return 'Could not create object';
            case self::DELETE_FAILED:          return 'Could not delete object';
            case self::WRITE_FAILED:           return 'Could not write to object';
            case self::READ_FAILED:            return 'Could not read from object';
            case self::EXECUTE_FAILED:         return 'Could not execute process';
            case self::UNAUTHORIZED:           return 'Unauthorized';
            case self::INSUFFICIENT_RIGHTS:    return 'Insufficient rights';
            case self::RATE_LIMIT_EXCEEDED:    return 'Rate limit exceeded';
            case self::SIZE_LIMIT_EXCEEDED:    return 'Size limit exceeded';
            case self::INVALID_VERSION:        return 'Invalid version';
            case self::INVALID_REQUEST:        return 'Invalid request';
            case self::NOT_FOUND:              return 'Not found';
        }
    }
}
