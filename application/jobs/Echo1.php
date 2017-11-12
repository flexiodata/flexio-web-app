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
// EXAMPLE:
{
    "type": "flexio.echo",
    "params": {
        "msg": ""
    }
}
*/

class Echo1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        parent::run($context);

        // process stdin
        $stdin = $context->getStdin();
        $stdout = $context->getStdout();
        $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT);
        $stdout->set($stdin->get());
        $stdout->setPath(\Flexio\Base\Util::generateHandle());

        $job_definition = $this->getProperties();
        $msg = $job_definition['params']['msg'] ?? '';

        $streamwriter = $stdout->getWriter();
        $streamwriter->write($msg);
    }
}
