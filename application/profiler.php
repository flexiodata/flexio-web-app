<?php

function shutdown_profiler()
{
    $xhprof_data = xhprof_disable();

    //$str = var_export($xhprof_data,true);
    //file_put_contents('/tmp/xhprof.txt', $str);

    include_once "/usr/share/php/xhprof_lib/utils/xhprof_lib.php";
    include_once "/usr/share/php/xhprof_lib/utils/xhprof_runs.php";
    $xhprof_runs = new XHProfRuns_Default();
    $xhprof_runs->save_run($xhprof_data, "fx");
}

if (isset($g_config->profiling) && $g_config->profiling && function_exists('xhprof_enable'))
{
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    register_shutdown_function('shutdown_profiler');
}
