<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
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

/*
// DESCRIPTION:
{
    "op": "fail",  // string, required
    "code": "",    // string
    "message": ""  // string
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['fail']),
        'code'       => array('required' => false, 'type' => 'string'),
        'message'    => array('required' => false, 'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Fail implements \Flexio\IFace\IJob
{
    private $properties = array();

    public static function validate(array $task) : array
    {
        $errors = array();
        return $errors;
    }

    public static function run(\Flexio\IFace\IProcess $process, array $task) : void
    {
        unset($task['op']);
        \Flexio\Jobs\Util::replaceParameterTokens($process, $task);

        $object = new static();
        $object->properties = $task;
        $object->run_internal($process);
    }

    private function run_internal(\Flexio\IFace\IProcess $process) : void
    {
        $params = $this->getJobParameters();
        $code = $params['code'] ?? '';
        $message = $params['message'] ?? '';

        switch ($code)
        {
            default:
                throw new \Error;

            case \Flexio\Base\Error::UNDEFINED:
            case \Flexio\Base\Error::GENERAL:
            case \Flexio\Base\Error::UNIMPLEMENTED:
            case \Flexio\Base\Error::DEPRECATED:
            case \Flexio\Base\Error::NO_DATABASE:
            case \Flexio\Base\Error::NO_MODEL:
            case \Flexio\Base\Error::NO_SERVICE:
            case \Flexio\Base\Error::INVALID_SYNTAX:
            case \Flexio\Base\Error::INTEGRITY_FAILED;
            case \Flexio\Base\Error::CONNECTION_FAILED:
            case \Flexio\Base\Error::CREATE_FAILED:
            case \Flexio\Base\Error::DELETE_FAILED:
            case \Flexio\Base\Error::WRITE_FAILED:
            case \Flexio\Base\Error::READ_FAILED:
            case \Flexio\Base\Error::EXECUTE_FAILED:
            case \Flexio\Base\Error::UNAUTHORIZED:
            case \Flexio\Base\Error::INSUFFICIENT_RIGHTS:
            case \Flexio\Base\Error::RATE_LIMIT_EXCEEDED:
            case \Flexio\Base\Error::SIZE_LIMIT_EXCEEDED:
            case \Flexio\Base\Error::INVALID_VERSION:
            case \Flexio\Base\Error::INVALID_REQUEST:
                throw new \Flexio\Base\Exception($code, $message);
        }
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}
