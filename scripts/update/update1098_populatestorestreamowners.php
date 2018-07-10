<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-07-10
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
    // STEP 1: get the root store directories
    // public const TYPE_DIRECTORY = 'SD';
    // public const TYPE_FILE = 'SF';
    $query = $db->query("select eid, owned_by from tbl_stream where stream_type = 'SD' and name = ''");

    // STEP 2: populate the owner info from the parent
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        $owned_by = $row['owned_by'];
        populateChildStreamOwner($db, $eid, $owned_by);
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


function populateChildStreamOwner($db, $parent_eid, $owned_by)
{
    if (\Flexio\Base\Eid::isValid($parent_eid) === false)
        return;
    if (\Flexio\Base\Eid::isValid($owned_by) === false)
        return;

    // populate the owner info from the given parent
    $qparent_eid = $db->quote($parent_eid);
    $qowned_by = $db->quote($owned_by);
    $result = $db->query("update tbl_stream set owned_by = $qowned_by where parent_eid = $qparent_eid");

    // get the list of children and populate the their owner info
    $result = $db->query("select eid, owned_by from tbl_stream where stream_type = 'SD' and name = ''");
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        $owned_by = $row['owned_by'];
        populateChildStreamOwner($db, $eid, $owned_by);
    }
}
