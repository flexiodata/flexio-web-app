<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-07-11
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
    // populate empty connection aliases with a valid modified form of the name

    // STEP 1: get a list of the pipes with an empty alias and
    // populate the alias info with a valid modified form of the name
    $result = $db->query("select eid, owned_by, name from tbl_pipe where alias = ''");
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        $owned_by = $row['owned_by'];
        $name = $row['name'];
        populateAliasFromName($db, 'tbl_pipe', $eid, $owned_by, $name);
    }

    // STEP 2: get a list of the connections with an empty alias and
    // populate the alias info with a valid modified form of the name
    $result = $db->query("select eid, owned_by, name from tbl_connection where alias = ''");
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        $owned_by = $row['owned_by'];
        $name = $row['name'];
        populateAliasFromName($db, 'tbl_connection', $eid, $owned_by, $name);
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



function populateAliasFromName($db, string $table, string $eid, string $owned_by, string $name) : void
{
    $alias = getValidAliasFromName($db, $table, $owned_by, $name);

    $qeid = $db->quote($eid);
    $qalias = $db->quote($alias);
    $result = $db->exec("update $table set alias = $qalias where eid = $qeid");
}

function getValidAliasFromName($db, string $table, string $owned_by, string $name) : string
{
    // convert the name into a valid alias:
    // 1. if the name is empty, start off with a prefix, that we'll append numbers to later
    // 2. if the name isn't empty, start off with the name and invalid characters with hyphens

    $idx = 1;
    $alias = ($table === "tbl_pipe" ? "pipe" : "connection") . "-" . $idx;
    if (strlen($name) > 0)
        $alias = preg_replace('/[^\da-zA-Z_-]/i', '-', $name);
    $alias = strtolower($alias);

    $base_alias = $alias;
    while (1)
    {
        // fall through if we can't find something
        if ($idx >= 100)
            break;

        if (aliasExists($db, $table, $owned_by, $alias) === false)
            return $alias;

        $idx++;
        $alias = $base_alias . "-" . $idx;
    }

    // if we can't find a something valid from what's there,
    // then return a random string
    return \Flexio\Base\Util::generateRandomString(10);
}

function aliasExists($db, string $table, string $owned_by, string $alias) : bool
{
    $qowned_by = $db->quote($owned_by);
    $qalias = $db->quote($alias);

    $existing_eid = $db->fetchOne("select eid from $table where owned_by = $qowned_by and alias = $qalias");
    if ($existing_eid !== false)
        return true;

    return false;
}

