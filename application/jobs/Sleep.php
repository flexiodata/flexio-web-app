<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-06-29
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Sleep extends \Flexio\Jobs\Base
{
    public function run(\Flexio\Object\Context &$context)
    {
        parent::run($context);

        // get the duration
        $job_definition = $this->getProperties();
        $milliseconds_to_wait = $job_definition['params']['value'];
        $milliseconds_to_update_status = 100;

        $milliseconds_waited = 0;
        $start_time = microtime(true);
        while (true)
        {
            // get the current time
            $current_time = microtime(true);
            $milliseconds_waited = ($current_time - $start_time)*1000;

            // check if we're done
            if ($milliseconds_waited >= $milliseconds_to_wait)
                break;

            // TODO: update the status

            // wait
            usleep($milliseconds_to_update_status*1000);
        }
    }


    // job definition info
    const TEMPLATE = <<<EOD
    {
        "type": "flexio.sleep",
        "params": {
            "value": 1
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
                "enum": ["flexio.sleep"]
            },
            "params": {
                "type": "object"
            }
        }
    }
EOD;
}
