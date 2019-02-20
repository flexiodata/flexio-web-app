<?php
/*!
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-21
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
    // STEP 1: add the column
    $sql = <<<EOT
        alter table tbl_project
              add column connection_eid varchar(12) NOT NULL default '';
EOT;
    $db->exec($sql);


    // STEP 2: add the index
    $sql = <<<EOT
        CREATE INDEX idx_project_connection_eid ON tbl_project (connection_eid);
EOT;
    $db->exec($sql);


    // STEP 3: get a list of all projects
    $sql = 'select eid from tbl_project';
    $result = $db->query($sql);

    // STEP 4: iterate through each of the projects and populate the
    // connection_eid from the existing default connection setting
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        setProjectConnectionEid($db, $eid);
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



function setProjectConnectionEid($db, $project_eid)
{
    // get the owner associations without regard to deletion (hence the custom
    // query as opposed to the model association call)
    $qproject_eid = $db->quote($project_eid);
    $sql = "select target_eid as eid ".
           "    from tbl_association tas ".
           "    inner join tbl_object tobsrc on tas.source_eid = tobsrc.eid ".
           "    inner join tbl_object tobtar on tas.target_eid = tobtar.eid ".
           "    where tas.source_eid = $qproject_eid ".
           "        and tas.association_type = '".Model::EDGE_OWNED_BY."'".
           "    order by tobtar.id ";
    $result = $db->query($sql);

    // should only have one owner; use the first one
    $row = $result->fetch();
    if ($row === false)
        return false;

    $owner_eid = $row['eid'];
    $connection_eid = getExistingDefaultConnectionEid($owner_eid);

    if (!\Flexio\Base\Eid::isValid($connection_eid))
        return false;

    $qconnection_eid = $db->quote($connection_eid);
    $sql = "update tbl_project set connection_eid = $qconnection_eid where eid = $qproject_eid";
    $result = $db->exec($sql);
    return true;
}

function getExistingDefaultConnectionEid($user_eid)
{
    $registry_model = \Flexio\System\System::getModel()->registry;
    $connection_model = \Flexio\System\System::getModel()->connection;

    $default_server_registry_tag = 'server.default.connection';
    $connection_eid = $registry_model->getString($user_eid, $default_server_registry_tag);
    return $connection_eid;
}
