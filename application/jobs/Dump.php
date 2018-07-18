<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-02-27
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "dump",  // string, required
    "msg": ""      // string, required
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['dump']),
        'msg'        => array('required' => true,  'type' => 'string')
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Dump extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        $instream = $process->getStdin();
        $outstream = $process->getStdout();

        $outstream->copyFrom($instream);

        $params = $this->getJobParameters();
        $msg = $params['msg'] ?? '';

        if (is_array($msg) || is_object($msg))
        {
            $msg = @json_encode($msg);
        }

        echo $msg;
    }
}
