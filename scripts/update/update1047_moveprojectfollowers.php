<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-05-27
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
    // note: this script transfers followers from projects to the pipes
    // and connections that they used to be members of (and still are
    // associated with in the association table)
    transferProjectFollowers($db);
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


function transferProjectFollowers($db)
{
    // STEP 1: get a list of projects
    $query_sql = "select eid, eid_type, eid_status, updated from tbl_object where eid_type = '" . \Model::TYPE_PROJECT . "'";
    $result = $db->query($query_sql);

    // STEP 2: for each project, transfer the followers of that
    // project to the individual pipes/connections in the project
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        addProjectFollowersToMembers($db, $eid);
        removeProjectFollowers($db, $eid);
    }
}

function addProjectFollowersToMembers($db, $eid, $updated)
{
}

function removeProjectFollowers($db, $eid, $updated)
{
}

