<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
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
        $project_eid = $row['eid'];
        transferProjectFollowersToMembers($db, $project_eid);
    }
}

function transferProjectFollowersToMembers($db, $project_eid)
{
    // get the project follower association info and members
    $project_follower_assoc_info = getAssociationInfo($db, $project_eid, \Model::EDGE_FOLLOWED_BY);
    $project_members = \Flexio\System\System::getModel()->assoc_range($project_eid, \Model::EDGE_HAS_MEMBER);

    // apply the appropriate project follower association info to the members
    foreach ($project_follower_assoc_info as $f)
    {
        $follower_eid = $f['target_eid'];

        foreach ($project_members as $m)
        {
            $member_eid = $m['eid'];

            // create the appropriate associations between the project follower and
            // the project member
            $new_member_assoc_info = array();
            $new_member_assoc_info['source_eid'] = $member_eid;
            $new_member_assoc_info['target_eid'] = $follower_eid;
            $new_member_assoc_info['association_type'] = \Model::EDGE_FOLLOWED_BY;
            $new_member_assoc_info['created'] = $f['created'];
            $new_member_assoc_info['updated'] = $f['updated'];
            insertAssociationInfo($db, $new_member_assoc_info);

            $new_member_assoc_info = array();
            $new_member_assoc_info['source_eid'] = $follower_eid;
            $new_member_assoc_info['target_eid'] = $member_eid;
            $new_member_assoc_info['association_type'] = \Model::EDGE_FOLLOWING;
            $new_member_assoc_info['created'] = $f['created'];
            $new_member_assoc_info['updated'] = $f['updated'];
            insertAssociationInfo($db, $new_member_assoc_info);
        }

        // delete the project followers
        \Flexio\System\System::getModel()->assoc_delete($project_eid, \Model::EDGE_FOLLOWED_BY, $follower_eid);
        \Flexio\System\System::getModel()->assoc_delete($follower_eid, \Model::EDGE_FOLLOWING, $project_eid);
    }
}

function getAssociationInfo($db, $source_eid, $type)
{
    $qsource_eid = $db->quote($source_eid);
    $qtype = $db->quote($type);

    $sql = "select ".
            "        tas.source_eid as source_eid, ".
            "        tas.target_eid as target_eid, ".
            "        tas.association_type as association_type, ".
            "        tas.created as created, ".
            "        tas.updated as updated ".
            "    from tbl_association tas ".
            "    inner join tbl_object tobsrc on tas.source_eid = tobsrc.eid ".
            "    inner join tbl_object tobtar on tas.target_eid = tobtar.eid ".
            "    where tas.source_eid = $qsource_eid ".
            "        and tas.association_type = $qtype ".
            "    order by tobtar.id ";
    $result = $db->query($sql);

    $info = array();
    while ($result && ($row = $result->fetch()))
    {
        $info[] = $row;
    }

    return $info;
}

function insertAssociationInfo($db, $info)
{
    $db->insert('tbl_association', $info, true /*ignore duplicate key*/);
}
