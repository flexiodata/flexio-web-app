<?php
/*!
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-10-30
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
    // STEP 1: add the scehdule field to tbl_pipe
    $sql = <<<EOT
        alter table tbl_pipe
            add schedule text default '',
            add schedule_status varchar(1) NOT NULL default 'I';
EOT;
    $db->exec($sql);


    // STEP 2: create the index on the schedule status
    $sql = <<<EOT
        create index idx_pipe_schedule_status on tbl_pipe (schedule_status);
EOT;
    $db->exec($sql);


    // STEP 3: drop the schedule columns on the workspace table; not currently
    // used, so no need to migrate values
    $sql = <<<EOT
    alter table tbl_workspace
        drop column schedule,
        drop column schedule_status;
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
