<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-01-16
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

    // STEP 2: for each pipe, get the task, change the task format
    // and save the pipe with the new task
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $pipe_eid = $row['eid'];
        $pipe_task = json_decode($row['task'],true);

        // we should have a valid task that's an array, but make sure;
        // otherwise just leave what's there
        if ($pipe_task === false)
            continue;
        if (!is_array($pipe_task))
            continue;

        // iterate through the pipe tasks array and change the formats;
        // unset any version info
        foreach ($pipe_task as &$task_item)
        {
            if (array_key_exists('version', $task_item))
                unset($task_item['version']);

            $task_item = convertFormat($task_item);
        }

        $updated_pipe_task = json_encode($pipe_task);
        writePipe($db, $pipe_eid, $updated_pipe_task);
    }
}

function updateProcessTable($db)
{
    // STEP 1: get a list of processes
    $query_sql = 'select eid, task from tbl_process';
    $result = $db->query($query_sql);

    // STEP 2: for each process, get the task, change the
    // task format and save the process with the updated info
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $process_eid = $row['eid'];
        $process_task = json_decode($row['task'],true);

        // we should have a valid task that's an array, but make sure;
        // otherwise just leave what's there
        if ($process_task === false)
            continue;
        if (!is_array($process_task))
            continue;

        // if the process task contains a type at the root level, it's a subprocess,
        // so just change the type directly; if the process task is an array, then
        // it's a copy of the pipe process, so loop through each item
        if (array_key_exists('type', $process_task))
        {
            // convert the task format
            $process_task = convertFormat($process_task);
        }
         else
        {
            // iterate through the process tasks array and change the formats
            foreach ($process_task as &$task_item)
            {
                $task_item = convertFormat($task_item);
            }
        }

        $updated_process_task = json_encode($process_task);
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

function convertFormat($task)
{
    // get the task type
    $type = $task['type'] ?? '';

    switch ($task)
    {
        // if we can't find the task, use what's there; this allows the
        // script to be run multiple times against the table without
        // resetting the task if we can't find it or it hasn't already
        // been converted
        default:
            return $task;

        // TODO: implement conversions:
        case 'flexio.calc':
        case 'flexio.convert':
        case 'flexio.convert-pdf':
        case 'flexio.convert-to-json':
        case 'flexio.copy':
        case 'flexio.create':
        case 'flexio.distinct':
        case 'flexio.duplicate':
        case 'flexio.email':
        case 'flexio.execute':
        case 'flexio.replace':
        case 'flexio.grep':
        case 'flexio.group':
        case 'flexio.input':
        case 'flexio.limit':
        case 'flexio.merge':
        case 'flexio.nop':
        case 'flexio.output':
        case 'flexio.prompt':
        case 'flexio.rename':
        case 'flexio.rename-file':
        case 'flexio.search':
        case 'flexio.select':
        case 'flexio.sleep':
        case 'flexio.sort':
        case 'flexio.transform':
        case 'flexio.custom':
            return $task;

        case 'flexio.filter':
            return convertFilterTask($task);
    }
}

function convertFilterTask($task)
{
    return $task;
}

