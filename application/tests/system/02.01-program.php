<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-05-06
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
        // TEST: background job processing tests

        // BEGIN TEST

        // come up with a random filename
        $tmpfile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fxtestsuite_' . md5(microtime()) . '.tmp';

        // run a line of code that puts some data into a temporary file with a random name
        \Flexio\System\Program::runInBackground("file_put_contents('$tmpfile', '$tmpfile');", false /* we want it to run in the background */);

        // sleep 3 seconds while the job runs
        sleep(3);



        $str = file_get_contents($tmpfile); // get the value in the file
        @unlink($tmpfile); // clean up

        $actual = ($str == $tmpfile ? true : false);
        $expected = true;
        \Flexio\Tests\Check::assertBoolean('A.1', '\Flexio\Base\Util::runInBackground(); $wait = false',  $actual, $expected, $results);
    }
}
