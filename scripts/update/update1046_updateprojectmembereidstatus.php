<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-05-24
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
    // note: this script makes sure that pipe/connection members of projects
    // that have been deleted are also deleted; this is necessary because
    // historically, pipes/connections were only accessed through the project,
    // so if a project was deleted, individual pipes/connections didn't need
    // to have their eid_status set to 'deleted' because there was no way to
    // access them; however, now that pipes/connections are being shown without
    // the project, pipes/connections that were part of projects that were
    // deleted need to have their eid_status to 'deleted' so they don't show
    updateProjectMemberEidStatus($db);
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



function updateProjectMemberEidStatus($db)
{
    // STEP 1: get a list of projects
    $query_sql = "select eid, eid_type, eid_status, updated from tbl_object where eid_type = '" . \Model::TYPE_PROJECT . "'";
    $result = $db->query($query_sql);

    // STEP 2: for each project that's deleted, set the eid_status
    // of project pipe/connection members to deleted
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        $eid_type = $row['eid_type'];
        $eid_status = $row['eid_status'];
        $updated = $row['updated'];

        if ($eid_status !== \Model::STATUS_DELETED)
            continue;

        setProjectMemberStatusToDeleted($db, $eid, $updated);
    }
}

function setProjectMemberStatusToDeleted($db, $project_eid, $project_updated)
{
    // STEP 1: get the pipe/connection members of the project
    $members = \Flexio\System\System::getModel()->assoc_range($project_eid, \Model::EDGE_HAS_MEMBER);

    // STEP 2: set the member status
    foreach ($members as $m)
    {
        $eid = $m['eid'];
        $eid_status = $m['eid_status'];

        // if an item is already deleted, no need to do anything
        if ($eid_status === \Model::STATUS_DELETED)
            continue;

        writeObject($db, $eid, \Model::STATUS_DELETED, $project_updated);
    }
}

function writeObject($db, $eid, $status, $updated)
{
    $qeid = $db->quote($eid);
    $qstatus = $db->quote($status);
    $qupdated = $db->quote($updated);

    $sql = "update tbl_object ".
           "    set ".
           "        eid_status = $qstatus, ".
           "        updated = $qupdated ".
           "    where eid = $qeid;";

    $db->exec($sql);
}
