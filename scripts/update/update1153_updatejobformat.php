<?php
/*!
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-10-02
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

    // STEP 2: for each pipe, get the task and if we have a top-level
    // sequence job that wraps an array of tasks in the items node, then
    // extract the task array and if there's only one item in that task
    // array, make that the top-level task, or if there's multiple items
    // in the task array, make the entire array the top-level task (note:
    // this reverses update 1068, but also makes arrays of a single task
    // item into single task object)
/*
    {
        "op": "sequence",
        "params": {
            "items": [{"op":""}] // extract the task from the items node and make it the top-level task
        }
    }
*/

    while ($result && ($row = $result->fetch()))
    {
        $pipe_eid = $row['eid'];
        $pipe_task = json_decode($row['task'], true);

        // if we don't have a sequence task, move on
        if (!is_array($pipe_task))
            continue;
        if (!array_key_exists('op', $pipe_task))
            continue;

        $pipe_task_type = $pipe_task['op'] ?? false;
        if ($pipe_task_type !== 'sequence')
            continue;

        $updated_pipe_task = new \stdClass();
        $pipe_task_items = $pipe_task['items'] ?? [];

        if (count($pipe_task_items) === 0)
            $updated_pipe_task = new \stdClass();
        else if (count($pipe_task_items) === 1)
            $updated_pipe_task = $pipe_task_items[0];
        else
            $updated_pipe_task = $pipe_task_items;

        $updated_pipe_task = json_encode($updated_pipe_task);
        writePipe($db, $pipe_eid, $updated_pipe_task);
    }
}

function updateProcessTable($db)
{
    // STEP 1: get a list of processes
    $query_sql = 'select eid, task from tbl_process';
    $result = $db->query($query_sql);

    // STEP 2: for each process, get the task and if we have a top-level
    // sequence job that wraps an array of tasks in the items node, then
    // extract the task array and if there's only one item in that task
    // array, make that the top-level task, or if there's multiple items
    // in the task array, make the entire array the top-level task (note:
    // this reverses update 1068, but also makes arrays of a single task
    // item into single task object)
/*
    {
        "op": "sequence",
        "params": {
            "items": [{"op":""}] // extract the task from the items node and make it the top-level task
        }
    }
*/

    while ($result && ($row = $result->fetch()))
    {
        $process_eid = $row['eid'];
        $process_task = json_decode($row['task'], true);

        // if we don't have a sequence task, move on
        if (!is_array($process_task))
            continue;

        $process_task_type = $process_task['op'] ?? false;
        if ($process_task_type !== 'sequence')
            continue;

        $updated_process_task = new \stdClass();
        $process_task_items = $process_task['items'] ?? [];

        if (count($process_task_items) === 0)
            $updated_process_task = new \stdClass();
        else if (count($process_task_items) === 1)
            $updated_process_task = $process_task_items[0];
        else
            $updated_process_task = $process_task_items;

        $updated_process_task = json_encode($updated_process_task);
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

