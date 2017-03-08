<?php
/**
 *
 * Copyright (c) 2010-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2010-05-01
 *
 * @package flexio
 * @subpackage System
 */


namespace Flexio\System;


class System
{
    const SESSION_VERSION = 5;

    // login() authenticates a username and password and, if valid,
    // creates session variables which store the authentication information;
    // if the authentication succeeded, the method returns true, otherwise
    // it returns false

    public static function login($username, $password, &$error_message = null)
    {
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

        if (substr($username, 0, 7) == '@autht:')
        {
            // authentication via temporary login token -- check if valid
            $result = \Flexio\System\System::verifyTemporaryLoginCredentials($username, $password);
            if ($result === false)
                return false;

            // clear any existing identity
            \Flexio\System\System::clearLoginIdentity();

            $_SESSION['env']['session_version'] = \Flexio\System\System::SESSION_VERSION;
            $_SESSION['env']['user_first_name'] = '';
            $_SESSION['env']['user_last_name'] = '';
            $_SESSION['env']['user_name'] = 'nobody';
            $_SESSION['env']['user_email'] = '';
            $_SESSION['env']['user_eid'] = '';
            $_SESSION['env']['lang'] = '';
            $_SESSION['env']['thousands_separator'] = ',';
            $_SESSION['env']['decimal_separator'] = '.';
            $_SESSION['env']['date_format'] = 'm/d/Y';
            $_SESSION['env']['timezone'] = 'UTC';

            return true;
        }

        // check if password is correct and get info about the user
        $user_model = \Flexio\System\System::getModel()->user;
        if (!$user_model)
            return false;

        if (!$user_model->checkUserPassword($username, $password))
        {
            $error_message = _('Invalid username or password.');
            return false;
        }

        $user_eid = $user_model->getEidFromIdentifier($username);
        $user_info = $user_model->get($user_eid);

        if (!$user_info)
            return false;

        // if the user has been deleted or the status is anything besides
        // active, return false
        if ($user_info['eid_status'] == \Model::STATUS_DELETED)
        {
            return false;
        }
         else
        {
            if ($user_info['eid_status'] != \Model::STATUS_AVAILABLE)
            {
                $error_message = _('Account not verified.  Please verify your account.');
                return false;
            }
        }

        // set new identity
        $_SESSION['env']['session_version'] = \Flexio\System\System::SESSION_VERSION;
        $_SESSION['env']['user_first_name'] = $user_info['first_name'];
        $_SESSION['env']['user_last_name'] = $user_info['last_name'];
        $_SESSION['env']['user_name'] = $user_info['user_name'];
        $_SESSION['env']['user_email'] = $user_info['email'];
        $_SESSION['env']['user_eid'] = $user_info['eid'];
        $_SESSION['env']['lang'] = $user_info['locale_language'];
        $_SESSION['env']['thousands_separator'] = $user_info['locale_thousands'];
        $_SESSION['env']['decimal_separator'] = $user_info['locale_decimal'];
        $_SESSION['env']['date_format'] = $user_info['locale_dateformat'];
        $_SESSION['env']['timezone'] = $user_info['timezone'];

        // make sure we don't have any inactivity value
        if (isset($_SESSION['last_activity']))
            unset($_SESSION['last_activity']);

        \Flexio\System\System::setupSessionAuth();

        //session_write_close();

        return true;
    }

    // sets up a php call's user information
    public static function setupSessionAuth()
    {
        global $g_store;

        if (IS_CLI())
            return false;

        // reset identity to nothing
        $g_store->user_first_name = '';
        $g_store->user_last_name = '';
        $g_store->user_name = '';
        $g_store->user_email = '';
        $g_store->user_eid = '';
        $g_store->lang = '';
        $g_store->thousands_separator = ',';
        $g_store->decimal_separator = '.';
        $g_store->date_format = 'm/d/Y';
        $g_store->timezone = 'UTC';


        // next, check if there is an authorization header -- this takes precedence over any session cookie
        $headers = apache_request_headers();
        if (isset($headers['Authorization']))
        {
            $auth_header = trim($headers['Authorization']);

            $pos = strpos($auth_header, ' ');
            $auth_type = ($pos === false) ? $auth_header : substr($auth_header, 0, $pos);
            $params_raw = ($pos === false) ? '' : trim(substr($auth_header, $pos+1));
            $user_info = false;

            if ($auth_type == 'Bearer')
            {

                $access_code = trim($params_raw);

                $token_info = \Flexio\System\System::getModel()->token->getInfoFromAccessCode($access_code);
                if (!$token_info)
                {
                    // unknown user
                    \Flexio\System\Util::header_error(401);
                    exit(0);
                }

                $user = \Flexio\Object\User::load($token_info['user_eid']);
                if ($user === false)
                {
                    // deleted user
                    \Flexio\System\Util::header_error(401);
                    exit(0);
                }

                $user_info = $user->get();
            }
            /*
             else if ($auth_type == 'FLEXIO1-HMAC-SHA256')
            {
                if (strlen($params_raw) == 0)
                {
                    $params = [];
                }
                else
                {
                    $params = explode(',',$params_raw);
                    $params = array_map('trim', $params);
                }

                $credential = null;
                $signature = null;
                foreach ($params as $param)
                {
                    $eq = strpos($param, '=');
                    if ($eq !== false)
                    {
                        $param_name = substr($param, 0, $eq);
                        $param_value = substr($param, $eq+1);

                        if ($param_name == 'Credential') $credential = $param_value;
                        if ($param_name == 'Signature')  $signature = $param_value;
                    }
                }
            }
            */
             else
            {
                // unknown algorith/auth type
                \Flexio\System\Util::header_error(404);
                exit(0);
            }

            if (!$user_info)
            {
                // unknown algorith/auth type
                \Flexio\System\Util::header_error(404);
                exit(0);
            }

            // set user info
            $g_store->user_first_name = $user_info['first_name'];
            $g_store->user_last_name = $user_info['last_name'];
            $g_store->user_name = $user_info['first_name'] . ' ' . $user_info['last_name'];
            $g_store->user_email = $user_info['email'];
            $g_store->user_eid = $user_info['eid'];
            $g_store->lang = $user_info['locale_language'];
            $g_store->thousands_separator = $user_info['locale_thousands'];
            $g_store->decimal_separator = $user_info['locale_decimal'];
            $g_store->date_format = $user_info['locale_dateformat'];
            $g_store->timezone = $user_info['timezone'];
        }
         else
        {
            if (!isset($_SESSION['env']['user_name']) || strlen($_SESSION['env']['user_name']) == 0)
                return false;
            if ($_SESSION['env']['session_version'] < \Flexio\System\System::SESSION_VERSION)
                return false;

            // set user info
            $g_store->user_first_name = $_SESSION['env']['user_first_name'];
            $g_store->user_last_name = $_SESSION['env']['user_last_name'];
            $g_store->user_name = $_SESSION['env']['user_name'];
            $g_store->user_email = $_SESSION['env']['user_email'];
            $g_store->user_eid = $_SESSION['env']['user_eid'];
            $g_store->lang = $_SESSION['env']['lang'];
            $g_store->thousands_separator = $_SESSION['env']['thousands_separator'];
            $g_store->decimal_separator = $_SESSION['env']['decimal_separator'];
            $g_store->date_format = $_SESSION['env']['date_format'];
            $g_store->timezone = $_SESSION['env']['timezone'];
        }

/*
        // note: always return dates in the API using UTC rather than local
        // timezone; the UI will do the appropriate conversion; see
        // date_default_timezone_set('UTC') in bootstrap.php for the default

        // set current timezone
        if (!@date_default_timezone_set($g_store->timezone))
        {
            // bad timezone identifier -- use UTC
            $g_store->timezone = 'UTC';
        }
*/
        // set the language to use
        if (strlen($g_store->lang) > 0)
            \Flexio\System\System::setCurrentLanguage($g_store->lang);

        return true;
    }

    public static function setCurrentLanguage($lang)
    {
        $GLOBALS['g_store']->lang = $lang;
        \Flexio\System\Translate::setLocale($lang);
    }

    public static function clearLoginIdentity()
    {
        global $g_store;

        $g_store->user_first_name = '';
        $g_store->user_last_name = '';
        $g_store->user_name = '';
        $g_store->user_email = '';
        $g_store->user_eid = '';

        if (!IS_CLI())
        {
            if (session_id())
            {
                $_SESSION['env']['user_first_name'] = '';
                $_SESSION['env']['user_last_name'] = '';
                $_SESSION['env']['user_name'] = '';
                $_SESSION['env']['user_email'] = '';
                $_SESSION['env']['user_eid'] = '';
            }
        }
    }

    public static function serializeGlobalVars()
    {
        global $g_store;

        return base64_encode(serialize(
                array('user_first_name'    => $g_store->user_first_name,
                      'user_last_name'     => $g_store->user_last_name,
                      'user_name'          => $g_store->user_name,
                      'user_email'         => $g_store->user_email,
                      'user_eid'           => $g_store->user_eid)));
    }

    public static function unserializeGlobalVars($str)
    {
        global $g_store;
        $arr = unserialize(base64_decode($str));

        $g_store->user_first_name = $arr['user_first_name'];
        $g_store->user_last_name = $arr['user_last_name'];
        $g_store->user_name = $arr['user_name'];
        $g_store->user_email = $arr['user_email'];
        $g_store->user_eid = $arr['user_eid'];
    }

    public static function verifyTemporaryLoginCredentials($login, $password, $maxage = 60 /* max age in seconds allowed */)
    {
        global $g_config;

        // verify signature of temporary logins is present
        if (substr($login, 0, 7) != '@autht:')
            return false;

        $enc_login = substr($login, 7);
        $enc_login = base64_decode($enc_login);

        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
        $iv = substr($enc_login, 0, $iv_size);
        $enc_login = substr($enc_login, $iv_size);

        $plain_login = mcrypt_decrypt(MCRYPT_BLOWFISH, substr($g_config->private_key, 0, 32), $enc_login, MCRYPT_MODE_CBC, $iv);
        $plain_login = trim($plain_login, "\0");

        // parse colon-delimited string
        $arr = explode(':', $plain_login);
        if (count($arr) != 3)
        {
            // invalid number of string parts
            return false;
        }

        // first, verify that the timestamp is less than 1 minute old
        $timestamp = (int)$arr[2];
        $cur_time = time();
        if ($timestamp < ($cur_time - $maxage) || $timestamp > $cur_time)
        {
            // timestamp is invalid
            return false;
        }

        // next calculate the password hash -- the caller's supplied hash must match this
        $hash_input = $plain_login . ':' . $g_config->private_key;
        $hash = sha1($hash_input);
        if ($password != $hash)
        {
            // supplied password/hash is invalid
            return false;
        }

        return true;
    }

    public static function resetModel()
    {
        // this function resets the store reference to the model so that
        // a new model can be created with getModel() that uses updated
        // database configuration settings
        global $g_store;
        $g_store->model = null;
    }

    public static function getModel()
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

    public static function setLocaleSettings($params)
    {
        // NB: Make sure call this function before session_write_close()
        // has been invoked

        global $g_store;
        $session_active = isset($_SESSION);


        if (isset($params['locale_language']))
        {
            $g_store->lang = $params['locale_language'];
            if ($session_active)
                $_SESSION['env']['lang'] = $params['locale_language'];
        }

        if (isset($params['locale_thousands']))
        {
            $g_store->thousands_separator = $params['locale_thousands'];
            if ($session_active)
                $_SESSION['env']['thousands_separator'] = $params['locale_thousands'];
        }

        if (isset($params['locale_decimal']))
        {
            $g_store->decimal_separator = $params['locale_decimal'];
            if ($session_active)
                $_SESSION['env']['decimal_separator'] = $params['locale_decimal'];
        }

        if (isset($params['locale_dateformat']))
        {
            $g_store->date_format = $params['locale_dateformat'];
            if ($session_active)
                $_SESSION['env']['date_format'] = $params['locale_dateformat'];
        }

        if (isset($params['timezone']))
        {
            $g_store->timezone = $params['timezone'];
            if ($session_active)
                $_SESSION['env']['timezone'] = $params['timezone'];
            date_default_timezone_set($g_store->timezone);
        }
    }

    public static function getLocaleShortDateFormat()
    {
        return $GLOBALS['g_store']->date_format;
    }

    public static function getLocaleDateFormat()
    {
        $lang = $GLOBALS['g_store']->lang;
        $lang_prefix = substr($lang, 0, 2);

        switch ($lang_prefix)
        {
            default:
            case 'en':  $res = "%B %e, %Y"; break;
            case 'fr':  $res = "%e %B %Y"; break;
            case 'de':  $res = "%e. %B %Y"; break;
            case 'es':  $res = "%e %B %Y"; break;
        }

        if (\Flexio\System\Util::isPlatformWindows())
            $res = str_replace('%e', '%#d', $res);

        return $res;
    }

    public static function getLocaleDateTimeFormat()
    {
        $lang = $GLOBALS['g_store']->lang;
        $lang_prefix = substr($lang, 0, 2);

        switch ($lang_prefix)
        {
            default:
            case 'en':  $res = "%B %e, %Y at %I:%M %p";     break;
            case 'fr':  $res = "%e %B %Y &agrave; %H:%M"; break;
            case 'de':  $res = "%e. %B %Y um %H.%M";       break;
            case 'es':  $res = "%e %B %Y a %H:%M";        break;
        }

        if (\Flexio\System\Util::isPlatformWindows())
            $res = str_replace('%e', '%#d', $res);

        return $res;
    }

    public static function getCurrentThousandsSeparator()
    {
        return $GLOBALS['g_store']->thousands_separator;
    }

    public static function getCurrentDecimalSeparator()
    {
        return $GLOBALS['g_store']->decimal_separator;
    }

    public static function getCurrentLanguage()
    {
        return $GLOBALS['g_store']->lang;
    }

    public static function getCurrentTimezone()
    {
        return $GLOBALS['g_store']->timezone;
    }

    public static function getCurrentTimezoneOffsetInMinutes()
    {
        $tz_utc = new \DateTimeZone('UTC');
        $tz_local = new \DateTimeZone($GLOBALS['g_store']->timezone);
        $dt_local = new \DateTime('now', $tz_utc);
        return ($tz_local->getOffset($dt_local) / 60);
    }

    public static function getTimestamp()
    {
        // gives us a way of getting a consistent time in
        // order to set the created/updated database values
        // to the same value across calls

        global $g_store;
        if (isset($g_store->timestamp))
            return $g_store->timestamp;

        $g_store->timestamp = date("Y-m-d H:i:s", time());
        return $g_store->timestamp;
    }

    public static function isLoggedIn()
    {
        return (\Flexio\System\System::getCurrentUserName() != '') ? true : false;
    }

    public static function getBaseDirectory()
    {
        global $g_store;
        if (isset($g_store->dir_home))
            return $g_store->dir_home;

        return dirname(dirname(dirname(__FILE__)));
    }

    public static function getPublicDirectory()
    {
        return \Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'public';
    }

    public static function getApplicationDirectory()
    {
        return \Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'application';
    }

    public static function getConfigDirectory()
    {
        return \Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'config';
    }

    public static function getResDirectory()
    {
        return \Flexio\System\System::getApplicationDirectory() . DIRECTORY_SEPARATOR . 'res';
    }

    public static function getUpdateDirectory()
    {
        return (\Flexio\System\System::getBaseDirectory() . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'update');
    }

    public static function getUpdateVersionFromFilename($filename)
    {
        $version = 0;
        $matches = array();
        if (preg_match("/update(\d+)/", $filename, $matches))
            $version = intval($matches[1]);

        return $version;
    }

    public static function getLatestVersionNumber()
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

    public static function getGitRevision()
    {
        $path = dirname(dirname(__DIR__)) . '/.git/refs/heads/master';
        $str = @file_get_contents($path);
        if (!$str)
            $str = '';
        return trim($str);
    }

    public static function getCurrentUserFirstName()
    {
        return $GLOBALS['g_store']->user_first_name;
    }

    public static function getCurrentUserLastName()
    {
        return $GLOBALS['g_store']->user_last_name;
    }

    public static function getCurrentUserEmail()
    {
        return $GLOBALS['g_store']->user_email;
    }

    public static function getCurrentUserName()
    {
        return $GLOBALS['g_store']->user_name;
    }

    public static function getCurrentUserEid()
    {
        return $GLOBALS['g_store']->user_eid;
    }

    public static function isNewInstallation()
    {
        $f1 = file_exists(\Flexio\System\System::getConfigDirectory() . DIRECTORY_SEPARATOR . 'config.json');
        return $f1 ? false : true;
    }

    public static function updateConfigSetting($fname, $setting, $value)
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

    public static function getConfig()
    {
        return $GLOBALS['g_config'];
    }

    public static function log($str)
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
            $GLOBALS['query_log_lines'][] = "\r\n--------------------------------------------\r\n".isset_or($_SERVER['REQUEST_URI'],'(no request)')."\r\n\r\n";

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
