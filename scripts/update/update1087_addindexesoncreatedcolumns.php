<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-03-19
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
    // STEP 1: add indexes on created columns
    $db->exec('create index idx_user_created on tbl_user (created)');
    $db->exec('create index idx_token_created on tbl_token (created)');
    $db->exec('create index idx_acl_created on tbl_acl (created)');
    $db->exec('create index idx_pipe_created on tbl_pipe (created)');
    $db->exec('create index idx_connection_created on tbl_connection (created)');
    $db->exec('create index idx_process_created on tbl_process (created)');
    $db->exec('create index idx_processlog_created on tbl_processlog (created)');
    $db->exec('create index idx_stream_created on tbl_stream (created)');
    $db->exec('create index idx_comment_created on tbl_comment (created)');
    $db->exec('create index idx_action_created on tbl_action (created)');
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
