<?php
/*!
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-12-04
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


    // STEP 3: get a list of all resources and their locations
    $sql = 'select eid, location from tbl_resource';
    $result = $db_application->query($sql);


    // STEP 4: iterate through each of the resources and populate the
    // mime_type column based on whether the content is stored as a stream or
    // a table; for streams, use text/csv (at this point the streams that
    // have been created should be this type); for tables, use
    // application/vnd.flexio.table
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        $storage_type = getContentStorage($db_application, $db_datastore, $eid);
        if ($storage_type === false)
            continue;

        $mime_type = false;

        if ($storage_type === 'stream')
            $mime_type = 'text/csv';
        if ($storage_type === 'table')
            $mime_type = 'application/vnd.flexio.table';

        if ($mime_type === false) // unknown storage type
            continue;

        writeMimeType($db_application, $eid, $mime_type);
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



function getContentStorage($db_application, $db_datastore, $resource_eid)
{
    // STEP 1: find the object content location from the resource
    $info = lookupServerInfoByEid($db_application, $resource_eid);
    if (!isset($info['location']))
        return false;

    $location = $info['location'];

    // STEP 2: find the object content storage type
    $info = $db_datastore->getFileInfo($location);
    if (!$info)
        return false;
    if (!isset($info['type']))
        return false;

    return $info['type'];
}

function writeMimeType($db_application, $resource_eid, $mime_type)
{
    $sql = "update tbl_resource ".
           "    set mime_type = '$mime_type' ".
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
           "    tco.host as host, ".
           "    tco.port as port, ".
           "    tco.database as database, ".
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
