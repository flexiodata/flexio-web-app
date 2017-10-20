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


class Echo1 extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        // process stdin
        $stdin = $context->getStdin();
        $stdout = $context->getStdout();

        $stdout->setMimeType(\Flexio\Base\ContentType::MIME_TYPE_TXT);
        //$stdout = $stdin->copy()->setPath(\Flexio\Base\Util::generateHandle());
        //$context->setStdout($stdout);

        $job_definition = $this->getProperties();
        $msg = $job_definition['params']['msg'] ?? '';

        $streamwriter = \Flexio\Object\StreamWriter::create($stdout);
        $streamwriter->write($msg);
    }


    // job definition info
    const MIME_TYPE = 'flexio.echo';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.echo",
        "params": {
            "msg": ""
        }
    }
EOD;
    // direction is "asc" or "desc"
    const SCHEMA = <<<EOD
    {
        "type": "object",
        "required": ["type","params"],
        "properties": {
            "type": {
                "type": "string",
                "enum": ["flexio.echo"]
            },
            "params": {
                "type": "object",
                "required": ["msg"],
                "properties": {
                }
            }
        }
    }
EOD;
}
