<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-01-14
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

    // STEP 2: for each pipe, get the task, change the type
    // and save the pipe with the new task
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $pipe_eid = $row['eid'];
        $pipe_task = json_decode($row['task'],true);

        if ($pipe_task === false)
            continue;

        // iterate through the pipe tasks array and change the types;
        // unset any version info
        foreach ($pipe_task as &$task_item)
        {
            if (array_key_exists('version', $task_item))
                unset($task_item['version']);

            $task_item['type'] = convertType($task_item['type']);
        }

        $updated_pipe_task = json_encode($pipe_task);
        writePipe($db, $pipe_eid, $updated_pipe_task);
    }
}

function updateProcessTable($db)
{
    // STEP 1: get a list of processes
    $query_sql = 'select eid, task_type, task from tbl_process';
    $result = $db->query($query_sql);

    // STEP 2: for each process, get the task_type and task, change the
    // type and save the process with the updated info
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $process_eid = $row['eid'];
        $process_task_type = $row['task_type'];
        $process_task = json_decode($row['task'],true);

        if ($process_task === false)
            continue;

        // convert the task_type
        $updated_process_task_type = convertType($process_task_type);


        // if the process task contains a type at the root level, it's a subprocess,
        // so just change the type directly; if the process task is an array, then
        // it's a copy of the pipe process, so loop through each item
        if (array_key_exists('type', $process_task))
        {
            // unset any legacy version info
            if (array_key_exists('version', $process_task))
                unset($process_task['version']);

            // convert the type
            $process_task['type'] = convertType($process_task['type']);
        }
         else
        {
            // iterate through the process tasks array and change the types;
            // unset any legacy version info
            foreach ($process_task as &$task_item)
            {
                if (array_key_exists('version', $task_item))
                    unset($task_item['version']);

                if (array_key_exists('type', $task_item))
                    $task_item['type'] = convertType($task_item['type']);
            }
        }

        $updated_process_task = json_encode($process_task);
        writeProcess($db, $process_eid, $updated_process_task_type, $updated_process_task);
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

function writeProcess($db, $process_eid, $task_type, $task)
{
    $task = $db->quote($task);
    $sql = "update tbl_process ".
           "    set ".
           "        task_type = '$task_type', ".
           "        task_version = 0, ".
           "        task = $task ".
           "    where eid = '$process_eid';";

    $db->exec($sql);
}

function convertType($type)
{
    switch ($type)
    {
        // if we can't find the type, use what's there; this allows the
        // script to be run multiple times against the table without
        // resetting the type if it's already been converted
        default:
            return $type;

        case 'application/vnd.flexio.job':                 return 'flexio';
        case 'application/vnd.flexio.calcfield-job':       return 'flexio.calc';
        case 'application/vnd.flexio.convert-job':         return 'flexio.convert';
        case 'application/vnd.flexio.convert-pdf-job':     return 'flexio.convert-pdf';
        case 'application/vnd.flexio.convert-to-json-job': return 'flexio.convert-to-json';
        case 'application/vnd.flexio.copy-job':            return 'flexio.copy';
        case 'application/vnd.flexio.create-job':          return 'flexio.create';
        case 'application/vnd.flexio.distinct-job':        return 'flexio.distinct';
        case 'application/vnd.flexio.duplicate-job':       return 'flexio.duplicate';
        case 'application/vnd.flexio.email-send-job':      return 'flexio.email';
        case 'application/vnd.flexio.execute-job':         return 'flexio.execute';
        case 'application/vnd.flexio.filter-job':          return 'flexio.filter';
        case 'application/vnd.flexio.find-replace-job':    return 'flexio.replace';
        case 'application/vnd.flexio.grep-job':            return 'flexio.grep';
        case 'application/vnd.flexio.group-job':           return 'flexio.group';
        case 'application/vnd.flexio.input-job':           return 'flexio.input';
        case 'application/vnd.flexio.limit-job':           return 'flexio.limit';
        case 'application/vnd.flexio.merge-job':           return 'flexio.merge';
        case 'application/vnd.flexio.nop-job':             return 'flexio.nop';
        case 'application/vnd.flexio.output-job':          return 'flexio.output';
        case 'application/vnd.flexio.prompt-job':          return 'flexio.prompt';
        case 'application/vnd.flexio.rename-column-job':   return 'flexio.rename';
        case 'application/vnd.flexio.rename-file-job':     return 'flexio.rename-file';
        case 'application/vnd.flexio.search-job':          return 'flexio.search';
        case 'application/vnd.flexio.select-column-job':   return 'flexio.select';
        case 'application/vnd.flexio.sleep-job':           return 'flexio.sleep';
        case 'application/vnd.flexio.sort-job':            return 'flexio.sort';
        case 'application/vnd.flexio.transform-job':       return 'flexio.transform';
        case 'application/vnd.flexio.custom-job':          return 'flexio.custom';
    }
}

