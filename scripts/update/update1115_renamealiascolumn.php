<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-07-12
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
    // STEP 1: drop the existing name columns; info now stored in short_description
    $db->exec("alter table tbl_pipe drop column name;");
    $db->exec("alter table tbl_connection drop column name;");

    // STEP 2: drop the existing alias indexes
    $db->exec("drop index if exists idx_pipe_alias;");
    $db->exec("drop index if exists idx_connection_alias;");

    // STEP 3: rename the alias column to name
    $db->exec("alter table tbl_pipe rename column alias to name;");
    $db->exec("alter table tbl_connection rename column alias to name;");

    // STEP 4: create indexes on the name columns
    $db->exec("create index idx_pipe_name ON tbl_pipe (name);");
    $db->exec("create index idx_connection_name ON tbl_connection (name);");
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

