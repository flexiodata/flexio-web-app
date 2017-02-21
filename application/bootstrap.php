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



if (file_exists(__DIR__ . '/../config/config.json'))
{
    $configjson = file_get_contents(__DIR__ . '/../config/config.json');
    $g_config = json_decode($configjson);
    unset($configjson);
}
 else
{
    if (file_exists(__DIR__ . '/../config/config-defaults.json'))
    {
        $configjson = file_get_contents(__DIR__ . '/../config/config-defaults.json');
        $g_config = json_decode($configjson);
        unset($configjson);
    }
}

if (is_null($g_config)) die("Invalid configuration file format");

// g_store stores global variables, such as database pointers
$g_store = new stdClass();
$g_store->user_name = '';
$g_store->user_email = '';
$g_store->user_first_name = '';
$g_store->user_last_name = '';
$g_store->user_eid = '';
$g_store->dir_home = dirname(dirname(__FILE__));
$g_store->http_host = null;    // see GET_HTTP_HOST() below
$g_store->lang = '';
$g_store->thousands_separator = ',';
$g_store->decimal_separator = '.';
$g_store->date_format = 'm/d/Y';
$g_store->timezone = 'UTC';
$g_store->timestamp = null;
$g_store->model = null;
$g_store->datastores = [];
$g_store->connections = [];
$g_store->connection_enckey = '9i$8iw]aKmZzq12r8';

if (isset($g_config->dir_home))
    $g_store->dir_home = $g_config->dir_home;


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

function IS_LOCALHOST()
{
    return (GET_HTTP_HOST() == 'localhost');
}

function IS_TESTSITE()
{
    return ($_SERVER['SERVER_NAME'] == "test.flex.io");
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

function isset_or(&$v, $def = false)
{
    return isset($v) ? $v : $def;
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

$g_autoloader_ignore_errors = false;
spl_autoload_register(function ($class_name) {
    $class_name = str_replace(['.','/'], '', $class_name);
    $class_name = str_replace(['_',"\\"], '/', $class_name) . '.php';
    if (strpos($class_name, '/') !== false)
    {
        $parts = explode('/',$class_name);
        if ($parts[0] == 'Flexio')
        {
            for ($i = 0; $i < count($parts)-1; ++$i)
                $parts[$i] = lcfirst($parts[$i]);
            unset($parts[0]);
            $class_name = __DIR__ . '/' . implode('/',$parts);
        }
        else if ($parts[0] == 'ParagonIE')
        {
            // sodium_compat
            return false;
        }
    }
    if ($GLOBALS['g_autoloader_ignore_errors'])
    {
        if (false === (@include_once $class_name))
            return false;
    }
     else
    {
        require_once $class_name;
        return true;
    }
});

function setAutoloaderIgnoreErrors($value)
{
    $GLOBALS['g_autoloader_ignore_errors'] = $value;
}


// php debug settings for debug mode
if (IS_DEBUG())
{
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    ini_set('log_errors_max_len', 8192);
    $GLOBALS['g_start_time'] = microtime(true);
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


function setupIncludeDirectories()
{
    global $g_config, $g_store;
    $base = $g_store->dir_home . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR;
    set_include_path($base . 'jobs'        . PATH_SEPARATOR
                   . $base . 'services'    . PATH_SEPARATOR
                   . $base . 'system'      . PATH_SEPARATOR
                   . $base . 'tests'       . PATH_SEPARATOR
                   . get_include_path());
}
setupIncludeDirectories();



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
    if ($_SERVER['REQUEST_URI'] == '/' && strlen(isset_or($g_config->homepage_proxy,'')) > 0 && (!isset($_COOKIE['FXSESSID'])))
    {
        homeProxy();
    }
}




require_once __DIR__ . '/system/Framework.php';
require_once __DIR__ . '/system/Plugins.php';
require_once __DIR__ . '/system/System.php';
require_once __DIR__ . '/models/Model.php';

//require_once __DIR__ . '/autoload_logger.php'; // uncomment this line for autoload logging




class DatabaseSessionHandler implements SessionHandlerInterface
{
    private $registry_model;
    private $session_data = '';

    function __construct()
    {
    }

    function open($path, $name)
    {
        $this->registry_model = System::getModel()->registry;
        return true;
    }

    function close()
    {
        $this->registry_model = null;
        $this->session_data = '';
        return true;
    }

    function read($session_id)
    {
        $this->session_data = $this->registry_model->getString('', "session;$session_id", '');
        //file_put_contents("c:\\fxsite\\ben.txt", "GET ($session_id): ".$this->session_data . "\n", FILE_APPEND);
        return $this->session_data;
    }

    function write($session_id, $data)
    {
        if ($this->session_data !== $data)
        {
            // if data changed, write it
            $this->session_data = $this->registry_model->setString('', "session;$session_id", $data, 86400);
            $this->session_data = $data;
            //file_put_contents("c:\\fxsite\\ben.txt", "WRITE ($session_id): ".$data . "\n", FILE_APPEND);
        }
    }

    function destroy($session_id)
    {
        $this->registry_model->deleteEntryByName('', "session;$session_id");
        $this->session_data = '';
        //file_put_contents("c:\\fxsite\\ben.txt", "DELETE ($session_id)\n", FILE_APPEND);
    }

    function gc($age)
    {
        $this->registry_model->cleanupExpiredEntries();
    }
}



$g_session_handler = 'files';
if (isset($g_config->session_handler))
    $g_session_handler = $g_config->session_handler;

if ($g_session_handler == 'database')
{
    $session_handler = new DatabaseSessionHandler();
    session_set_save_handler($session_handler, true);

}
 else if ($g_session_handler == 'memcache')
{
    $session_memcached_host = isset_or($g_config->session_memcached_host, '');
    $session_memcached_port = isset_or($g_config->session_memcached_port, '');

    if (strlen($session_memcached_host) == 0) die('session_memcached_host not set');
    if (strlen($session_memcached_port) == 0) die('session_memcached_port not set');

    ini_set('session.save_handler', 'memcache');
    ini_set('session.save_path', "tcp://$session_memcached_host:$session_memcached_port");
}
 else if ($g_session_handler == 'memcached')
{
    $session_memcached_host = isset_or($g_config->session_memcached_host, '');
    $session_memcached_port = isset_or($g_config->session_memcached_port, '');

    if (strlen($session_memcached_host) == 0) die('session_memcached_host not set');
    if (strlen($session_memcached_port) == 0) die('session_memcached_port not set');

    ini_set('session.save_handler', 'memcached');
    ini_set('session.save_path', "$session_memcached_host:$session_memcached_port");
}
 else if ($g_session_handler == 'redis')
{
    $session_redis_host = isset_or($g_config->session_redis_host, '');
    $session_redis_port = isset_or($g_config->session_redis_port, '');

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

        if ($uri == '/' && strlen(isset_or($GLOBALS['g_config']->homepage_proxy,''))>0 && session_status() == PHP_SESSION_ACTIVE)
        {
            $user_eid = isset_or($_SESSION['env']['user_eid'], '');
            if (strlen($user_eid) == 0)
            {
                @session_write_close();
                homeProxy();
            }
        }


/*
        // check idle
        if (!Flexio::checkIdle())
        {
            System::clearLoginIdentity();
            @session_destroy();

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            {
                // TODO: send error response code through api:
                // (Api::ERROR_INSUFFICIENT_RIGHTS, _('Session expired'));
            }
             else
            {
                header('Location: /app/signin');
            }

            exit(0);
        }
*/

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

        $framework = Framework::getInstance();
        $framework->setControllerPrefix("\\Flexio\\Controllers\\");
        $framework->setControllerSuffix('Controller');
        $framework->registerPlugin(new FlexioPlugin);
        $framework->dispatch();
    }
/*
    public static function checkIdle()
    {
        // checkIdle() returns true if idle is ok

        // check if we aren't logged in at all; if not, return ok
        if (!isset($_SESSION['env']['user_name']))
            return true;
        if (strlen($_SESSION['env']['user_name']) == 0)
            return true;

        // check if idle time checking is disabled for this session
        if (isset($_SESSION['no_idle_timeout']) && toBoolean($_SESSION['no_idle_timeout']))
            return true;

        // round the time to the nearest 1000 seconds so that we don't update session data so much
        $curtime = round(time(), -3);

        // if last activity session value doesn't exist, set it and return ok
        if (!isset($_SESSION['last_activity']))
        {
            $_SESSION['last_activity'] = $curtime;
            return true;
        }

        $last_activity = $_SESSION['last_activity'];
        $idletime = $curtime - $last_activity;

        // default session timeout of 2 hours
        $session_timeout = 7200;
        if (isset($GLOBALS['g_config']->session_timeout))
            $session_timeout = $GLOBALS['g_config']->session_timeout;

        if ($idletime > $session_timeout)
            return false;

        // update last activity time stamp
        if ($last_activity != $curtime)
        {
            $_SESSION['last_activity'] = $curtime;
        }

        return true;
    }*/

    public static function getInstance()
    {
        if (null === Flexio::$_instance)
        {
            Flexio::$_instance = new self();
            Flexio::$_instance->_initialize();
        }

        return Flexio::$_instance;
    }
}
