<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-12-19
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
    // DEBUG: set to false to keep old 'type'
    $remove_old_type_param = true;

    // STEP 1: update the pipe table
    updatePipeTable($db, $remove_old_type_param);

    // STEP 2: update the process table
    updateProcessTable($db, $remove_old_type_param);

    // STEP 3: update the process log table
    updateProcessLogTable($db, $remove_old_type_param);
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



function updatePipeTable($db, $remove_old_type_param)
{
    // STEP 1: get a list of pipes
    $query_sql = 'select eid, task from tbl_pipe';
    $result = $db->query($query_sql);

    // STEP 2: for each pipe, get the task, change the type
    // and save the pipe with the new task
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $pipe_eid = $row['eid'];
        $pipe_task = json_decode($row['task'],true);

        if ($pipe_task === false)
            continue;

        // iterate through the pipe tasks array and change the type key
        // to "op" and the type value to the new job name; unset the old
        // "type" key
        foreach ($pipe_task as &$task_item)
        {
            $task_type = $task_item['type'] ?? false;
            if ($task_type === false)
                continue;

            $task_item['op'] = convertType($task_type);

            if ($remove_old_type_param === true)
                unset($task_item['type']);
        }

        $updated_pipe_task = json_encode($pipe_task);
        writePipe($db, $pipe_eid, $updated_pipe_task);
    }
}

function updateProcessTable($db, $remove_old_type_param)
{
    // STEP 1: get a list of processes
    $query_sql = 'select eid, task from tbl_process';
    $result = $db->query($query_sql);

    // STEP 2: for each process, get the task, change the type
    // and save the process with the new task
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $process_eid = $row['eid'];
        $process_task = json_decode($row['task'],true);

        if ($process_task === false)
            continue;

        // iterate through the process tasks array and change the type key
        // to "op" and the type value to the new job name; unset the old
        // "type" key
        foreach ($process_task as &$task_item)
        {
            $task_type = $task_item['type'] ?? false;
            if ($task_type === false)
                continue;

            $task_item['op'] = convertType($task_type);

            if ($remove_old_type_param === true)
                unset($task_item['type']);
        }

        $updated_process_task = json_encode($process_task);
        writeProcess($db, $process_eid, $updated_process_task);
    }
}

function updateProcessLogTable($db, $remove_old_type_param)
{
    // STEP 1: get a list of process log entries
    $query_sql = 'select eid, task_type, task from tbl_processlog';
    $result = $db->query($query_sql);

    // STEP 2: for each process log entry, get the task_type and task,
    // change the type and save the process with the new task
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $process_eid = $row['eid'];
        $process_task_type = $row['task_type'];
        $process_task = json_decode($row['task'],true);

        if ($process_task === false)
            continue;

        // change the type key to "op" and the type value to the new job
        // name; unset the old "type" key
        $task_type = $process_task['type'] ?? false;
        if ($task_type !== false)
        {
            $process_task['op'] = convertType($task_type);

            if ($remove_old_type_param === true)
                unset($process_task['type']);
        }

        $updated_process_task_type = convertType($process_task_type);
        $updated_process_task = json_encode($process_task);
        writeProcessLog($db, $process_eid, $updated_process_task_type, $updated_process_task);
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

function writeProcessLog($db, $processlog_eid, $task_type, $task)
{
    $task_type = $db->quote($task_type);
    $task = $db->quote($task);

    $sql = "update tbl_processlog ".
           "    set ".
           "        task_type = $task_type, ".
           "        task = $task ".
           "    where eid = '$processlog_eid';";

    $db->exec($sql);
}

function convertType($type)
{
    // if the job type starts with a "flexio.", then strip it off;
    // otherwise just return the type

    // make sure the type is clean
    $type = trim(strtolower($type));

    // strip off any leading "flexio."
    if (substr($type,0,7) === "flexio.")
        $type = substr($type,7);

    return $type;
}
