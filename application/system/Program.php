<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; David Z. Williams; Aaron L. Williams
 * Created:  2017-03-08
 *
 * @package flexio
 * @subpackage System
 */


namespace Flexio\System;


class Program
{
    public static function runInBackground($code, $wait = false)
    {
        $phpbin = \Flexio\System\System::getBinaryPath('php');

        $stubphp = \Flexio\System\System::getBaseDirectory();
        $stubphp = str_replace("\\", "/", $stubphp);
        $stubphp .= '/scripts/stub.php';

        $curidentity = \Flexio\System\System::serializeGlobalVars();
        $curlang = $GLOBALS['g_store']->lang;
        $cursessid = session_id();
        $curservername = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';

        $httphost = GET_HTTP_HOST();
        if (is_null($httphost))
            $httphost = 'null';
             else
            $httphost = "'$httphost'";

        $runcode = <<<EOT
            include_once '$stubphp';
            class DeleteOnExit {
                function __destruct() {
                    unlink(__FILE__);
                }
            }
            \$g_delete_on_exit = new \\DeleteOnExit();
            session_id('$cursessid');
            if (strlen('$curservername') > 0)
                \$_SERVER['SERVER_NAME'] = '$curservername';
            \\Flexio\\System\\System::unserializeGlobalVars('$curidentity');
            \\Flexio\\System\\System::setCurrentLanguage('$curlang');
            \$GLOBALS['g_store']->http_host = $httphost;
            $code;
EOT;

        $runcode = ("<" . "?" . "php " . $runcode);

        $tmpfname = tempnam(sys_get_temp_dir(), 'cde');
        $handle = fopen($tmpfname, "w");
        fwrite($handle, $runcode);
        fclose($handle);
        chmod($tmpfname, 0600);


        $command = "$phpbin -f \"$tmpfname\"";
        if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN")
        {
            $wsh_shell = new \COM("WScript.Shell");
            $exec = $wsh_shell->Run($command, 0, $wait);
        }
         else
        {
            if ($wait)
                $suffix = '';
                 else
                $suffix = '&';

            exec("$command > /dev/null $suffix");
        }
    }
}

