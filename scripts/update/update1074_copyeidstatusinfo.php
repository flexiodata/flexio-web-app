<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-03-08
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
    // STEP 1: add the eid_status to the various model tables
    $db->exec("alter table tbl_user add column eid_status varchar(1) NOT NULL default ''");
    $db->exec("alter table tbl_token add column eid_status varchar(1) NOT NULL default ''");
    $db->exec("alter table tbl_acl add column eid_status varchar(1) NOT NULL default ''");
    $db->exec("alter table tbl_pipe add column eid_status varchar(1) NOT NULL default ''");
    $db->exec("alter table tbl_connection add column eid_status varchar(1) NOT NULL default ''");
    $db->exec("alter table tbl_process add column eid_status varchar(1) NOT NULL default ''");
    $db->exec("alter table tbl_processlog add column eid_status varchar(1) NOT NULL default ''");
    $db->exec("alter table tbl_stream add column eid_status varchar(1) NOT NULL default ''");
    $db->exec("alter table tbl_comment add column eid_status varchar(1) NOT NULL default ''");
    $db->exec("alter table tbl_action add column eid_status varchar(1) NOT NULL default ''");

    // STEP 2: copy the status info from the tbl_object table to the various other tables
    $db->exec("update tbl_user as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_token as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_acl as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_pipe as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_connection as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_process as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_processlog as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_stream as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_comment as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
    $db->exec("update tbl_action as tar set eid_status = src.eid_status from tbl_object as src where tar.eid = src.eid");
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
