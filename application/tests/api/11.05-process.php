<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-04-12
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // ENDPOINT: POST /:teamid/processes/:objeid


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $password1 = \Flexio\Base\Password::generate();
        $userid1 = \Flexio\Tests\Util::createUser(null, null, $password1);
        $token1 = \Flexio\Tests\Util::createToken($userid1);
        $password2 = \Flexio\Base\Password::generate();
        $userid2 = \Flexio\Tests\Util::createUser(null, null, $password2);
        $token2 = \Flexio\Tests\Util::createToken($userid2);



        // TEST: jobs created in background

        // BEGIN TEST
        $script = <<<EOD
import time
def flexio_handler(context):
    time.sleep(2) # assure overlap
    context.output.content_type = "text/plain"
    context.output.write("+")
EOD;
        // launch 500 background jobs
        $process_eids = array();
        for ($i = 0; $i < 250; $i++)
        {
            $process_eids[] = self::runBackgroundExecuteJob($apibase, $userid1, $token1, $script);
        }

        // get the responses for each one
        $result = '';
        foreach ($process_eids as $eid)
        {
            $reponse = self::getBackgroundExecuteJobResult($apibase, $userid1, $token1, $eid);
            $result .= $reponse;
        }

        $actual = count($process_eids);
        $expected = 500;
        \Flexio\Tests\Check::assertNumber('A.1', 'POST /:teamid/processes/:objeid; verify the number of processes that were launched',  $actual, $expected, $results);

        $actual = $result;
        $expected = '++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++';
        \Flexio\Tests\Check::assertString('A.2', 'POST /:teamid/processes/:objeid; check the results of running 25 background jobs at the same time',  $actual, $expected, $results);
    }

    private static function runBackgroundExecuteJob($apibase, $userid, $token, $script) : string
    {
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid/processes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => '{
                "run": true,
                "background": true,
                "debug": false,
                "process_mode": "' . \Flexio\Jobs\Process::MODE_BUILD /*needed for output*/ . '",
                "task": {
                    "op": "execute",
                    "lang": "python",
                    "code": "' . base64_encode($script) . '"
                }
            }'
        );
        $result = \Flexio\Tests\Util::callApi($params);
        $response = $result['response'];
        $process_info = @json_decode($response, true);
        return  $process_info['eid'] ?? '';
    }

    private static function getBackgroundExecuteJobResult($apibase, $userid, $token, $processid) : string
    {
        $idx = 0;
        while (true)
        {
            $params = array(
                'method' => 'GET',
                'url' => "$apibase/$userid/processes/$processid",
                'token' => $token,
                'content_type' => 'application/json'
            );
            $result = \Flexio\Tests\Util::callApi($params);
            $response = $result['response'];
            $process_info = @json_decode($response, true);

            if ($process_info['process_status'] !== \Flexio\Jobs\Process::STATUS_RUNNING)
            {
                $stream_eid = $process_info['output']['eid'] ?? '';
                if (!\Flexio\Base\Eid::isValid($stream_eid))
                    return 'F';

                $params = array(
                    'method' => 'GET',
                    'url' => "$apibase/$userid/streams/$stream_eid/content",
                    'token' => $token,
                    'content_type' => 'application/json'
                );
                $result = \Flexio\Tests\Util::callApi($params);
                return $result['response'] ?? 'F';
            }

            $idx++;
            if ($idx > 20*100) // allow 100 seconds to finish (20 per second)
                break;

            usleep(50000); // wait 50ms between checks
        }
    }
}

