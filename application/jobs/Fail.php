<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-16
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Fail extends \Flexio\Jobs\Base
{
    public function run()
    {
        $job_definition = $this->getProperties();
        $code = $job_definition['params']['code'] ?? '';
        $message = $job_definition['params']['message'] ?? '';

        switch ($code)
        {
            default:
                throw new \Error;

            case NONE:
            case UNDEFINED:
            case GENERAL:
            case UNIMPLEMENTED:
            case NO_DATABASE:
            case NO_MODEL:
            case NO_SERVICE:
            case MISSING_PARAMETER:
            case INVALID_PARAMETER:
            case INVALID_SYNTAX:
            case NO_OBJECT:
            case CONNECTION_FAILED:
            case CREATE_FAILED:
            case DELETE_FAILED:
            case WRITE_FAILED:
            case READ_FAILED:
            case UNAUTHORIZED:
            case INSUFFICIENT_RIGHTS:
            case SIZE_LIMIT_EXCEEDED:
            case INVALID_METHOD:
            case INVALID_VERSION:
            case INVALID_REQUEST:
                throw new \Flexio\Base\Exception($error, $message);
        }
    }


    // job definition info
    const MIME_TYPE = 'flexio.fail';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.fail",
        "params": {
        }
    }
EOD;
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.fail"]
            },
            "params": {
                "code": "string"
                "message": "string"
            }
        }
    }
EOD;
}
