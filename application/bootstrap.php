<?php
/*!
 *
 * Copyright (c) 2008-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   David Z. Williams; Benjamin I. Williams
 * Created:  2008-09-05
 *
 */

require __DIR__ . '/../vendor/autoload.php';

$g_start_time = microtime(true);


$configjson = @file_get_contents(__DIR__ . '/../config/config.json');
if ($configjson !== false)
{
    $g_config = json_decode($configjson);
    unset($configjson);
    if (is_null($g_config))
    {
        die("Invalid configuration file format");
    }
}
 else
{
    die("Missing configuration file");
}


// g_store stores global variables, such as database pointers
$g_store = new stdClass();
$g_store->user_eid = '';
$g_store->dir_home = ($g_config->dir_home ?? dirname(__DIR__));
$g_store->http_host = null;    // see GET_HTTP_HOST() below
$g_store->timestamp = null;
$g_store->model = null;
$g_store->connections = [];
$g_store->connection_enckey = '9i$8iw]aKmZzq12r8';


//require_once __DIR__ . '/profiler.php'; // uncomment this line to enabled profiling

function GET_HTTP_HOST()
{
    if (!is_null($GLOBALS['g_store']->http_host))
        return $GLOBALS['g_store']->http_host;
    return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
}

function IS_DEBUG()
{
    return $GLOBALS['g_config']->debug;
}

function IS_TESTING()
{
    return isset($GLOBALS['g_config']->tests_allowed) ? $GLOBALS['g_config']->tests_allowed : false;
}

function IS_PROCESSTRYCATCH()
{
    if (!IS_DEBUG())
        return true; // release-mode always trys/catches
    return ($GLOBALS['g_config']->process_trycatch ?? true);
}

function IS_LOCALHOST()
{
    return (GET_HTTP_HOST() == 'localhost');
}

function IS_TESTSITE()
{
    return ($_SERVER['SERVER_NAME'] == "test.flex.io");
}

function IS_PRODSITE()
{
    return ($_SERVER['SERVER_NAME'] == "www.flex.io");
}

function IS_CLI()   // determines if this is a command line environment
{
    return isset($_GLOBALS['argv']);
}

function IS_UNDER_MAINTENANCE()
{
    return $GLOBALS['g_config']->maintenance;
}

function IS_API_ONLY()
{
    return (isset($GLOBALS['g_config']->api_only) ? $GLOBALS['g_config']->api_only : false);
}

function IS_SECURE()
{
    // this checks if the client is connecting securely; note that
    // $_SERVER['HTTPS'] will only be on if the user is connecting directly
    // to the web server, and not going through a reverse proxy.  If traffic
    // is going through a reverse proxy such as a EC2 load balancer, we must check
    // HTTP_X_FORWAREDED_PROTO instead

    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        return true;
    return (isset($_SERVER['HTTPS']) && strlen($_SERVER['HTTPS']) > 0);
}

function getPhpExecTime()
{
    return microtime(true) - $GLOBALS['g_start_time'];
}

function toBoolean($v)
{
    if (is_string($v))
        return ($v == 'true' || $v == '1' || $v == 'on');
         else
        return (bool)$v;
}

function isset_and_true($v)
{
    return (isset($v) && ($v === true));
}

function array_key_exists_or($k, $arr, $def = '')
{
    return array_key_exists($k, $arr) ? $arr[$k] : $def;
}

function fxdebug($val)
{
    if (is_array($val) || is_object($val))
        $val = var_export($val, true);
    if (IS_DEBUG() && file_exists('/tmp/flex-debug.txt'))
        file_put_contents('/tmp/flex-debug.txt', "$val\n", FILE_APPEND);
}

function fxStartSession()
{
    if (!isset($GLOBALS['fx_session_started']))
    {
        $GLOBALS['fx_session_started'] = true;
        session_start();
    }
}

if (!function_exists('getallheaders'))
{
    // implementation for nginx

    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
        return $headers;
    }
}

spl_autoload_register(function ($class) {
    if (strpos($class, 'Flexio\\') === 0)
    {
        $parts = explode('\\',$class);
        for ($i = 0; $i < count($parts)-1; ++$i)
            $parts[$i] = strtolower($parts[$i]);
        unset($parts[0]);
        $class = __DIR__ . '/' . implode('/',$parts) . '.php';
        if (file_exists($class))
        {
            require_once $class;
            return true;
        }
        return false;
    }
});


// php debug settings for debug mode
if (IS_DEBUG())
{
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    ini_set('log_errors_max_len', 8192);
}

// set session garbage collection lifetime; set this value relatively
// high because we'll handle idle timeout manually
ini_set('session.gc_maxlifetime', (86400 * 30) /* 30 days */);

ini_set('session.cookie_lifetime', (86400 * 30) /* 30 days */);

ini_set('session.cookie_secure',1);
ini_set('session.cookie_httponly',1);
ini_set('session.use_only_cookies',1);
ini_set('session.name', 'FXSESSID');

// turn on error reporting
error_reporting(E_ALL | E_STRICT);

// start out using UTC -- if the user has a preferred timezone, this will be set in System
date_default_timezone_set('UTC');


if (!isset($g_config->directory_database_type) || strlen($g_config->directory_database_type) == 0)
{
    die("Please set directory_database_type in your config.json; for example 'mysql'");
}


function homeProxy()
{
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
        $fhost = $_SERVER['HTTP_X_FORWARDED_HOST'];
    else if (isset($_SERVER['HTTP_HOST']))
        $fhost = $_SERVER['HTTP_HOST'];
    else
        die();
    $fproto = IS_SECURE() ? 'https' : 'http';

    global $g_config;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $g_config->homepage_proxy);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Forwarded-Host: $fhost", "X-Forwarded-Proto: $fproto"));
    echo curl_exec($ch);
    exit(0);
}


// if this is a web call, check to make sure the connection is secure
if (isset($_SERVER['REQUEST_URI']))
{
    // if we're not in a secure connection, redirect
    if (!IS_SECURE())
    {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit(0);
    }

    // some configurations will proxy through a (marketing) website homepage
    // if the user is not logged in
    if ($_SERVER['REQUEST_URI'] == '/' && strlen($g_config->homepage_proxy ?? '') > 0 && (!isset($_COOKIE['FXSESSID'])))
    {
        homeProxy();
    }
}




require_once __DIR__ . '/system/Framework.php';
require_once __DIR__ . '/system/Plugins.php';
require_once __DIR__ . '/system/System.php';
require_once __DIR__ . '/models/Model.php';

//require_once __DIR__ . '/autoload_logger.php'; // uncomment this line for autoload logging



$g_session_handler = 'files';
if (isset($g_config->session_handler))
    $g_session_handler = $g_config->session_handler;

if ($g_session_handler == 'database')
{
    $session_handler = new \Flexio\System\DatabaseSessionHandler();
    session_set_save_handler($session_handler, true);

}
 else if ($g_session_handler == 'memcache')
{
    $session_memcached_host = $g_config->session_memcached_host ?? '';
    $session_memcached_port = $g_config->session_memcached_port ?? '';

    if (strlen($session_memcached_host) == 0) die('session_memcached_host not set');
    if (strlen($session_memcached_port) == 0) die('session_memcached_port not set');

    ini_set('session.save_handler', 'memcache');
    ini_set('session.save_path', "tcp://$session_memcached_host:$session_memcached_port");
}
 else if ($g_session_handler == 'memcached')
{
    $session_memcached_host = $g_config->session_memcached_host ?? '';
    $session_memcached_port = $g_config->session_memcached_port ?? '';

    if (strlen($session_memcached_host) == 0) die('session_memcached_host not set');
    if (strlen($session_memcached_port) == 0) die('session_memcached_port not set');

    ini_set('session.save_handler', 'memcached');
    ini_set('session.save_path', "$session_memcached_host:$session_memcached_port");
}
 else if ($g_session_handler == 'redis')
{
    $session_redis_host = $g_config->session_redis_host ?? '';
    $session_redis_port = $g_config->session_redis_port ?? '';

    if (strlen($session_redis_host) == 0) die('session_redis_host not set');
    if (strlen($session_redis_port) == 0) die('session_redis_port not set');

    ini_set('session.save_handler', 'redis');
    ini_set('session.save_path', "tcp://$session_redis_host:$session_redis_port");
}
 else if ($g_session_handler == 'files')
{
    // php defaults to session files on the web server
}
 else
{
    die('invalid session handler');
}





class Flexio
{
    protected static $_instance = null;

    protected function _initialize()
    {
    }

    public function run()
    {
        $uri = $_SERVER['REQUEST_URI'];

        if (IS_SECURE())
        {
            // only start a session if we have a session cookie
            if (isset($_COOKIE['FXSESSID']))
            {
                fxStartSession();
            }
        }

        if ($uri == '/' && strlen($GLOBALS['g_config']->homepage_proxy ?? '')>0 && session_status() == PHP_SESSION_ACTIVE)
        {
            $user_eid = $_SESSION['env']['user_eid'] ?? '';
            if (strlen($user_eid) == 0)
            {
                @session_write_close();
                homeProxy();
            }
        }

        // check for calls that need to write to the session, like login, logout, etc

        if (false === strstr($uri, '/signin') &&
            false === strstr($uri, '/logout') &&
            false === strstr($uri, '/login') &&
            false === strstr($uri, '/user'))
        {
            // TODO: make sure user info is being written on
            // post; doesn't need to happen here, as long it's
            // happening on the post; previously we had user/edit
            // here
            @session_write_close();
        }

        if ((false !== strstr($uri, '/login') || false !== strstr($uri, '/signin')) && !IS_SECURE())
        {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit();
        }

        $framework = \Flexio\System\Framework::getInstance();
        $framework->setControllerPrefix("\\Flexio\\Controllers\\");
        $framework->setControllerSuffix('Controller');
        $framework->registerPlugin(new \Flexio\System\FlexioPlugin);
        $framework->dispatch();
    }

    public static function getInstance()
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self();
            self::$_instance->_initialize();
        }

        return self::$_instance;
    }
}
