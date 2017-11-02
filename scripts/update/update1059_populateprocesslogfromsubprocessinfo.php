<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-10-31
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
    // STEP 1: copy log subprocess info from process table to process log table;
    // these correspond to the information in the records where the process_eid is
    // not equal to the eid (which is the main process eid)
    $sql = <<<EOT
        insert into tbl_processlog
            (eid, process_eid, task_type, task_version, task,
             input, output, started, finished, log_type, created, updated)
        select
            eid as eid,
            process_eid as process_eid,
            task_type as task_type,
            task_version as task_version,
            task as task,
            input as input,
            output as output,
            started as started,
            finished as finished,
            'P' as log_type,
            created as created,
            updated as updated
        from
            tbl_process
        where
            process_eid != eid;
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
