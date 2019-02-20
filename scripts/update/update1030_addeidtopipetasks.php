<?php
/*!
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-09-27
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
    // STEP 1: get a list of pipes
    $query_sql = 'select eid, task from tbl_pipe';
    $result = $db->query($query_sql);

    // STEP 2: for each pipe, get the task, add the eids
    // and save the pipe with the new task
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $pipe_eid = $row['eid'];
        $pipe_task = json_decode($row['task'],true);

        if ($pipe_task === false)
            continue;

        // create a task from the task array item; this will add in
        // an eid
        $task_object = \Flexio\Object\Task::create($pipe_task);
        $updated_pipe_task = $task_object->get();

        // remove any legacy cid code that's set
        foreach ($updated_pipe_task as &$t)
        {
            unset($t['cid']);
        }

        $updated_pipe_task = json_encode($updated_pipe_task);
        writeTask($db, $pipe_eid, $updated_pipe_task);
    }
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


function writeTask($db, $pipe_eid, $task)
{
    $task = $db->quote($task);
    $sql = "update tbl_pipe ".
           "    set ".
           "        task = $task ".
           "    where eid = '$pipe_eid';";

    $db->exec($sql);
}
