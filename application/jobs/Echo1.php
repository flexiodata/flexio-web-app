<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
    "op": "echo",  // string, required
    "msg": ""      // string, required
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['echo'])
        'msg'        => array('required' => true,  'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Echo1 implements \Flexio\IFace\IJob
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
        \Flexio\Jobs\Base::replaceParameterTokens($process, $task);

        $object = new static();
        $object->properties = $task;
        $object->run_internal($process);
    }

    private function run_internal(\Flexio\IFace\IProcess $process) : void
    {
        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $params = $this->getJobParameters();
        $msg = $params['msg'] ?? '';


        if (is_array($msg) || is_object($msg))
        {
            $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
            $msg = json_encode($msg, JSON_UNESCAPED_SLASHES);
        }

        $streamwriter = $outstream->getWriter();
        $streamwriter->write($msg);

       // echo $msg;
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}
