<?php
/*!
 *
 * Copyright (c) 2010-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2010-09-27
 *
 */


// stub is a file included by other processing scripts.  The purpose of stub
// is to process the command line parameters to set up a valid operating
// environment for GP in which scripts can access the appropriate databases


include_once __DIR__ . '/../application/bootstrap.php';


$g_context = null;
$g_context_param = '';

if (isset($argv))
{
    foreach ($argv as $arg)
    {
        if (substr($arg,0,10) == "--context=")
        {
            $g_context_param = trim(substr($arg,10));
            $context_decoded = base64_decode($g_context_param);
            $g_context = unserialize($context_decoded);
        }
    }
}

if (is_null($g_context) || !isset($g_context['utility_fullpath']))
{
    include_once 'stub.php';
    // TODO: log error message:
    // Util::logMessage('Error: error while invoking launcher.php - missing parameters');
}




// invoke the desired php file
$phpbin = Util::getBinaryPath('php');
$path = $g_context['utility_fullpath'];


$command = "$phpbin -f $path -- --context=" . $g_context_param;
$fp = popen($command, "r");

$output = '';
while (!feof($fp))
{
    $buffer = fgets($fp);
    if (strlen($buffer) > 0)
        $output .= $buffer;
}

$retcode = pclose($fp);

if (strlen(''.$output))
{
    include_once 'stub.php';
    // TODO: log message
    // Util::logMessage("Utility output: $output");
}
