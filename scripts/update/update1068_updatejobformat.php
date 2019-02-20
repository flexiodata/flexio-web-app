<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-06
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
    // STEP 1: update the pipe table
    updatePipeTable($db);

    // STEP 2: update the process table
    updateProcessTable($db);
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



function updatePipeTable($db)
{
    // STEP 1: get a list of pipes
    $query_sql = 'select eid, task from tbl_pipe';
    $result = $db->query($query_sql);

    // STEP 2: for each pipe, get the task and add a top-level
    // sequence job that wraps the old task array
/*
    {
        "op": "sequence",
        "params": {
            "items": [] // old task array goes in the items array
        }
    }
*/
    $sequence_task = array(
        'op' => 'sequence',
        'params' => array(
            'items' => array()
        )
    );

    while ($result && ($row = $result->fetch()))
    {
        $pipe_eid = $row['eid'];
        $pipe_task = json_decode($row['task']);

        // if we don't have a task array, we have a top-level task, so move on
        if (!is_array($pipe_task))
            continue;
        if (array_key_exists('op', $pipe_task))
            continue;

        $sequence_task['params']['items'] = $pipe_task;
        $updated_pipe_task = json_encode($sequence_task);
        writePipe($db, $pipe_eid, $updated_pipe_task);
    }
}

function updateProcessTable($db)
{
    // STEP 1: get a list of processes
    $query_sql = 'select eid, task from tbl_process';
    $result = $db->query($query_sql);

    // STEP 2: for each process, get the task and add a top-level
    // sequence job that wraps the old task array
    /*
        {
            "op": "sequence",
            "params": {
                "items": [] // old task array goes in the items array
            }
        }
    */
    $sequence_task = array(
        'op' => 'sequence',
        'params' => array(
            'items' => array()
        )
    );

    while ($result && ($row = $result->fetch()))
    {
        $process_eid = $row['eid'];
        $process_task = json_decode($row['task']);

        // if we don't have a task array, we have a top-level task, so move on
        if (!is_array($process_task))
            continue;
        if (array_key_exists('op', $process_task))
            continue;

        $sequence_task['params']['items'] = $process_task;
        $updated_process_task = json_encode($sequence_task);
        writeProcess($db, $process_eid, $updated_process_task);
    }
}

function writePipe($db, $pipe_eid, $task)
{
    $task = $db->quote($task);
    $sql = "update tbl_pipe ".
           "    set ".
           "        task = $task ".
           "    where eid = '$pipe_eid';";

    $db->exec($sql);
}

function writeProcess($db, $process_eid, $task)
{
    $task = $db->quote($task);
    $sql = "update tbl_process ".
           "    set ".
           "        task = $task ".
           "    where eid = '$process_eid';";

    $db->exec($sql);
}

