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
    const NONE                   =  'none';
    const UNDEFINED              =  'undefined';
    const GENERAL                =  'general';
    const UNIMPLEMENTED          =  'unimplemented';
    const INVALID_VERSION        =  'invalid-version';
    const INVALID_REQUEST        =  'invalid-request';
    const INVALID_METHOD         =  'invalid-method';
    const INVALID_SYNTAX         =  'invalid-syntax';
    const MISSING_PARAMETER      =  'missing-parameter';
    const INVALID_PARAMETER      =  'invalid-parameter';
    const UNAUTHORIZED           =  'unauthorized';
    const INSUFFICIENT_RIGHTS    =  'insufficient-rights';
    const RATE_LIMIT_EXCEEDED    =  'rate-limit-exceeded';
    const SIZE_LIMIT_EXCEEDED    =  'size-limit-exceeded';
    const CONNECTION_FAILED      =  'connection-failed';
    const CREATE_FAILED          =  'create-failed';
    const READ_FAILED            =  'read-failed';
    const WRITE_FAILED           =  'write-failed';
    const DELETE_FAILED          =  'delete-failed';
    const NO_DATABASE            =  'no-database';
    const NO_MODEL               =  'no-model';
    const NO_SERVICE             =  'no-service';
    const NO_OBJECT              =  'no-object';

    public static function getDefaultMessage($code)
    {
        switch ($code)
        {
            default:
                return 'Operation failed';

            case self::NONE:                   return '';
            case self::UNDEFINED:              return 'Operation failed';
            case self::GENERAL:                return 'General error';
            case self::UNIMPLEMENTED:          return 'Unimplemented';
            case self::NO_DATABASE:            return 'Database not available';
            case self::NO_MODEL:               return 'Model not available';
            case self::NO_SERVICE:             return 'Service not available';
            case self::NO_OBJECT:              return 'Object not available';
            case self::CONNECTION_FAILED:      return 'Could not connect';
            case self::INVALID_SYNTAX:         return 'Invalid syntax';
            case self::MISSING_PARAMETER:      return 'Missing parameter';
            case self::INVALID_PARAMETER:      return 'Invalid parameter';
            case self::CREATE_FAILED:          return 'Could not create object';
            case self::DELETE_FAILED:          return 'Could not delete object';
            case self::WRITE_FAILED:           return 'Could not write to object';
            case self::READ_FAILED:            return 'Could not read from object';
            case self::UNAUTHORIZED:           return 'Unauthorized';
            case self::INSUFFICIENT_RIGHTS:    return 'Insufficient rights';
            case self::RATE_LIMIT_EXCEEDED:    return 'Rate limit exceeded';
            case self::SIZE_LIMIT_EXCEEDED:    return 'Size limit exceeded';
            case self::INVALID_METHOD:         return 'Invalid method';
            case self::INVALID_VERSION:        return 'Invalid version';
            case self::INVALID_REQUEST:        return 'Invalid request';
        }
    }
}
