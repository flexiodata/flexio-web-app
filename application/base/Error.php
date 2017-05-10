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
    const NO_DATABASE            =  'no_database';
    const NO_MODEL               =  'no_model';
    const NO_SERVICE             =  'no_service';
    const MISSING_PARAMETER      =  'missing_parameter';
    const INVALID_PARAMETER      =  'invalid_parameter';
    const INVALID_SYNTAX         =  'invalid_syntax';
    const NO_OBJECT              =  'no_object';
    const CONNECTION_FAILED      =  'connection_failed';
    const CREATE_FAILED          =  'create_failed';
    const DELETE_FAILED          =  'delete_failed';
    const WRITE_FAILED           =  'write_failed';
    const READ_FAILED            =  'read_failed';
    const UNAUTHORIZED           =  'unauthorized';
    const INSUFFICIENT_RIGHTS    =  'insufficient_rights';
    const SIZE_LIMIT_EXCEEDED    =  'size_limit_exceeded';
    const INVALID_METHOD         =  'invalid_method';
    const INVALID_VERSION        =  'invalid_version';
    const INVALID_REQUEST        =  'invalid_request';

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
            case self::SIZE_LIMIT_EXCEEDED:    return 'Size limit exceeded';
            case self::INVALID_METHOD:         return 'Invalid method';
            case self::INVALID_VERSION:        return 'Invalid version';
            case self::INVALID_REQUEST:        return 'Invalid request';
        }
    }
}
