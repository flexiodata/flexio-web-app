<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
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

class Exit1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $params = $this->getJobParameters();
        $code = $params['code'] ?? \Flexio\Jobs\Process::RESPONSE_NORMAL;
        $response = $params['response'] ?? '';

        if (is_array($response) || is_object($response))
        {
            $outstream->setMimeType(\Flexio\Base\ContentType::JSON);
            $response = json_encode($response, JSON_UNESCAPED_SLASHES);
        }

        $streamwriter = $outstream->getWriter();
        $streamwriter->write($response);

        // this next line will cause the process loop to exit
        // and return the http response code in $code
        $process->setResponseCode((int)$code);
    }
}
