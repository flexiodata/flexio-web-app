<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-04-21
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Base
{
    const STORAGE_LOCAL = 'home';
    const STORAGE_AMAZONS3 = 'testsuite-amazons3';
    const STORAGE_BOX = 'testsuite-box';
    const STORAGE_DROPBOX = 'testsuite-dropbox';
    const STORAGE_GITHUB = 'testsuite-github';
    const STORAGE_GOOGLEDRIVE = 'testsuite-googledrive';
    const STORAGE_SFTP = 'testsuite-sftp';

    const ERROR_EXCEPTION = 'error-exception';
    const ERROR_NO_EXCEPTION = 'error-no-exception';
    const ERROR_BAD_PARSE = 'error-bad-parse';
    const ERROR_EVAL_MISMATCH = 'error-eval-mismatch';

    const DOUBLE_EPSILON = 0.000000000001;
    const CONTENT_TYPE_BUFFER_TEST_SIZE = 2048;

    const FLAG_NONE = '';
    const FLAG_ERROR_SUPPRESS = 'flag-error-suppress';

    public static function testsAllowed()
    {
        return (isset($GLOBALS['g_config']->tests_allowed) ? $GLOBALS['g_config']->tests_allowed : false);
    }

    public static function configure(\Flexio\Api2\Request $request)
    {
        if (!self::testsAllowed())
            return array();

        $tests = array();
        self::addTests('test', $tests);
        self::addTests('config', $tests);
        self::addTests('base', $tests);
        self::addTests('system', $tests);
        self::addTests('service', $tests);
        self::addTests('model', $tests);
        self::addTests('object', $tests);
        self::addTests('jobs', $tests);
        self::addTests('api', $tests);
        self::addTests('sdk', $tests);

        \Flexio\Api2\Response::sendContent($tests);
    }

    public static function run(\Flexio\Api2\Request $request)
    {
        $params = $request->getQueryParams();

        if (!self::testsAllowed())
        {
            $r = array();
            $r['name'] = "Error: tests aren't allowed";
            \Flexio\Api2\Response::sendContent($r);
            return;
        }

        if (!isset($params['id']))
        {
            $r = array();
            $r['name'] = "Error: missing test 'id' parameter";
            \Flexio\Api2\Response::sendContent($r);
            return;
        }

        $param_parts = explode('/', $params['id']);
        if (!isset($param_parts[0]) || !isset($param_parts[1]))
        {
            $r = array();
            $r['name'] = "Error: invalid test request";
            \Flexio\Api2\Response::sendContent($r);
            return;
        }

        $test_name = $params['id'];
        $test_folder = $param_parts[0];
        $test_id = $param_parts[1];

        $test_file = "$test_id";
        $test_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $test_folder . DIRECTORY_SEPARATOR . $test_file . '.php';

        // test file doesn't exist
        if (!@file_exists($test_path))
        {
            $r = array();
            $r['name'] = "Error: unable to locate the test: " . $test_name;
            \Flexio\Api2\Response::sendContent($r);
            return;
        }

        // load the job's php file and instantiate the job object
        include_once $test_path;
        $test = new Test;
        $results = array(); // array of \Flexio\Tests\Result

        if (!$test)
        {
            $r = array();
            $r['name'] = "Error: unable to load the test: " . $test_name;
            \Flexio\Api2\Response::sendContent($r);
            return;
        }


        // run the test
        $time1 = microtime(true);
        $test->run($results);
        $time2 = microtime(true);



        // add up the results
        $test_run_count = 0;
        $test_passed_count = 0;
        $test_failed_count = 0;
        $test_failed_list = '';

        foreach ($results as $r)
        {
            $test_run_count++;

            if ($r->passed === true)
            {
                $test_passed_count++;
                continue;
            }

            if ($test_failed_count > 0)
                $test_failed_list .= ', ';

            $test_failed_list .= $r->name;
            $test_failed_count++;
        }

        $test_passed = ($test_failed_count == 0 ? true : false);
        $test_time = round(($time2 - $time1)*1000,2);

        $test_message = '';
        $test_message .= "($test_time ms)";
        $test_message .= " $test_passed_count test(s) succeeded";
        $test_message .= ($test_failed_count == 0) ? '' : "; $test_failed_count test(s) failed: $test_failed_list";

        // report the results
        $r = array();
        $r['name'] = $test_name;
        $r['passed'] = $test_passed;
        $r['message'] = $test_message;
        $r['test_cnt'] = $test_run_count;
        $r['passed_cnt'] = $test_passed_count;
        $r['failed_cnt'] = $test_failed_count;
        $r['time'] = $test_time;
        $r['details'] = $results;

        \Flexio\Api2\Response::sendContent($r);
        return;
    }

    private static function addTests($subfolder, &$tests)
    {
        $search = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . '*.php';
        $files = glob($search);

        foreach ($files as $f)
        {
            $basename = basename($f);

            $matches = array();
            if (preg_match("/^([0-9.]*?-?[a-z0-9_-]*?)\\.php/", $basename, $matches))
            {
                $tests[] = $subfolder . '/' . $matches[1];
            }
        }
    }
}
