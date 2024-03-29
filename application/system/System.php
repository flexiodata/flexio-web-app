<?php
/**
 *
 * Copyright (c) 2010-2011, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2010-05-01
 *
 * @package flexio
 * @subpackage System
 */


declare(strict_types=1);
namespace Flexio\System;


class System
{
    public const SESSION_VERSION = 5;

    public static function createUserSession(string $user_eid) : bool
    {
        // creates session variables which store the authentication information
        // from a user eid; if the session creation succeeded, the method returns
        // true, otherwise it returns false

        global $g_store, $g_config;

        // clear any existing identity
        \Flexio\System\System::clearLoginIdentity();

        // if we haven't already started a session, start a new one
        fxStartSession();

        // generate a new session id to prevent session hijacking
        if (session_id())
        {
            // David was getting this error message, so we added the @
            // <br /> <b>Warning</b>:  session_regenerate_id(): Session object destruction failed.
            // ID: redis (path: tcp://localhost:6379) in <b>/media/sf_flexio/application/system/System.php</b>
            // on line <b>41</b><br /> {     "errors": [         {             "message": "Invalid username or
            //  password.",             "code": "general"         }     ] }
            @session_regenerate_id(true);
        }

        try
        {
            $user_model = \Flexio\System\System::getModel()->user;
            $user_info = $user_model->get($user_eid);

            // set new identity
            $_SESSION['env']['session_version'] = \Flexio\System\System::SESSION_VERSION;
            $_SESSION['env']['user_eid'] = $user_info['eid'];

            // make sure we don't have any inactivity value
            if (isset($_SESSION['last_activity']))
                unset($_SESSION['last_activity']);

            \Flexio\System\System::setupSessionAuth();

            return true;
        }
        catch (\Flexio\Base\Exception $e)
        {
        }

        // unable to set up the session
        return false;
    }

    public static function destroyUserSession() : void
    {
        \Flexio\System\System::clearLoginIdentity();
        @session_destroy();
        @setcookie('FXSESSID', '', time()-86400, '/');
    }

    // sets up a php call's user information
    public static function setupSessionAuth() : bool
    {
        global $g_store;

        if (IS_CLI())
            return false;

        // reset identity to nothing
        $g_store->user_eid = '';

        // try to authenticate the user using a session cookie
        if (!isset($_SESSION['env']['user_eid']) || strlen($_SESSION['env']['user_eid']) == 0)
            return false;
        if ($_SESSION['env']['session_version'] < \Flexio\System\System::SESSION_VERSION)
            return false;

        // set user info
        $g_store->user_eid = $_SESSION['env']['user_eid'];
        return true;
    }

    public static function setCurrentUserEid(string $user_eid) : void
    {
        $GLOBALS['g_store']->user_eid = $user_eid;
    }

    public static function getCurrentUserEid() : string
    {
        return $GLOBALS['g_store']->user_eid;
    }

    public static function clearLoginIdentity() : void
    {
        global $g_store;

        $g_store->user_eid = '';

        if (!IS_CLI())
        {
            if (session_id())
                $_SESSION['env']['user_eid'] = '';
        }
    }

    public static function serializeGlobalVars() : string
    {
        global $g_store;

        return base64_encode(serialize(
                array('user_eid'           => $g_store->user_eid)));
    }

    public static function unserializeGlobalVars(string $str) : void
    {
        global $g_store;
        $arr = unserialize(base64_decode($str));
        $g_store->user_eid = $arr['user_eid'];
    }

    public static function generateTemporaryAuthToken(string $user_eid) : ?string
    {
        $pk = $GLOBALS['g_config']->private_key ?? '';
        if (strlen($pk) === 0)
            return null;

        $time = ''.time();
        $token = "autht1:sha256:$user_eid:$time";

        $hash_input = "$user_eid:$time:$pk";
        $hash = hash('sha256', $hash_input);

        return "$token:$hash";
    }

    public static function getUserEidFromAuthToken(string $token) : ?string
    {
        $parts = explode(':', $token);
        if (count($parts) != 5)
            return null;
        if ($parts[0] != 'autht1')
            return null;
        return $parts[2];
    }

    public static function verifyTemporaryAuthToken(string $token, int $timestamp_tolerance = 60) : bool
    {
        $pk = $GLOBALS['g_config']->private_key ?? '';
        if (strlen($pk) === 0)
            return false;

        $parts = explode(':', $token);
        if (count($parts) != 5)
            return false;
        if ($parts[0] != 'autht1')
            return false;
        if ($parts[1] != 'sha256')
            return false;

        // verify hash is ok
        $hash_input = $parts[2] . ':' . $parts[3] . ':' . $pk;
        $hash = hash('sha256', $hash_input);

        if ($parts[4] != $hash)
            return false; // hash mismatch

        // check to make sure the timestamp is within the correct timespan
        $timestamp = (int)$parts[3];
        $cur_time = time();
        if ($timestamp < ($cur_time - $timestamp_tolerance) || $timestamp > ($cur_time + $timestamp_tolerance))
        {
            // timestamp is invalid
            return false;
        }

        return true;
    }

    public static function resetModel() : void
    {
        // this function resets the store reference to the model so that
        // a new model can be created with getModel() that uses updated
        // database configuration settings
        global $g_store;
        $g_store->model = null;
    }

    public static function getModel() : \Model
    {
        global $g_store;
        if (isset($g_store->model))
            return $g_store->model;

        // note: always return dates in the API using UTC rather than local
        // timezone; the UI will do the appropriate conversion; see
        // date_default_timezone_set('UTC') in bootstrap.php for the default

        require_once dirname(__DIR__) . '/models/Model.php';

        $model = new \Model;
        $model->setTimezone('UTC');
        $g_store->model = $model;

        return $model;
    }

    public static function getSearchCache() : \Flexio\Services\ElasticSearch
    {
        global $g_store;
        if (isset($g_store->search_cache))
            return $g_store->search_cache;

        $elasticsearch_connection_info = array(
            'type'     => $GLOBALS['g_config']->search_cache_type ?? '',
            'host'     => $GLOBALS['g_config']->search_cache_host ?? '',
            'port'     => $GLOBALS['g_config']->search_cache_port ?? '',
            'username' => $GLOBALS['g_config']->search_cache_username ?? '',
            'password' => $GLOBALS['g_config']->search_cache_password ?? ''
        );

        $search_cache = \Flexio\Services\ElasticSearch::create($elasticsearch_connection_info);
        $g_store->search_cache = $search_cache;
        return $search_cache;
    }

    public static function getStoreStreamPath() : string
    {
        $storage_root = $GLOBALS['g_config']->storage_root ?? '';
        if (strlen($storage_root) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        $storage_stream_path = ($storage_root . DIRECTORY_SEPARATOR . 'streams');

        if (IS_DEBUG() && !is_writable($storage_stream_path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS, "Cannot write to streams store directory");

        return $storage_stream_path;
    }

    public static function getStoreTempPath() : string
    {
        $storage_root = $GLOBALS['g_config']->storage_root ?? '';
        if (strlen($storage_root) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        $storage_temp_path = ($storage_root . DIRECTORY_SEPARATOR . 'tmp');

        if (IS_DEBUG() && !is_writable($storage_temp_path))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS, "Cannot write to tmp store directory");

        return $storage_temp_path;
    }

    public static function getStoreTempFile(string $filename) : string
    {
        // get the base storage path
        $storage_tmpbase = \Flexio\System\System::getStoreTempPath();

        // trim off trailing/preceding slashes and whitespace
        $filename = trim($filename, "\ \t\n\r\0\x0B");

        foreach (explode('/', $filename) as $part)
        {
            if ($part == '..')
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "relative paths not allowed");
        }

        $str = $storage_tmpbase . DIRECTORY_SEPARATOR . $filename;
        if (DIRECTORY_SEPARATOR != '/')
            $str = str_replace('/', DIRECTORY_SEPARATOR, $str);

        return $str;
    }

    public static function getLocaleDateFormat(string $lang) : string
    {
        $lang_prefix = substr($lang, 0, 2);

        switch ($lang_prefix)
        {
            default:
            case 'en':  $res = "%B %e, %Y"; break;
            case 'fr':  $res = "%e %B %Y"; break;
            case 'de':  $res = "%e. %B %Y"; break;
            case 'es':  $res = "%e %B %Y"; break;
        }

        if (\Flexio\System\System::isPlatformWindows())
            $res = str_replace('%e', '%#d', $res);

        return $res;
    }

    public static function getLocaleDateTimeFormat(string $lang) : string
    {
        $lang_prefix = substr($lang, 0, 2);

        switch ($lang_prefix)
        {
            default:
            case 'en':  $res = "%B %e, %Y at %I:%M %p";     break;
            case 'fr':  $res = "%e %B %Y &agrave; %H:%M"; break;
            case 'de':  $res = "%e. %B %Y um %H.%M";       break;
            case 'es':  $res = "%e %B %Y a %H:%M";        break;
        }

        if (\Flexio\System\System::isPlatformWindows())
            $res = str_replace('%e', '%#d', $res);

        return $res;
    }

    public static function getTimestamp() : string
    {
        // gives us a way of getting a consistent time in
        // order to set the created/updated database values
        // to the same value across calls

        global $g_store;
        if (isset($g_store->timestamp))
            return $g_store->timestamp;

        //$g_store->timestamp = date("Y-m-d H:i:s", time());
        $g_store->timestamp = \Flexio\Base\Util::getCurrentTimestamp(); // use more precise timestamp
        return $g_store->timestamp;
    }



    public static function openPhpInputStream()
    {
        if (isset($_GET['fxpdata']))
        {
            $stream = fopen('php://memory','r+');
            fwrite($stream, hex2bin($_GET['fxpdata']));
            rewind($stream);
            return $stream;
        }
         else
        {
            return fopen('php://input', 'rb');
        }
    }

    public static function getPhpInputStreamContentType()
    {
        if (isset($_GET['fxpdata']))
        {
            return $_GET['fxpdatact'] ?? '';
        }
         else
        {
            return $_SERVER['CONTENT_TYPE'] ?? '';
        }
    }

    public static function getBaseUrl() : string
    {
        return 'https://' . $_SERVER['SERVER_NAME'];
    }

    public static function getUserVerificationLink(string $email_to, string $verify_code) : string
    {
        return self::getBaseUrl() . '/app/signup/verify?ref=verification_email&email='.urlencode($email_to).'&verify_code='.$verify_code;
    }

    public static function getPasswordResetLink(string $email_to, string $verify_code) : string
    {
        return self::getBaseUrl() . '/app/resetpassword?email='.urlencode($email_to).'&verify_code='.$verify_code;
    }

    public static function getTeamInviteLink(string $email_to, string $verify_code, string $team_name) : string
    {
        $link = self::getBaseUrl() . '/app/'.$team_name.'/members/join?email='.urlencode($email_to);

        // if the user already exists and is verified,
        // don't add a verify code to the team invite link
        try
        {
            // get the eid for the user joining
            $user_eid = \Flexio\Object\User::getEidFromEmail($email_to);
            if (!$user_eid)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $current_user = \Flexio\Object\User::load($user_eid);
            if ($current_user->getStatus() !== \Model::STATUS_AVAILABLE)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            // return url without verify code
            return $link;
        }
        catch (\Flexio\Base\Exception $e)
        {
            // return url with verify code
            $link .= '&verify_code='.$verify_code;
            return $link;
        }
    }

    public static function getBaseDirectory() : string
    {
        global $g_store;
        if (isset($g_store->dir_home))
            return $g_store->dir_home;

        return dirname(dirname(dirname(__FILE__)));
    }

    public static function getPublicDirectory() : string
    {
        return \Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'public';
    }

    public static function getApplicationDirectory() : string
    {
        return \Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'application';
    }

    public static function getConfigDirectory() : string
    {
        return \Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'config';
    }

    public static function getEmailTemplateDirectory() : string
    {
        return \Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'email';
    }

    public static function getTestDataDirectory() : string
    {
        return \Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'testdata';
    }

    public static function getUpdateDirectory() : string
    {
        return (\Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'update');
    }

    public static function getUpdateVersionFromFilename(string $filename) : int
    {
        $version = 0;
        $matches = array();
        if (preg_match("/update(\d+)/", $filename, $matches))
            $version = intval($matches[1]);

        return $version;
    }

    public static function getLatestVersionNumber() : int
    {
        // gets the version number of the database that is available
        // to update to (this corresponds to the highest number in update####
        // in the list of updates)

        // get the update directory
        $update_dir = \Flexio\System\System::getUpdateDirectory();

        // find the latest system version from the update directory
        $version = 0;

        if ($handle = opendir($update_dir))
        {
            while (false !== ($file = readdir($handle)))
            {
                if ($file == "." || $file == "..")
                    continue;

                $version_int = \Flexio\System\System::getUpdateVersionFromFilename($file);
                if ($version_int > $version)
                    $version = $version_int;
            }

            closedir($handle);
        }

        return $version;
    }

    public static function getPackageInfo() : array
    {
        $package = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'package.json';
        $package_info = @file_get_contents($package);
        if ($package_info === false)
            return array();
        $package_info = @json_decode($package_info, true);
        if ($package_info === false)
            return array();
        return $package_info;
    }

    public static function getGitRevision() : string
    {
        $path = dirname(dirname(__DIR__)) . '/.git/refs/heads/master';
        $str = @file_get_contents($path);
        if (!$str)
            $str = '';
        return trim($str);
    }

    public static function getBinaryPath(string $bin) : ?string
    {
        $fxhome = \Flexio\System\System::getBaseDirectory();
        $base_path = dirname($fxhome);

        // on some newer windows setups, we've been installing the server software
        // separately from the code tree
        // \fxsite\flexio - code
        // \fxsite\server\php\php.exe

        if (is_dir($base_path . DIRECTORY_SEPARATOR . 'server'))
            $base_path .= (DIRECTORY_SEPARATOR . 'server');

        if (\Flexio\System\System::isPlatformWindows())
        {
            // running on windows -- we need to fully qualify the exe path
            switch ($bin)
            {
                case 'grep':        return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'unixutil'        . DIRECTORY_SEPARATOR . 'grep.exe';
                // TODO: add paths for python, nodejs, golang
                case 'r':           return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'rlang'           . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'r-portable' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'x64' . DIRECTORY_SEPARATOR . 'rscript.exe';
                case 'gs':          return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'gs'              . DIRECTORY_SEPARATOR . 'gswin32c.exe';
                case 'unzip':       return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'zipwin32'        . DIRECTORY_SEPARATOR . 'unzip.exe';
                case 'zip':         return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'zipwin32'        . DIRECTORY_SEPARATOR . 'zip.exe';
                case 'php':         return $base_path . DIRECTORY_SEPARATOR . 'php'     . DIRECTORY_SEPARATOR . 'php.exe';
                case 'mysql':       return $base_path . DIRECTORY_SEPARATOR . 'mysql'   . DIRECTORY_SEPARATOR . 'bin'             . DIRECTORY_SEPARATOR . 'mysql.exe';
                case 'mysqldump':   return $base_path . DIRECTORY_SEPARATOR . 'mysql'   . DIRECTORY_SEPARATOR . 'bin'             . DIRECTORY_SEPARATOR . 'mysqldump.exe';
                case 'pdftk':       return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'pdftk'           . DIRECTORY_SEPARATOR . 'pdftk.exe';
                case 'phantomjs':   return $fxhome    . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'phantomjs-1.9.1' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'windows'     . DIRECTORY_SEPARATOR . 'phantomjs.exe';
                default:            return null;
            }
        }
         else
        {
            $xtra_bin_dir = '';
            if (\Flexio\System\System::isXampp() && \Flexio\System\System::isPlatformMac())
                $xtra_bin_dir = PHP_BINDIR.'/';

            $phantom_js_platform_folder = 'linux64';
            if (\Flexio\System\System::isPlatformMac())
                $phantom_js_platform_folder = 'macosx';

            // running on linux, no need to fully qualify exe path
            switch ($bin)
            {
                case 'grep':        return 'grep';
                case 'docker':      return 'docker';
                case 'python':      return 'python3';
                case 'nodejs':      return 'nodejs';
                case 'javascript':  return 'nodejs';
                case 'go':          return 'go run';
                case 'r':           return 'rscript';

                case 'php':
                    if (strlen($xtra_bin_dir) > 0)
                        return $xtra_bin_dir . 'php';
                         else
                        return '/usr/bin/php';

                case 'gs':          return 'gs';
                case 'unzip':       return $xtra_bin_dir . 'unzip';
                case 'zip':         return $xtra_bin_dir . 'zip';
                case 'mysql':       return $xtra_bin_dir . 'mysql';
                case 'mysqldump':   return $xtra_bin_dir . 'mysqldump';
                case 'pdftk':       return 'pdftk';
                case 'phantomjs':   return 'phantomjs';
                default:            return null;
            }
        }

        return null;
    }

    public static function isPlatformWindows() : bool
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") ? true : false;
    }

    public static function isPlatformMac() : bool
    {
        return (strtoupper(substr(PHP_OS, 0, 6)) == "DARWIN") ? true : false;
    }

    public static function isPlatformLinux() : bool
    {
        return (strtoupper(substr(PHP_OS, 0, 5)) == "LINUX") ? true : false;
    }

    public static function isXampp() : bool
    {
        return (strpos(strtoupper(PHP_BINDIR), "XAMPP") !== false) ? true : false;
    }

    public static function isNewInstallation() : bool
    {
        $f1 = file_exists(\Flexio\System\System::getConfigDirectory() . DIRECTORY_SEPARATOR . 'config.json');
        return $f1 ? false : true;
    }

    public static function updateConfigSetting(string $fname, string $setting, string $value) : bool
    {
        // prevent code injection
        $value = str_replace("\\", "\\\\", $value);
        $value = str_replace("'", "", $value);

        $contents = @file_get_contents($fname);
        if ($contents === false)
            return false;

        $setting = preg_quote($setting);
        $contents = preg_replace("/(\"$setting\"\\s*:\\s*\")([^\"]*?)(\"\\s*)/", '${1}' . $value . '${3}', $contents);

        if (file_put_contents($fname, $contents) === false)
            return false;

        return true;
    }

    public static function getConfig() // TODO: add return type
    {
        return $GLOBALS['g_config'];
    }

    public static function logTimestamp(string $str) : void
    {
        $t1 = (int)microtime(true);
        $t1_micropart = sprintf("%06d", ($t1 - floor($t1)) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $t1_micropart, $t1));
        $timestamp = $date->format("Y-m-d H:i:s.u");

        $log = "Timestamp: $timestamp\n$str";
        self::log($log);
    }

    public static function log(string $str) : void
    {
        if (!isset($GLOBALS['g_config']->query_log))
            return;

        $query_log = $GLOBALS['g_config']->query_log;
        $str = str_replace("\n", "\r\n", $str);

        if (strlen($query_log) == 0)
            return;

        if (!isset($GLOBALS['query_log_first_write']))
        {
            $GLOBALS['query_log_first_write'] = true;
            $GLOBALS['query_log_count'] = 0;
            $GLOBALS['query_log_lines'] = [];
            $GLOBALS['query_log_lines'][] = "\r\n--------------------------------------------\r\n".($_SERVER['REQUEST_URI'] ?? '(no request)')."\r\n\r\n";

            register_shutdown_function(function () {
                $lines = join('', $GLOBALS['query_log_lines']);
                file_put_contents($GLOBALS['g_config']->query_log, $lines, FILE_APPEND);
            });
        }

        $count = ++$GLOBALS['query_log_count'];
        $GLOBALS['query_log_lines'][] = sprintf("#%04d: ", $count) . $str;

        if ($count > 10000)
        {
            // flush the query log, because it's getting too big
            $lines = join('', $GLOBALS['query_log_lines']);
            file_put_contents($GLOBALS['g_config']->query_log, $lines, FILE_APPEND);

            $GLOBALS['query_log_count'] = 0;
            $GLOBALS['query_log_lines'] = [];
        }
    }
}
