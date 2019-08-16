<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-08-15
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
/*
    $curSql = "DECLARE cursor1 CURSOR FOR SELECT * FROM big_table";
    $con = new PDO("pgsql:host=whatever dbname=whatever", "user", "pass");
    $con->beginTransaction(); // cursors require a transaction.
    $stmt = $con->prepare($curSql);
    $stmt->execute();

    $innerStatement = $con->prepare("FETCH 1 FROM cursor1");

    while($innerStatement->execute() && $row = $innerStatement->fetch(PDO::FETCH_ASSOC)) {
        echo $row['field'];
    }
*/

    // STEP 1: create a cursor to iterate over the pipes; use a cursor
    // to avoid an out-of-memory error when the table is large
    $cursor_sql = 'declare cursor_pipe cursor for select eid, task from tbl_pipe';
    $db->beginTransaction();
    $stmt = $db->prepare($cursor_sql);
    $stmt->execute();
    $iter_cursor = $db->prepare("fetch 1 from cursor_pipe");

    // STEP 2: for each pipe, get the task, remove the eid
    // and save the pipe with the new task
    $objects = array();
    while ($iter_cursor->execute() && ($row = $iter_cursor->fetch()))
    {
        $pipe_eid = $row['eid'];
        $pipe_task = json_decode($row['task'],true);

        if ($pipe_task === false)
            continue;

        $pipe_task = convertTask($pipe_task);

        $updated_pipe_task = json_encode($pipe_task);
        writePipe($db, $pipe_eid, $updated_pipe_task);
    }

    $db->commit();
}

function updateProcessTable($db)
{
    // STEP 1: create a cursor to iterate over the processes; use a cursor
    // to avoid an out-of-memory error when the table is large
    $cursor_sql = 'declare cursor_process cursor for select eid, pipe_info, task from tbl_process';
    $db->beginTransaction();
    $stmt = $db->prepare($cursor_sql);
    $stmt->execute();
    $iter_cursor = $db->prepare("fetch 1 from cursor_process");

    // STEP 2: for each process, get the pipe info and the task,
    // remove the eid, and save the process with the new pipe info
    // and new task
    $objects = array();
    while ($iter_cursor->execute() && ($row = $iter_cursor->fetch()))
    {
        $process_eid = $row['eid'];

        // get the pipe info saved in the process
        $process_pipe_info = json_decode($row['pipe_info'], true);
        $process_pipe_info_task = $process_pipe_info['task'] ?? false;

        // get the process task
        $process_task = json_decode($row['task'], true);

        // convert the pipe info task
        if ($process_pipe_info_task !== false)
        {
            $process_pipe_info_task = convertTask($process_pipe_info_task);
            $process_pipe_info['task'] = $process_pipe_info_task;
        }

        // convert the process task
        if ($process_task !== false)
            $process_task = convertTask($process_task);

        $updated_process_pipe_info = json_encode($process_pipe_info);
        $updated_process_task = json_encode($process_task);
        writeProcess($db, $process_eid, $updated_process_pipe_info, $updated_process_task);
    }

    $db->commit();
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

function writeProcess($db, $process_eid, $pipe_info, $task)
{
    $pipe_info = $db->quote($pipe_info);
    $task = $db->quote($task);
    $sql = "update tbl_process ".
           "    set ".
           "        pipe_info = $pipe_info,".
           "        task = $task ".
           "    where eid = '$process_eid';";

    $db->exec($sql);
}

function convertTask($task)
{
    if (!is_array($task))
        return $task;

    // remove the eid
    unset($task['eid']);

    // perform standard transformations (at this time) to make
    // sure tasks are properly represented when converted to json
    $task = \Flexio\Jobs\Base::fixEmptyParams($task);
    $task = \Flexio\Jobs\Base::flattenParams($task);

    foreach ($task as $key => &$value)
    {
        $value = convertTask($value);
    }

    return $task;
}

