<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-26
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
    // STEP 1: convert connection types in the old format to the new format
    updateConnectionTypesInConnectionTable($db);

    // STEP 2: convert the connection types in task metadata
    updateConnectionTypesInPipeTable($db);
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



function updateConnectionTypesInConnectionTable($db)
{
    // STEP 1: get the existing information
    $query_sql = 'select eid, connection_type from tbl_connection';
    $result = $db->query($query_sql);

    // STEP 2: for each item, update the connection type
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $connection_eid = $row['eid'];
        $connection_type_old = $row['connection_type'];
        $connection_type_new = getNewConnectionType($connection_type_old);

        // if we found a connection type to update, write out the updated connection type;
        // otherwise, leave what's there
        if ($connection_type_new !== false)
        {
            $sql = "update tbl_connection ".
                "    set ".
                "        connection_type = '$connection_type_new' ".
                "    where eid = '$connection_eid';";

            $db->exec($sql);
        }
    }
}

function updateConnectionTypesInPipeTable($db)
{
    // STEP 1: get the existing information
    $query_sql = 'select eid, task from tbl_pipe';
    $result = $db->query($query_sql);

    // STEP 2: for each item, update the connection type
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $pipe_eid = $row['eid'];
        $pipe_task = json_decode($row['task'],true);

        foreach ($pipe_task as &$step)
        {
            $metadata = $step['metadata'] ?? false;
            if ($metadata !== false)
            {
                $connection_type_old = $metadata['connection_type'] ?? '';
                $connection_type_new = getNewConnectionType($connection_type_old);

                if ($connection_type_new !== false)
                    $step['metadata']['connection_type'] = $connection_type_new;
            }
        }

        $pipe_task = json_encode($pipe_task);
        $sql = "update tbl_pipe ".
            "    set ".
            "        task = ". $db->quote($pipe_task). "".
            "    where eid = ". $db->quote($pipe_eid). ";";

        $db->exec($sql);
    }
}

function getNewConnectionType($connection_type_old)
{
    switch ($connection_type_old)
    {
        default:
            return false;

        case 'flexio.api':              return 'flexio';
        case 'flexio.local':            return 'flexio.local'; // note: shouldn't exist, but need to distinguish from 'flexio'
        case 'ftp.api':                 return 'ftp';
        case 'sftp.api':                return 'sftp';
        case 'mysql.api':               return 'mysql';
        case 'postgres.api':            return 'postgres';
        case 'dropbox.oauth2':          return 'dropbox';
        case 'googledrive.oauth2':      return 'googledrive';
        case 'googlesheets.oauth2':     return 'googlesheets';
        case 'amazons3.api':            return 'amazons3';
        case 'upload.api':              return 'upload';
        case 'download.api':            return 'download';
        case 'stdin.api':               return 'stdin';
        case 'stdout.api':              return 'stdout';
        case 'email.api':               return 'email';
        case 'http.api':                return 'http';
        case 'rss.api':                 return 'rss';
        case 'socrata.api':             return 'socrata';
        case 'pipelinedeals.api':       return 'pipelinedeals';
        case 'mailjet.api':             return 'mailjet';
        case 'twilio.api':              return 'twilio';
    }
}
