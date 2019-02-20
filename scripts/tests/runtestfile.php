<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-10
 *
 * @package flexio
 * @subpackage Database_Update
 */


include_once __DIR__.'/../stub.php';


if ($argc < 2)
{
    echo '{ "success": false, "msg": "Usage: php runtestfile.php <test_id>" }';
    exit(0);
}


try
{
    $test_params = array();
    $test_params['id'] = $argv[1];
    $show_failures_only = $argv[2] ?? false;
    $show_failures_only = toBoolean($show_failures_only);

    $result = \Flexio\Tests\Base::run($test_params);
    echoResult($result, $show_failures_only);
}
catch (\Exception $e)
{
    $message = "Warning: unable to run tests for " . $params['id'];
    echo($message);
}


function echoResult($result, $show_failures_only)
{
    if (is_array($result) === false)
    {
        $message = "Warning: unable to process output for test $t\n";
        echo($message);
    }

    // header info
    $test_name = $result['name'];
    $test_passed = $result['passed'];
    $test_message = $result['message'];
    $test_run_count = $result['test_cnt'];
    $test_passed_count = $result['passed_cnt'];
    $test_failed_count = $result['failed_cnt'];
    $test_time = $result['time'];

    echo($test_name);
    echo($test_message);

    if ($show_failures_only === false || $test_failed_count > 0)
    {
        echo("\n");
        echo("--------------------------------------------------");
        echo("\n");
    }

    // detail info
    $test_detail = $result['details'];
    foreach ($test_detail as $test_item)
    {
        $name = $test_item->name;
        $description = $test_item->description;
        $passed = $test_item->passed;


        $message = '';
        $message .= $passed === true ? "[PASSED] " : "[FAILED] "; // use "[KNOWN ] " for known errors
        $message .= "$name $description";
        $message .= "\n";

        if ($show_failures_only === true && $passed === true)
            continue;

        echo($message);
    }

    echo("\n");
}
