<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-02-10
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
    $db = ModelDb::factory('PDO_POSTGRES', $params);
    $conn = $db->getConnection();
}
catch (Exception $e)
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
    // STEP 1: update the structure of tbl_connection so some columns
    // can handle large text values
    $sql = <<<EOT
        alter table tbl_connection
            alter column host type text,
            alter column username type text,
            alter column password type text,
            alter column database type text;
EOT;
    $db->exec($sql);

    // STEP 2: convert encrypted credentials in the old format to the new format
    updateConnectionTableCredentialInfo($db);
}
catch(Exception $e)
{
    echo '{ "success": false, "msg":' . json_encode($e->getMessage()) . '}';
    exit(0);
}


// update the version number
$current_version = System::getUpdateVersionFromFilename(__FILE__);
System::getModel()->setDbVersionNumber($current_version);

echo '{ "success": true, "msg": "Operation completed successfully." }';



function updateConnectionTableCredentialInfo($db)
{
    // STEP 1: get the existing information
    $query_sql = 'select eid, username, password, token, refresh_token from tbl_connection';
    $result = $db->query($query_sql);

    // STEP 2: for each item, encrypt the credentials in the new format
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $connection_eid = $row['eid'];

        // decrypt the credentials
        $username = Util::decrypt($row['username'], $GLOBALS['g_store']->connection_enckey);
        $password = Util::decrypt($row['password'], $GLOBALS['g_store']->connection_enckey);
        $token = Util::decrypt($row['token'], $GLOBALS['g_store']->connection_enckey);
        $refresh_token = Util::decrypt($row['refresh_token'], $GLOBALS['g_store']->connection_enckey);

        // encrypt the credentials
        $username_encrypted = Util::encrypt($username, $GLOBALS['g_store']->connection_enckey);
        $password_encrypted = Util::encrypt($password, $GLOBALS['g_store']->connection_enckey);
        $token_encrypted = Util::encrypt($token, $GLOBALS['g_store']->connection_enckey);
        $refresh_token_encrypted = Util::encrypt($refresh_token, $GLOBALS['g_store']->connection_enckey);

        // write out the encrypted credentials
        writeConnectionInfo($db, $connection_eid, $username_encrypted, $password_encrypted, $token_encrypted, $refresh_token_encrypted);
    }
}

function writeConnectionInfo($db, $connection_eid, $username, $password, $token, $refresh_token)
{
    $sql = "update tbl_connection ".
           "    set ".
           "        username = '$username', ".
           "        password = '$password', ".
           "        token = '$token', ".
           "        refresh_token  = '$refresh_token' ".
           "    where eid = '$connection_eid';";

    $db->exec($sql);
}
