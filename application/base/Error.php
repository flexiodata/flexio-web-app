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
}
