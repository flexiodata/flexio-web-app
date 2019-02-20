<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-10-04
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
    // STEP 1: rename the token_expires column
    $sql = <<<EOT
        ALTER TABLE tbl_connection RENAME COLUMN token_expires TO expires;
EOT;
$db->exec($sql);


    // STEP 2: add the new connection info column
    $sql = <<<EOT
        ALTER TABLE tbl_connection ADD connection_info TEXT DEFAULT '';
EOT;
$db->exec($sql);


    // STEP 3: copy the existing connection info to the new connection_info column
    copyConnectionInfo($db);
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
                      host,
                      port,
                      username,
                      password,
                      token,
                      refresh_token,
                      database
                  from tbl_connection
                ";
    $result = $db->query($query_sql);

    // STEP 2: for each of the connection info items, save the connection
    // info in the connection_info column
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];

        // get the relevant connection parameters to save
        $connection_info = getConnectionInfo($row);

        // stringify and encrypt the connection info
        $connection_str = json_encode($connection_info);
        $connection_str_encrypted = \Flexio\Base\Util::encrypt($connection_str, $GLOBALS['g_store']->connection_enckey);

        // set the connection_info
        $update = array();
        $update['connection_info'] = $connection_str_encrypted;
        $db->update('tbl_connection', $update, 'eid = ' . $db->quote($eid));
    }
}


function getConnectionInfo($row) : array
{
    // unencrypt the parameters
    $connection_type = $row['connection_type'];
    $host = $row['host'];
    $port = $row['port'];
    $database = $row['database'];
    $username = \Flexio\Base\Util::decrypt($row['username'], $GLOBALS['g_store']->connection_enckey);
    $password = \Flexio\Base\Util::decrypt($row['password'], $GLOBALS['g_store']->connection_enckey);
    $access_token = \Flexio\Base\Util::decrypt($row['token'], $GLOBALS['g_store']->connection_enckey);
    $refresh_token = \Flexio\Base\Util::decrypt($row['refresh_token'], $GLOBALS['g_store']->connection_enckey);

    switch ($connection_type)
    {
        default:
            return array();

        case \Model::CONNECTION_TYPE_FTP:
        case \Model::CONNECTION_TYPE_SFTP:
        case \Model::CONNECTION_TYPE_SOCRATA:
           return array(
                'host' => $host ?? '',
                'port' => $port ?? '',
                'username' => $username ?? '',
                'password' => $password ?? ''
            );

        case \Model::CONNECTION_TYPE_MYSQL:
        case \Model::CONNECTION_TYPE_POSTGRES:
        case \Model::CONNECTION_TYPE_ELASTICSEARCH:
            return array(
                'host' => $host ?? '',
                'port' => $port ?? '',
                'username' => $username ?? '',
                'password' => $password ?? '',
                'database' => $database ?? ''
            );

        case \Model::CONNECTION_TYPE_DROPBOX:
        case \Model::CONNECTION_TYPE_BOX:
        case \Model::CONNECTION_TYPE_GOOGLEDRIVE:
        case \Model::CONNECTION_TYPE_GOOGLESHEETS:
            return array(
                'access_token' => $access_token ?? '',
                'refresh_token' => $refresh_token ?? '',
            );

        case \Model::CONNECTION_TYPE_AMAZONS3:
            return array(
                'host' => $host ?? '',
                'username' => $username ?? '',
                'password' => $password ?? '',
                'database' => $database ?? ''
            );

        case \Model::CONNECTION_TYPE_PIPELINEDEALS:
            return array(
                'access_token' => $access_token ?? ''
            );

        case \Model::CONNECTION_TYPE_MAILJET:
            return array(
                'username' => $username ?? '',
                'password' => $password ?? ''
            );

        case \Model::CONNECTION_TYPE_TWILIO:
            return array(
                'username' => $username ?? '',
                'access_token' => $access_token ?? ''
            );
    }
}

