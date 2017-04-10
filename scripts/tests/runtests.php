<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-08
 *
 * @package flexio
 * @subpackage Database_Update
 */


include_once __DIR__.'/../stub.php';


try
{
    // flag to indicate whether or not to show only failures
    $show_failures_only = true;

    // get the php binary
    $php = \Flexio\System\System::getBinaryPath('php');

    // get the tests
    $tests = getTests();
    if ($tests === false)
        return; // no tests to run

    // run through each test
    echo("Starting tests...\n\n");
    foreach ($tests as $t)
    {
        $cmd = "$php runtestfile.php $t $show_failures_only"; // true/false indicates whether to show failures only; for now, show everything
        passthru($cmd);
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

