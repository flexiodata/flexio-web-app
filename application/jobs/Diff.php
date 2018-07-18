<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-01-11
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "diff"
    // TODO: fill out
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['diff'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Diff extends \Flexio\Jobs\Base
{

private $code = <<<EOT

import difflib
diff = difflib.unified_diff("a", "b")

EOT;

    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        $dockerbin = \Flexio\System\System::getBinaryPath('docker');
        if (is_null($dockerbin))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $cmd = "$dockerbin run -a stdin -a stdout -a stderr --rm -i fxruntime sh -c '(echo ".base64_encode($code)." | base64 -d > /fxnodejs/script.js && timeout 30s nodejs /fxnodejs/run.js unmanaged /fxnodejs/script.js)'";

        $script_host = new ReportScriptHost();
        $script_host->params = $params;
        $script_host->setProcess($process);

        $ep = new ExecuteProxy;
        $ep->initialize($cmd, $script_host);
        $ep->run();

        $err = $ep->getStdError();

        if (isset($err))
        {
            //die("<pre>".$err);
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, $err);
        }
    }
}
