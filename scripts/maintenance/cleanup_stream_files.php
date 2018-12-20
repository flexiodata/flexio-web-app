<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-12-20
 *
 * @package flexio
 * @subpackage Maintenance
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


logMessage("Starting");


$dryrun = true;
$limit = 1000000;
$path    = '/srv/www/flexio/store/streams/';


try
{
    // STEP 1: get a list of the items in /srv/www/flexio/store/streams
    $items = scandir($path);

    // STEP 2: for each one of these items, see if it's in tbl_stream;
    // if not, delete it
    $count = 0;
    foreach ($items as $i)
    {
        $count++;

        if ($count >= $limit)
            break;

        $name = $i;
        $full_name = $path . $name;

        // make sure the item isn't a directory
        if (is_file($full_name) === false)
        {
            logMessage("Ignoring $name; item is a directory");
            continue;
        }

        // if the item exists in the database, don't delete it
        if (streamExistsInDb($db, $name))
        {
            //logMessage("Ignoring $name; item exists in the database");
            continue;
        }

        if ($dryrun === false)
        {
            // delete the file
            unlink($full_name);
            logMessage("Deleting $name");
        }
         else
        {
            logMessage("Dry run; following would be deleted: $name");
        }
    }
}
catch(\Exception $e)
{
    logMessage("Operation failed.");
    exit(0);
}


logMessage("Cleanup complete.");



function streamExistsInDb($db, $path)
{
    $qpath = $db->quote($path);
    $result = $db->fetchOne("select eid from tbl_stream where path = $qpath");

    if ($result !== false)
        return true;

    return false;
}


function logMessage($s)
{
    echo date("D M j G:i:s T Y") . ' - ' . $s . "\n";
}

