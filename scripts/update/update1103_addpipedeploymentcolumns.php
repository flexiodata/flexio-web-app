<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-10-03
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
    // STEP 1: rename various deploy columns
    $db->exec("alter table tbl_pipe rename column pipe_mode to deploy_mode;");
    $db->exec("alter table tbl_pipe rename column schedule_status to deploy_schedule;");

    // STEP 2: add new deploy columns
    $sql = <<<EOT
        alter table tbl_pipe
            add column deploy_api varchar(1) NOT NULL default 'I',
            add column deploy_ui varchar(1) NOT NULL default 'I';
EOT;
    $db->exec($sql);

    // STEP 3: rename/add appropriate indexes
    $sql = <<<EOT
        alter index idx_pipe_schedule_status rename to idx_pipe_deploy_schedule;
        create index idx_pipe_deploy_api on tbl_pipe (deploy_api);
        create index idx_pipe_deploy_ui on tbl_pipe (deploy_ui);
        create index idx_pipe_deploy_mode on tbl_pipe (deploy_mode);
EOT;
    $db->exec($sql);


    // STEP 3: populate the deploy_mode with 'B' (for build mode) if
    // not already populated
    $sql = <<<EOT
        update tbl_pipe set deploy_mode='B' where deploy_mode = '';
EOT;
    $db->exec($sql);
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
