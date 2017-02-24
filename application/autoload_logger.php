<?php

// this logs all classes loaded using the autoloader
$autoloader_log = '/tmp/autoloader.txt';
//$autoloader_log = 'c:\fxsite\flexio\autoloader.txt';
$fp = fopen($autoloader_log, 'a+');
fwrite($fp, "\n-------".$_SERVER['REQUEST_URI']."------------\n");
fclose($fp);

spl_autoload_register(function ($class) {
    global $g_store, $autoloader_log;

    $bt = debug_backtrace();
    $idx = 0; $found = false;
    for ($idx = 0; $idx < count($bt); ++$idx)
    {
        if (isset($bt[$idx]['file']))
        {
            $found = true;
            break;
        }
    }
    if (!$found)
        return false;
    $fp = fopen($autoloader_log, 'a+');
    fwrite($fp, sprintf("%-25s", $class) .
                ' ('.  str_replace($g_store->dir_home, '', $bt[$idx]['file']) .
                ' line '. $bt[$idx]['line'] .")\n");
    fclose($fp);
    return false;
}, true, true);
