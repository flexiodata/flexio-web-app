<?php

// This script verifies that the user is using ssl when logging in
function IS_SECURE()
{
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        return true;
    return (isset($_SERVER['HTTPS']) && strlen($_SERVER['HTTPS']) > 0);
}

if (substr($_SERVER['REQUEST_URI'],0,16) == "/login" && !IS_SECURE())
{
    $redirect = sprintf("Location: https://%s%s",
                        $_SERVER['HTTP_HOST'],
                        $_SERVER['REQUEST_URI']);

    $httpsport = '443';
    
    if ($_SERVER['SERVER_PORT'] == 8080 || $_SERVER['REDIRECT_PORT'] == 8080)
        $httpsport = '8443';

    $redirect = preg_replace('/:\d{2,5}\//', ":$httpsport/", $redirect);

    header($redirect);
    exit();
}

