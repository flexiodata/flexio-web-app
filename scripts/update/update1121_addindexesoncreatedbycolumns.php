<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-08-06
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
    $db->exec('create index idx_user_created_by ON tbl_user (created_by);');
    $db->exec('create index idx_teammember_created_by ON tbl_teammember (created_by);');
    $db->exec('create index idx_token_created_by ON tbl_token (created_by);');
    $db->exec('create index idx_pipe_created_by ON tbl_pipe (created_by);');
    $db->exec('create index idx_connection_created_by ON tbl_connection (created_by);');
    $db->exec('create index idx_process_created_by ON tbl_process (created_by);');
    $db->exec('create index idx_processlog_created_by ON tbl_processlog (created_by);');
    $db->exec('create index idx_stream_created_by ON tbl_stream (created_by);');
    $db->exec('create index idx_comment_created_by ON tbl_comment (created_by);');
    $db->exec('create index idx_action_created_by ON tbl_action (created_by);');
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
