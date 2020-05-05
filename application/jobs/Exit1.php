<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-10-09
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "exit",   // string, required
    "code": "",     // integer
    "response": ""  // string
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['exit']),
        'code'       => array('required' => false, 'type' => 'integer'),
        'response'   => array('required' => false, 'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Exit1 implements \Flexio\IFace\IJob
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
        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $params = $this->getJobParameters();
        $code = $params['code'] ?? \Flexio\Jobs\Process::RESPONSE_NORMAL;
        $response = $params['response'] ?? false;

        if (is_array($response) || is_object($response))
        {
            $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
            $response = json_encode($response, JSON_UNESCAPED_SLASHES);
        }

        if ($response !== false)
        {
            $streamwriter = $outstream->getWriter();
            $streamwriter->write($response);
        }
         else
        {
            // if no response is specified, write out stdout
            $outstream->copyFrom($instream);
        }


        // this next line will cause the process loop to exit
        // and return the http response code in $code
        $process->setResponseCode((int)$code);
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}
