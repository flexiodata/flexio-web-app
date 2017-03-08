<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-01-10
 *
 * @package flexio
 * @subpackage Install
 */


include_once __DIR__.'/../stub.php';


if ($argc != 8)
{
    echo '{ "success": false, "msg": "Usage: php install-database.php <db_type> <db_host> <db_admin_username> <db_admin_password> <directory_username> <directory_password> <directory_dbname>" }';
    exit(0);
}

$params = array('db_type' => $argv[1],
                'db_host' => $argv[2],
                'db_port' => 5432,
                'db_admin_username' => $argv[3],
                'db_admin_password' => $argv[4],
                'directory_username' => $argv[5],
                'directory_password' => $argv[6],
                'directory_dbname' => $argv[7]);

try
{
    $result = false;
    switch ($params['db_type'])
    {
        default:
            throw new \Exception('Invalid database type');

        case 'postgres':
            $result = createPostgresDatabase($params);
            break;

        case 'mysql':
            $result = createMySqlDatabase($params);
            break;
    }

    if ($result === false)
        throw new \Exception('Unable to create database');
}
catch(\Exception $e)
{
    echo '{ "success": false, "msg":' . json_encode($e->getMessage()) . '}';
    exit(0);
}


echo '{ "success": true, "msg": "Operation completed successfully." }';


function createPostgresDatabase($params)
{
    if (!isset($params['db_type'])) return false;
    if (!isset($params['db_host'])) return false;
    if (!isset($params['db_port'])) return false;
    if (!isset($params['db_admin_username']))  return false;
    if (!isset($params['db_admin_password']))  return false;
    if (!isset($params['directory_username'])) return false;
    if (!isset($params['directory_password'])) return false;
    if (!isset($params['directory_dbname']))   return false;

    // if it's not a new installation, don't create the database
    if (isNewInstallation() === false)
        return false;

    // first, make sure config.json exists and is writable
    $config_fname = System::getConfigDirectory() . DIRECTORY_SEPARATOR . 'config.json';

    if (!file_exists($config_fname))
    {
        @copy(System::getConfigDirectory() . DIRECTORY_SEPARATOR . 'config-defaults.json', $config_fname);
    }

    if (!file_exists($config_fname) || !is_writable($config_fname))
    {
        // configuration directory is not writeable
        return false;
    }

    // connect to the server where we will create the application database
    $connect_params['host'] = $params['db_host'];
    $connect_params['port'] = $params['db_port'];
    $connect_params['dbname'] = 'postgres';
    $connect_params['username'] = $params['db_admin_username'];
    $connect_params['password'] = $params['db_admin_password'];

    // create the application database
    $db = \Flexio\System\ModelDb::factory('PDO_POSTGRES', $connect_params);
    $qdb = $db->quoteIdentifier($params['directory_dbname']);
    $db->exec("create database $qdb");

    // create the application database user
    $username = $params['directory_username'];
    $password = $params['directory_password'];

    $db->exec("create user $username with password '$password'");
    $db->exec("grant all privileges on database $qdb to $username");

    $connect_params['host'] = $params['db_host'];
    $connect_params['port'] = $params['db_port'];
    $connect_params['dbname'] = $params['directory_dbname'];
    $connect_params['username'] = $params['directory_username'];
    $connect_params['password'] = $params['directory_password'];

    // create the application database
    $db = \Flexio\System\ModelDb::factory('PDO_POSTGRES', $connect_params);

    $script_filename = System::getApplicationDirectory() . DIRECTORY_SEPARATOR . 'models'  . DIRECTORY_SEPARATOR . 'postgres'  . DIRECTORY_SEPARATOR . 'createdb.sql';
    $commands = \Flexio\Base\DbUtil::parseSqlScript($script_filename);
    if (!$commands)
        return false;

    foreach ($commands as $command)
        $db->exec($command);

    // write configuration values to config.json
    $result = true;
    if (!System::updateConfigSetting($config_fname, 'directory_database_type', $params['db_type']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_host', $params['db_host']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_port', $params['db_port']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_username', $params['directory_username']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_password', $params['directory_password']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_dbname', $params['directory_dbname']))
        $result = false;

    if ($result === false)
        return false;

    // set the database version number; note: set this manually rather
    // than using System::getModel()->setDbVersionNumber() since this model
    // function depends on the configuration settings, which have just
    // changed and are not yet available to the model
    $latest_version = System::getLatestVersionNumber();
    $qversion = $db->quote($latest_version);
    $sql = "insert into tbl_system (name, value, created, updated) values ('version', $qversion, now(), now()) ";
    $db->exec($sql);

    return true;
}

function createMySqlDatabase($params)
{
    if (!isset($params['db_type']))
    if (!isset($params['db_host'])) return false;
    if (!isset($params['db_port'])) return false;
    if (!isset($params['db_admin_username']))  return false;
    if (!isset($params['db_admin_password']))  return false;
    if (!isset($params['directory_username'])) return false;
    if (!isset($params['directory_password'])) return false;
    if (!isset($params['directory_dbname']))   return false;

    // if it's not a new installation, don't create the database
    if (isNewInstallation() === false)
        return false;

    // first, make sure config.json exists and is writable
    $config_fname = System::getConfigDirectory() . DIRECTORY_SEPARATOR . 'config.json';

    if (!file_exists($config_fname))
    {
        @copy(System::getConfigDirectory() . DIRECTORY_SEPARATOR . 'config-defaults.json', $config_fname);
    }

    if (!file_exists($config_fname) || !is_writable($config_fname))
    {
        // configuration directory is not writeable
        return false;
    }

    // connect to the server where we will create the application database
    $host = $params['db_host'];
    $host .= ':' . $params['db_port'];

    $db = @mysql_connect($host, $params['db_admin_username'], $params['db_admin_password']);
    if ($db === false)
    {
        // could not connect to database
        return false;
    }

    // create the application database
    $result = mysql_query('create database '. $params['directory_dbname'], $db);
    if (!$result)
    {
        // could not create the application database
        mysql_close($db);
        return false;
    }

    // create the application database user
    $username = mysql_real_escape_string($params['directory_username']);
    $password = mysql_real_escape_string($params['directory_password']);

    $result = mysql_query("create user '$username'@'%' identified by '$password'", $db);
    if (!$result)
    {
        // could not create the application database user
        mysql_close($db);
        return false;
    }

    $result = mysql_query("grant all privileges on ". $params['directory_dbname'] . ".* to '$username'@'%'", $db);
    if (!$result)
    {
        mysql_close($db);
        return false;
    }

    $result = mysql_query("create user '$username'@'localhost' identified by '$password'", $db);
    if (!$result)
    {
        // could not create the application database user
        mysql_close($db);
        return false;
    }

    $result = mysql_query("grant all privileges on ". $params['directory_dbname'] . ".* to '$username'@'localhost'", $db);
    if (!$result)
    {
        mysql_close($db);
        return false;
    }

    // switch to the application database and add the database tables
    mysql_select_db($params['directory_dbname'], $db);




    $script_filename = System::getApplicationDirectory() . DIRECTORY_SEPARATOR . 'models'  . DIRECTORY_SEPARATOR . 'postgres'  . DIRECTORY_SEPARATOR . 'createdb.sql';
    $commands = \Flexio\Base\DbUtil::parseSqlScript($script_filename);
    if (!$commands)
        return false;

    foreach ($commands as $command)
        mysql_query($command, $db);


    // set the database version number; note: set this manually rather
    // than using System::getModel()->setDbVersionNumber() since this model
    // function depends on the configuration settings, which have not yet
    // changed and are not yet available to the model
    $latest_version = System::getLatestVersionNumber();
    $qversion = $db->quote($latest_version);
    $command = "insert into tbl_system (name, value, created, updated) values ('version', $qversion, now(), now()) ";
    mysql_query($command, $db);


    if (!$result)
    {
        mysql_close($db);
        return false;
    }

    // pause
    mysql_close($db);
    sleep(1);

    // write configuration values to config.json
    $result = true;
    if (!System::updateConfigSetting($config_fname, 'directory_database_type', $params['db_type']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_host', $params['db_host']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_port', $params['db_port']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_username', $params['directory_username']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_password', $params['directory_password']))
        $result = false;
    if (!System::updateConfigSetting($config_fname, 'directory_database_dbname', $params['directory_dbname']))
        $result = false;

    if ($result === false)
        return false;

    // TODO: update version number

    return true;
}

function checkPostgresDatabase($params)
{
    if (!isset($params['db_host'])) return false;
    if (!isset($params['db_port'])) return false;
    if (!isset($params['db_admin_username'])) return false;
    if (!isset($params['db_admin_password'])) return false;

    // set the database version number
    $connect_params['host'] = $params['db_host'];
    $connect_params['port'] = $params['db_port'];
    $connect_params['dbname'] = 'postgres';
    $connect_params['username'] = $params['db_admin_username'];
    $connect_params['password'] = $params['db_admin_password'];

    try
    {
        $db = \Flexio\System\ModelDb::factory('PDO_POSTGRES', $connect_params);

        $conn = $db->getConnection();

        return isset($conn);
    }
    catch (\Exception $e)
    {
        return false;
    }
}

function checkMySqlDatabase($params)
{
    if (!isset($params['db_host'])) return false;
    if (!isset($params['db_port'])) return false;
    if (!isset($params['db_admin_username'])) return false;
    if (!isset($params['db_admin_password'])) return false;

    if (!function_exists('mysql_connect'))
        return false;

    // connect to the server where we will create the directory database
    $host = $params['db_host'];
    $host .= ':' . $params['db_port'];

    $db = @mysql_connect($host, $params['db_admin_username'], $params['db_admin_password']);
    if ($db === false)
        return false;

    return true;
}

function isNewInstallation()
{
    $f1 = file_exists(System::getConfigDirectory() . DIRECTORY_SEPARATOR . 'config.json');
    return $f1 ? false : true;
}
