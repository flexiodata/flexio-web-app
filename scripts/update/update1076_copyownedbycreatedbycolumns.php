<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-03-09
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
    // const EDGE_CREATED       = 'CRT';  // user A created object B
    // const EDGE_CREATED_BY    = 'CRB';  // object A was created by user B
    // const EDGE_OWNS          = 'OWN';  // user A owns object B
    // const EDGE_OWNED_BY      = 'OWB';  // object A is owned by user B

    // STEP 1: add the owned_by and created_by columns to the various tables
    $db->exec("alter table tbl_user add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");
    $db->exec("alter table tbl_token add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");
    $db->exec("alter table tbl_acl add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");
    $db->exec("alter table tbl_pipe add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");
    $db->exec("alter table tbl_connection add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");
    $db->exec("alter table tbl_process add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");
    $db->exec("alter table tbl_processlog add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");
    $db->exec("alter table tbl_stream add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");
    $db->exec("alter table tbl_comment add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");
    $db->exec("alter table tbl_action add column owned_by varchar(12) NOT NULL default '', add column created_by varchar(12) NOT NULL default ''");

    // STEP 2: copy the owned_by information from the association table to the various tables
    $db->exec("update tbl_user as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");
    $db->exec("update tbl_token as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");
    $db->exec("update tbl_acl as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");
    $db->exec("update tbl_pipe as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");
    $db->exec("update tbl_connection as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");
    $db->exec("update tbl_process as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");
    $db->exec("update tbl_processlog as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");
    $db->exec("update tbl_stream as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");
    $db->exec("update tbl_comment as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");
    $db->exec("update tbl_action as tar set owned_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'OWB'");

    // STEP 3: copy the created_by information from the association table to the various tables
    $db->exec("update tbl_user as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");
    $db->exec("update tbl_token as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");
    $db->exec("update tbl_acl as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");
    $db->exec("update tbl_pipe as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");
    $db->exec("update tbl_connection as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");
    $db->exec("update tbl_process as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");
    $db->exec("update tbl_processlog as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");
    $db->exec("update tbl_stream as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");
    $db->exec("update tbl_comment as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");
    $db->exec("update tbl_action as tar set created_by = src.target_eid from tbl_association as src where tar.eid = src.source_eid and src.association_type = 'CRB'");

    // STEP 4: add the indexes to the various tables
    $db->exec("create index idx_user_owned_by ON tbl_user (owned_by)");
    $db->exec("create index idx_token_owned_by ON tbl_token (owned_by)");
    $db->exec("create index idx_acl_owned_by ON tbl_acl (owned_by)");
    $db->exec("create index idx_pipe_owned_by ON tbl_pipe (owned_by)");
    $db->exec("create index idx_connection_owned_by ON tbl_connection (owned_by)");
    $db->exec("create index idx_process_owned_by ON tbl_process (owned_by)");
    $db->exec("create index idx_processlog_owned_by ON tbl_processlog (owned_by)");
    $db->exec("create index idx_stream_owned_by ON tbl_stream (owned_by)");
    $db->exec("create index idx_comment_owned_by ON tbl_comment (owned_by)");
    $db->exec("create index idx_action_owned_by ON tbl_action (owned_by)");
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
