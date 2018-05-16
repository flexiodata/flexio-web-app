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


declare(strict_types=1);
namespace Flexio\System;


class Program
{
    public static function exec(string $cmdline, bool $wait = false) : void
    {
        if (\Flexio\System\System::isPlatformWindows())
        {
            $wsh_shell = new \COM("WScript.Shell");
            $exec = $wsh_shell->Run("$cmdline", 0, $wait);
        }
         else
        {
            $cmd = "$cmdline > /dev/null";
            if (!$wait)
                $cmd .= " &";
            exec($cmd);
        }
    }

    // this is a function that will pop up a traditional
    // GUI message box, which can be useful for debugging
    // At present, it only works on Win32
    public static function messageBox(string $msg, string $caption = 'Message') : void
    {
        // to show object data, use var_export (similar to var_dump):
        // \Flexio\System\Program::messageBox(var_export($validation_info,true));

        if (\Flexio\System\System::isPlatformWindows())
        {
            $wsh_shell = new \COM('WScript.Shell');
            $wsh_shell->Popup($msg, 0, $caption, 0x1040);
        }
    }

    public static function notepad(string $msg) : void
    {
        $filename = \Flexio\Base\File::createTempFile('', 'txt');
        file_put_contents($filename, $msg);

        $wsh_shell = new \COM("WScript.Shell");
        $exec = $wsh_shell->Run("notepad $filename", 1, true);

        unlink($filename);
    }

    public static function runInBackground(string $code, bool $wait = false) : void
    {
        $phpbin = \Flexio\System\System::getBinaryPath('php');

        $stubphp = \Flexio\System\System::getBaseDirectory();
        $stubphp = str_replace("\\", "/", $stubphp);
        $stubphp .= '/scripts/stub.php';

        $curidentity = \Flexio\System\System::serializeGlobalVars();
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
        self::execute($command, $wait);
    }

    public static function execute(string $command, bool $wait = false) : void
    {
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

