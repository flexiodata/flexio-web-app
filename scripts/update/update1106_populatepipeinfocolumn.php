<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-10-17
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
    // TODO: populate process pipe_info column with pipe info for processes
    // that were run on a pipe after the last time the pipe was updated
    // (i.e., for the pipes that we know the state of at the time the process ran)


    // STEP 1: get the processes that were created from a pipe or or after
    // the last update of the pipe

    $sql = <<<EOT
        select
            prc.eid as eid,
            prc.parent_eid as parent_eid
        from tbl_process prc
        inner join tbl_pipe pip on prc.parent_eid = pip.eid
        where prc.created >= pip.updated
EOT;
    $result = $db->query($sql);

    // STEP 2: populate the pipe info from the pipe
    while ($result && ($row = $result->fetch()))
    {
        $process_eid = $row['eid'];
        $pipe_eid = $row['parent_eid'];
        populatePipeInfo($db, $process_eid, $pipe_eid);
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


function populatePipeInfo($db, $process_eid, $pipe_eid)
{
    if (\Flexio\Base\Eid::isValid($process_eid) === false)
        return;
    if (\Flexio\Base\Eid::isValid($pipe_eid) === false)
        return;

    try
    {
        $process = \Flexio\Object\Process::load($process_eid);
        $pipe = \Flexio\Object\Pipe::load($pipe_eid);

        $properties = array();
        $properties['pipe_info'] = $pipe->get();
        $process->set($properties);
    }
    catch (\Flexio\Base\Exception $e)
    {
    }
}
