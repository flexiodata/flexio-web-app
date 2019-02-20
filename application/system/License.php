<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2011-11-16
 *
 * @package flexio
 * @subpackage System
 */


declare(strict_types=1);
namespace Flexio\System;


class License
{
    public static function checkLicense(string &$err = null) : bool
    {
        $license_fname = \Flexio\System\System::getConfigDirectory() . DIRECTORY_SEPARATOR . 'license.dat';
        if (!file_exists($license_fname))
        {
            if (isset($err)) $err = 'missing_license_file';
            return false;
        }

        // load license
        $license = @file_get_contents($license_fname);
        if ($license === false)
        {
            if (isset($err)) $err = 'license_file_read_error';
            return false;
        }

        return self::isLicenseAuthentic($license, $err);
    }

    public static function decodeLicense(string $license, string &$err = null) // TODO: add return type
    {
        $license = preg_replace('/-{5}(BEGIN|END) LICENSE-{5}/', '', $license);
        $license = str_replace(array("\n", "\r", "\t", ' '), '', $license);
        $license = self::unjumbleBase64($license);

        // make sure the license is signed
        $sigv1 = self::getBlock($license, "SIGNATUREV1");
        if ($sigv1 === false)
        {
            if (isset($err)) $err = 'missing_signature';
            return false;
        }

        // make sure the license block itself exists
        $license_json = self::getBlock($license, "LICENSEINFO");
        if ($license_json === false)
        {
            if (isset($err)) $err = 'missing_license';
            return false;
        }

        // decode the license block
        $license = json_decode($license_json);
        if (!isset($license))
        {
            if (isset($err)) $err = 'decode_error';
            return false;
        }

        // make sure the signature included in the license matches
        $smd5 = md5(self::getSalt() . $license_json);

        if (trim($smd5) != trim($sigv1))
        {
            if (isset($err)) $err = 'not_authentic';
            return false;
        }

        if (isset($err))
            $err = 'success';
        return $license;
    }

    public static function isLicenseAuthentic(string $license, string &$err = null) : bool
    {
        $license = self::decodeLicense($license, $err);
        if ($license === false)
            return false;


        // make sure the computer's serverid matches the license
        $server_id = self::getServerId();
        $found = false;

        if (isset($license->serverid))
        {
            foreach ($license->serverid as $s)
            {
                if ($s == '*' || (trim(strtolower($s)) == trim(strtolower(self::getServerId()))))
                {
                    $found = true;
                    break;
                }
            }
        }

        if (!$found)
        {
            if (isset($err)) $err = 'server_not_authorized';
            return false;
        }


        // make sure the computer's clock is not set before the time
        // indicated in the license
        $cur_time = time();

        if (isset($license->curtime) && strlen(trim($license->curtime)) > 0)
        {
            $issuer_time = @strtotime($license->curtime . " UTC");
            if ($issuer_time === false)
            {
                if (isset($err)) $err = 'curtime_timestamp_invalid';
                return false;
            }

            if ($cur_time < $issuer_time && ($issuer_time - $cur_time) > 604800)
            {
                // computer time differs from issuer time by more than 7 days
                if (isset($err)) $err = 'local_time_invalid';
                return false;
            }
        }


        if ($license->expires != '*')
        {
            // check the expiration time
            $expire_time = @strtotime($license->expires . " UTC");
            if ($expire_time === false)
            {
                if (isset($err)) $err = 'expiration_timestamp_invalid';
                return false;
            }

            if ($cur_time > $expire_time)
            {
                // computer time differs from issuer time by more than 7 days
                if (isset($err)) $err = 'license_expired';
                return false;
            }
        }


        // verify local clock has not been manipulated
        if (!self::checkClock())
        {
            if (isset($err)) $err = 'local_time_setback';
            return false;
        }


        if (isset($err))
            $err = 'success';
        return true;
    }

    // checkClock() returns false if the system has had its clock
    // set back by more than a week
    public static function checkClock() : bool
    {
        $dirs = array();

        if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN")
        {
            $windir = getenv("windir");
            if ($windir !== false)
            {
                $dirs[] = $windir;
                $dirs[] = "$windir\\system32";
            }

            $dirs[] = getenv("ProgramFiles");
            $dirs[] = getenv("ProgramFiles(x86)");
            $dirs[] = sys_get_temp_dir();

            $paths = getenv("PATH");
            if ($paths !== false)
                $dirs = array_merge($dirs, explode(';', $paths));
        }
         else
        {
            $dirs[] = '/usr';
            $dirs[] = '/usr/bin';
            $dirs[] = '/etc';
            $dirs[] = sys_get_temp_dir();
            $dirs[] = getenv("HOME");

            $paths = getenv("PATH");
            if ($paths !== false)
                $dirs = array_merge($dirs, explode(':', $paths));
        }

        $cur_time = time();
        $strikes = 0;
        foreach ($dirs as $d)
        {
            if ($d !== false && is_dir($d))
            {
                $t = filemtime($d);
                if ($t > $cur_time && ($t - $cur_time) > 604800)
                    $strikes++;
            }
        }

        // if three or more directories have modification times greater
        // than one week from today, return false, otherwise true
        return ($strikes >= 3) ? false : true;
    }

    public static function getServerId(bool $use_cache = true) : string
    {
        if (isset($GLOBALS['g_serverid_hash']))
            return $GLOBALS['g_serverid_hash'];

        // getting the serial number from the OS is timewise expensive,
        // so we'll cache it once a day in the temp folder

        $hash = md5('493jgh5jf9aKl421-'.gmdate("Ymd"));
        $fname = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "gpcompid-${hash}.txt";
        if (file_exists($fname))
        {
            if ($use_cache)
                return file_get_contents($fname);
                 else
                @unlink($fname);
        }

        // return a hash string which uniquely represents the server
        if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN")
        {
            try
            {
                $obj = new \COM('winmgmts://./root/CIMV2' );
                $bios = $obj->ExecQuery("Select * from Win32_BIOS");
                $processor = $obj->ExecQuery("Select * from Win32_Processor");

                $arr = array();

                foreach ($bios as $info)
                    $arr[] = trim('' . $info->SerialNumber);

                foreach ($processor as $info)
                    $arr[] = trim('' . $info->ProcessorId);

                $arr = array_unique($arr);
                sort($arr);

                $str = implode(',', $arr);
                $str = str_replace(array(" ", "\n", "\r", "\t"), "", $str);
                $str = strtolower($str);

                $comp_id = md5($str);
            }
            catch(\Exception $e)
            {
                // exception occured, return stock static id
                $comp_id = 'a49540e2117f9c1569dcbbbbbd6c34dc';
            }
        }
         else
        {
            $fp = popen("/sbin/ifconfig | egrep \"(eth[0-9]|ether[ ]+..:)\" | grep -o ..:..:..:..:..:..", "r");
            $data = '';
            while (!feof($fp))
            {
                $buffer = fgets($fp);
                if (strlen($buffer) > 0)
                {
                    if (strlen($data) > 0)
                        $data .= ',';
                    $data .= $buffer;
                }
                usleep(50);
            }

            pclose($fp);

            $data = str_replace(array(" ", "\n", "\r", "\t"), "", $data);
            $data = strtolower($data);

            $comp_id = md5(trim($data));
        }

        $GLOBALS['g_serverid_hash'] = $comp_id;

        $files = glob(sys_get_temp_dir() . DIRECTORY_SEPARATOR . "gpcompid-*.*");
        foreach ($files as $f)
            @unlink($f);
        file_put_contents($fname, $comp_id);

        return $comp_id;
    }

    public static function unjumbleBase64(string $s) : string
    {
        $s = base64_decode($s);
        $r = '';
        for ($i = 0; $i < strlen($s); ++$i)
        {
            $ch = substr($s,$i,1);
            if (ord($ch) <= 127)
                $r .= $ch;
        }
        return $r;
    }

    private static function getBlock(string $s, string $key) : string
    {
        $s = str_replace(array("\n", "\r"), '', $s);
        $matches = array();
        if (preg_match("/(-{5}BEGIN ${key}-{5})(.*?)(-{5}END ${key}-{5})/", $s, $matches) == 0)
            return false;
        return base64_decode(trim($matches[2]));
    }

    private static function getSalt() : string
    {
        $salt = 'aFVmcnUzYXRyQXRhNGFudWhVZnJ1M2F0ckF0YTRhbnVyYXhBU3RlUkF6dWNV'.
                'Y3J1ZHJBcGhFWWF5VXRoQVpVNzc2UXV0ckF2VVZhZnJBM1JVY0g5dGhVWnV2'.
                'QWNydVNld2VDckFQZTdQVUU2YXNhdGFNYXplQ3JheEFTdGVSQXp1Y1VjcnVk'.
                'ckFwaEVZYXlVdGhBWkU2YXNhdGFNYUVEcmVwUlVTcHVHZVZhZnVIVXZVWmVD'.
                'cmV0ZXN0QUZSZXlVNHU1VVdhc1V6dVFlcHI5Y3J1dGhldDZydXplQw==';
        return base64_decode(trim($salt));
    }
}
