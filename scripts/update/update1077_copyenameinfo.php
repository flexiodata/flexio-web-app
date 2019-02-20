<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-03-12
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
    // STEP 1: add the ename to the various model tables
    $db->exec("alter table tbl_pipe add column ename text default ''");
    $db->exec("alter table tbl_connection add column ename text default ''");

    // STEP 2: copy the ename info from the tbl_object table to the various other tables
    $db->exec("update tbl_pipe as tar set ename = src.ename from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_connection as tar set ename = src.ename from tbl_object as src where tar.eid = src.eid");

    // STEP 3: create the indexes on the ename fields
    $db->exec("create index idx_pipe_ename ON tbl_pipe (ename)");
    $db->exec("create index idx_connection_ename ON tbl_connection (ename)");
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
