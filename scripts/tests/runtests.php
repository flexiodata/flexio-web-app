<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Gold Prairie Website
 * Author:   Aaron L. Williams
 * Created:  2017-04-08
 *
 * @package flexio
 * @subpackage Database_Update
 */


include_once __DIR__.'/../stub.php';


try
{
    // get the tests
    $tests = getTests();
    if ($tests === false)
        return; // no tests to run

    // run through each test

    echo("Starting tests...\n\n");
    foreach ($tests as $t)
    {
        $params = array();
        $params['id'] = $t;
        $test_result = \Flexio\Tests\TestBase::run($params);

        if (is_array($test_result) === false)
        {
            $message = "Warning: unable to process output for test $t\n";
            echo($message);
            continue;
        }

        foreach ($test_result as $test_summary)
        {
/*
            $test_summary_name = $test_summary['name'];
            $test_summary_message = $test_summary['message'];

            echo($test_summary_name);
            echo($test_summary_message);
            echo('--------------------------------------------------');


            $r['name'] = $test_name;
            $r['passed'] = $test_passed;
            $r['message'] = $test_message;
            $r['test_cnt'] = $test_run_count;
            $r['passed_cnt'] = $test_passed_count;
            $r['failed_cnt'] = $test_failed_count;
            $r['time'] = $test_time;
            $r['details'] = $results;

            $name = $r['name'];
            $description = $r['description'];
            $passed = $r['passed'];
            $message = $r['message'];

            $message = '';
            $message .= $passed === true ? "Passed" : "Failed";
            $message .= ". ";
            $message .= "Test %t";
            $message .= "\n";
            echo($message);
*/
        }
    }

    echo("\n\nFinished tests.");
}
catch (\Exception $e)
{
    // TODO: echo failure
}


function getTests()
{
    $tests = array();
    //addTests('test', $tests);
    addTests('config', $tests);
    addTests('base', $tests);
    addTests('system', $tests);
    addTests('service', $tests);
    addTests('model', $tests);
    addTests('object', $tests);
    addTests('jobs', $tests);
    addTests('api', $tests);
    return $tests;
}

function addTests($subfolder, &$tests)
{
    $basedir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR;
    $search = $basedir . '*.php';
    $files = glob($search);

    foreach ($files as $f)
    {
        $basename = basename($f);

        $matches = array();
        if (preg_match("/^([0-9.]*?-?[a-z_]*?)\\.php/", $basename, $matches))
        {
            $tests[] = $subfolder . '/' . $matches[1];
        }
    }
}

