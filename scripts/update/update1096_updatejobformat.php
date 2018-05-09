<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-05-07
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
    // DEBUG: set to false to keep old 'params'
    $remove_old_params_key = true;

    // STEP 1: update the pipe table
    updatePipeTable($db, $remove_old_params_key);

    // STEP 2: update the process table
    updateProcessTable($db, $remove_old_params_key);

    // STEP 3: update the process log table
    updateProcessLogTable($db, $remove_old_params_key);
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



function updatePipeTable($db, $remove_old_params_key)
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

        $pipe_task = convertTask($pipe_task, $remove_old_params_key);

        $updated_pipe_task = json_encode($pipe_task);
        writePipe($db, $pipe_eid, $updated_pipe_task);
    }
}

function updateProcessTable($db, $remove_old_params_key)
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

        $process_task = convertTask($process_task, $remove_old_params_key);

        $updated_process_task = json_encode($process_task);
        writeProcess($db, $process_eid, $updated_process_task);
    }
}

function updateProcessLogTable($db, $remove_old_params_key)
{
    // STEP 1: get a list of process log entries
    $query_sql = 'select eid, task from tbl_processlog';
    $result = $db->query($query_sql);

    // STEP 2: for each process log entry, get the task, and save
    // the process with the new task
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $process_eid = $row['eid'];
        $process_task = json_decode($row['task'],true);

        if ($process_task === false)
            continue;

        $process_task = convertTask($process_task, $remove_old_params_key);

        $updated_process_task = json_encode($process_task);
        writeProcessLog($db, $process_eid, $updated_process_task);
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

function writeProcessLog($db, $processlog_eid, $task)
{
    $task = $db->quote($task);

    $sql = "update tbl_processlog ".
           "    set ".
           "        task = $task ".
           "    where eid = '$processlog_eid';";

    $db->exec($sql);
}

function convertTask($task, $remove_old_params_key)
{
    // TODO: recurse the pipe tasks and move the params key/values
    // to the top-level of their respective objects
    if (isset($task['params']))
    {
        if (is_array($task['params']))
        {
            foreach ($task['params'] as $key => $value)
            {
                $task[$key] = $value;
            }
        }

        if ($remove_old_params_key === true)
            unset($task['params']);
    }

    if (is_array($task))
    {
        foreach ($task as $key => &$value)
        {
            if (!is_array($value))
                continue;

            $value = convertTask($value, $remove_old_params_key);
        }
    }

    return $task;
}

