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


class TestResult
{
    public $name;
    public $description;
    public $passed;
    public $message;

    public function __construct($name = '', $description = '', $passed = false, $message = '')
    {
        $this->name = $name;
        $this->description = $description;
        $this->passed = $passed;
        $this->message = $message;
    }
}

class TestBase
{
    public static function testsAllowed()
    {
        return (isset($GLOBALS['g_config']->tests_allowed) ? $GLOBALS['g_config']->tests_allowed : false);
    }

    public static function configure(\Flexio\Api\Request $request)
    {
        if (!TestBase::testsAllowed())
            return array();

        $tests = array();
        TestBase::addTests('test', $tests);
        TestBase::addTests('config', $tests);
        TestBase::addTests('base', $tests);
        TestBase::addTests('system', $tests);
        TestBase::addTests('service', $tests);
        TestBase::addTests('model', $tests);
        TestBase::addTests('object', $tests);
        TestBase::addTests('jobs', $tests);
        TestBase::addTests('api', $tests);

        return $tests;
    }

    public static function run(\Flexio\Api\Request $request)
    {
        $params = $request->getQueryParams();

        if (!TestBase::testsAllowed())
            return false;

        if (!isset($params['id']))
            return false;

        $param_parts = explode('/', $params['id']);
        if (!isset($param_parts[0]) || !isset($param_parts[1]))
            return false;

        $test_name = $params['id'];
        $test_folder = $param_parts[0];
        $test_id = $param_parts[1];

        $test_file = "$test_id";
        $test_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $test_folder . DIRECTORY_SEPARATOR . $test_file . '.php';

        // test file doesn't exist
        if (!@file_exists($test_path))
        {
            $r['name'] = "Error: unable to locate the test: " . $test_name;
            return $r;
        }

        // load the job's php file and instantiate the job object
        include_once $test_path;
        $test = new Test;
        $results = array(); // array of TestResult

        if (!$test)
        {
            $r['name'] = "Error: unable to load the test: " . $test_name;
            return $r;
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

        return $r;
    }

    private static function addTests($subfolder, &$tests)
    {
        $search = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . '*.php';
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
}
