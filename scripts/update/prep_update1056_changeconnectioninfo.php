<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-10-05
 *
 * @package flexio
 * @subpackage Database_Update
 */


include_once __DIR__.'/../stub.php';


if ($argc != 5)
{
    echo '{ "success": false, "msg": "Usage: php update*.php <host> <username> <password> <database_name>" }';
    exit(0);
}


$params = array('host' => $argv[1],
                'port' => 5432,
                'username' => $argv[2],
                'password' => $argv[3],
                'dbname' => $argv[4]);

try
{
    $db = \Flexio\Base\Db::factory('PDO_POSTGRES', $params);
    $conn = $db->getConnection();
}
catch (\Exception $e)
{
    echo($e->getMessage());
    $db = null;
}

if (is_null($db))
{
    echo '{ "success": false, "msg": "Could not connect to database." }';
    exit(0);
}


try
{
    // STEP 1: change the format of some of the connection info
    changeConnectionInfo($db);
}
catch(\Exception $e)
{
    echo '{ "success": false, "msg":' . json_encode($e->getMessage()) . '}';
    exit(0);
}


// update the version number
$current_version = \Flexio\System\System::getUpdateVersionFromFilename(__FILE__);
\Flexio\System\System::getModel()->setDbVersionNumber($current_version);

echo '{ "success": true, "msg": "Operation completed successfully." }';


function copyConnectionInfo($db)
{
    // STEP 1: get a list of the existing connection info
    $query_sql = "select
                      eid,
                      connection_type,
                      connection_info
                  from tbl_connection
                ";
    $result = $db->query($query_sql);

    // STEP 2: for each of the connection info items, save the connection
    // info in the connection_info column
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        $connection_type = $row['connection_type'];
        $connection_str_encrypted = $row['connection_info'];

        // unencrypt the connection info
        $connection_str = \Flexio\Base\Util::decrypt($connection_str_encrypted, $GLOBALS['g_store']->connection_enckey);
        $connection_info_original = json_decode($connection_str, true);
        if (!is_array($connection_info_original))
            continue;

        // get the relevant connection parameters to save
        $connection_info_updated = array();
        $is_info_updated = getUpdatedConnectionInfo($connection_type, $connection_info_original, $connection_info_updated);

        // if the info isn't updated, move on
        if ($is_info_updated === false)
            continue;

        // encrypt and return the formatted connection info
        $connection_str_updated = json_encode($connection_info_updated);
        $connection_str_updated_encrypted = \Flexio\Base\Util::encrypt($connection_str_updated, $GLOBALS['g_store']->connection_enckey);

        // set the connection_info
        $update = array();
        $update['connection_info'] = $connection_str_updated_encrypted;
        $db->update('tbl_connection', $update, 'eid = ' . $db->quote($eid));
    }
}


function getUpdatedConnectionInfo(string $connection_type, array $connection_info, array &$connection_info_updated) : bool
{
    // reformat the connection info; returns true if the info is updated; false otherwise
    switch ($connection_type)
    {
        // default: nothing updated
        default:
            return false;
/*
// TODO: no updates for now

        case \Model::CONNECTION_TYPE_FTP:
        case \Model::CONNECTION_TYPE_SFTP:
        case \Model::CONNECTION_TYPE_SOCRATA:
           $connection_info_updated = array(
                'host' => $connection_info['host'] ?? '',
                'port' => $connection_info['port'] ?? 0,
                'username' => $connection_info['username'] ?? '',
                'password' => $connection_info['password'] ?? ''
            );
            return true;

        case \Model::CONNECTION_TYPE_MYSQL:
        case \Model::CONNECTION_TYPE_POSTGRES:
        case \Model::CONNECTION_TYPE_ELASTICSEARCH:
            $connection_info_updated = array(
                'host' => $connection_info['host'] ?? '',
                'port' => $connection_info['port'] ?? 0,
                'username' => $connection_info['username'] ?? '',
                'password' => $connection_info['password'] ?? '',
                'database' => $connection_info['database'] ?? ''
            );
            return true;

        case \Model::CONNECTION_TYPE_DROPBOX:
        case \Model::CONNECTION_TYPE_BOX:
        case \Model::CONNECTION_TYPE_GOOGLEDRIVE:
        case \Model::CONNECTION_TYPE_GOOGLESHEETS:
            $connection_info_updated = array(
                'access_token' => $connection_info['access_token'] ?? '',
                'refresh_token' => $connection_info['refresh_token'] ?? ''
            );
            return true;

        case \Model::CONNECTION_TYPE_AMAZONS3:
            $connection_info_updated = array(
                'host' => $connection_info['host'] ?? '',
                'username' => $connection_info['username'] ?? '',
                'password' => $connection_info['password'] ?? '',
                'database' => $connection_info['database'] ?? ''
            );
            return true;

        case \Model::CONNECTION_TYPE_PIPELINEDEALS:
            $connection_info_updated = array(
                'access_token' => $connection_info['access_token'] ?? ''
            );
            return true;

        case \Model::CONNECTION_TYPE_MAILJET:
            $connection_info_updated = array(
                'username' => $connection_info['username'] ?? '',
                'password' => $connection_info['password'] ?? ''
            );
            return true;

        case \Model::CONNECTION_TYPE_TWILIO:
            $connection_info_updated = array(
                'username' => $connection_info['username'] ?? '',
                'access_token' => $connection_info['access_token'] ?? ''
            );
            return true;
*/
    }
}

