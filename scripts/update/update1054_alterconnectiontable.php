<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-06-02
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


    // STEP 4: drop the existing connection_info columns
/*
    $sql = <<<EOT
        ALTER TABLE tbl_connection
            DROP COLUMN host,
            DROP COLUMN port,
            DROP COLUMN username,
            DROP COLUMN password,
            DROP COLUMN token,
            DROP COLUMN refresh_token,
            DROP COLUMN database;
EOT;
$db->exec($sql);
*/
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
        // unencrypt the parameters
        $connection_info = array();
        $connection_info['host'] = $row['host'];
        $connection_info['port'] = $row['port'];
        $connection_info['username'] = \Flexio\Base\Util::decrypt($row['username'], $GLOBALS['g_store']->connection_enckey);
        $connection_info['password'] = \Flexio\Base\Util::decrypt($row['password'], $GLOBALS['g_store']->connection_enckey);
        $connection_info['access_token'] = \Flexio\Base\Util::decrypt($row['token'], $GLOBALS['g_store']->connection_enckey);
        $connection_info['refresh_token'] = \Flexio\Base\Util::decrypt($row['refresh_token'], $GLOBALS['g_store']->connection_enckey);
        $connection_info['database'] = $row['database'];

        // stringify and encrypt the connection info
        $connection_str = json_encode($connection_info);
        $connection_str_encrypted = \Flexio\Base\Util::encrypt($connection_str, $GLOBALS['g_store']->connection_enckey);

        // set the connection_info
        $update = array();
        $update['connection_info'] = $connection_str_encrypted;
        $update['created'] = $row['created'];
        $update['updated'] = $row['updated'];
        $db->update('tbl_connection', $update, 'eid = ' . $db->quote($eid));
    }
}
