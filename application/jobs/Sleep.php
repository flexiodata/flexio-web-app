<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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

/*
// DESCRIPTION:
{
    "op": "sleep",
    "value": 1,
    // TODO: fill out
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['sleep'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Sleep extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        // get the duration
        $job_params = $this->getJobParameters();
        $milliseconds_to_wait = $job_params['value'];
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
}
