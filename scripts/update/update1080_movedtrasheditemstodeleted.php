<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-03-15
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
    // STEP 1: change the status of trashed items over to deleted
    $db->exec("update tbl_user set eid_status = 'D' where eid_status = 'T'");
    $db->exec("update tbl_token set eid_status = 'D' where eid_status = 'T'");
    $db->exec("update tbl_acl set eid_status = 'D' where eid_status = 'T'");
    $db->exec("update tbl_pipe set eid_status = 'D' where eid_status = 'T'");
    $db->exec("update tbl_connection set eid_status = 'D' where eid_status = 'T'");
    $db->exec("update tbl_process set eid_status = 'D' where eid_status = 'T'");
    $db->exec("update tbl_processlog set eid_status = 'D' where eid_status = 'T'");
    $db->exec("update tbl_stream set eid_status = 'D' where eid_status = 'T'");
    $db->exec("update tbl_comment set eid_status = 'D' where eid_status = 'T'");
    $db->exec("update tbl_action set eid_status = 'D' where eid_status = 'T'");
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
