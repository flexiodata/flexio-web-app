<?php
/*!
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-20
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
    // STEP 1: get the application database
    $db_application = $db;


    // STEP 2: connect to the datastore database
    $host = $g_config->datastore_host;
    $port = $g_config->datastore_port;
    $database = $g_config->datastore_dbname;
    $username = $g_config->datastore_username;
    $password = $g_config->datastore_password;

    $dbparams = array();
    $dbparams['host'] = $host;
    $dbparams['port'] = $port;
    $dbparams['database'] = $database;
    $dbparams['username'] = $username;
    $dbparams['password'] = $password;

    $db_datastore = Postgres::create($dbparams);
    if (!$db_datastore)
        throw new \Exception('Could not connect to datastore');


    // STEP 3: get a list of all resources; because the structure field
    // is new, we don't have to worry about pre-existing values
    $sql = 'select eid from tbl_resource';
    $result = $db_application->query($sql);


    // STEP 4: iterate through each of the resources and populate the
    // structure column from the content database structure
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        $structure = getStructure($db_application, $db_datastore, $eid);
        if ($structure === false)
            continue;

        $structure_write_value = array();
        $structure_write_value['structure'] = $structure;

        $structure_write_value = json_encode($structure_write_value);
        writeStructure($db_application, $eid, $structure_write_value);
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



function getStructure($db_application, $db_datastore, $resource_eid)
{
    // STEP 1: find the object content location from the resource
    $info = lookupServerInfoByEid($db_application, $resource_eid);
    if (!isset($info['location']))
        return false;

    $location = $info['location'];

    // STEP 2: if the resource is hooked up to something besides a
    // table, don't return a structure (e.g. return false for streams
    // or for tables that don't exist)
    $info = $db_datastore->getFileInfo($location);
    if (!$info)
        return false;
    if (!isset($info['type']) || $info['type'] != 'table')
        return false;

    // STEP 3: get the structure from the datastore
    $structure = $db_datastore->describeTable($location);
    if (!$structure)
        return false;

    // STEP 5: add empty expression element to each field
    foreach ($structure as &$col)
    {
        $col['display_name'] = $col['name'];
        $col['tags'] = '';
        $col['description'] = '';

        if (isset($wkstruct[$col['name']]['display_name']))
            $col['display_name'] = $wkstruct[$col['name']]['display_name'];
        if (isset($wkstruct[$col['name']]['description']))
            $col['description'] = $wkstruct[$col['name']]['description'];
        if (isset($wkstruct[$col['name']]['tags']))
            $col['tags'] = $wkstruct[$col['name']]['tags'];

        $col['expression'] = '';
    }

    return $structure;
}

function writeStructure($db_application, $resource_eid, $structure)
{
    $sql = "update tbl_resource ".
           "    set structure = '$structure' ".
           "    where eid = '$resource_eid';";

    $db_application->exec($sql);
}

function lookupServerInfoByEid($db_application, $eid)
{
    // adapted from implementation in ResourceModel

    if (!\Flexio\Base\Eid::isValid($eid))
        return false; // don't flag an error, but acknowledge that object doesn't exist

    $qeid = $db_application->quote($eid);

    $sql = "select ".
           "    tco.eid as eid, ".
           "    tco.server as server, ".
           "    tco.server_port as server_port, ".
           "    tco.location_group as location_group, ".
           "    tre.location as location ".
           "from ".
           "    tbl_connection tco ".
           "inner join ".
           "    tbl_resource tre ".
           "on ".
           "    tco.eid = tre.connection_eid ".
           "where ".
           "    tre.eid = $qeid";

    $res = $db_application->fetchRow($sql);

    if (!$res)
        return false;

    return $res;
}
