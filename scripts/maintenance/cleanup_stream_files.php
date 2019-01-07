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


$config = @file_get_contents(__DIR__ . '/../../config/config.json');
$config = json_decode($config, true);


$params = array('host' => $config['directory_database_host'],
                'port' => 5432,
                'username' => $config['directory_database_username'],
                'password' => $config['directory_database_password'],
                'dbname' => $config['directory_database_dbname']);


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


$dryrun = true;
$limit = 1000000;
$log_increment = 10000;
$path    = '/srv/www/flexio/store/streams/';

$total_count = 0;
$deleted_count = 0;


// get a list of the items in /srv/www/flexio/store/streams
$items = scandir($path);
$total_count = count($items);


// get a list of the stream paths in database
$stream_paths = getStreamsInDb($db);


logMessage("Starting; $total_count files to process");


try
{
    // for each one of these items, see if it's in tbl_stream; if not, delete it
    $count = 0;
    foreach ($items as $i)
    {
        $count++;

        if ($dryrun === false)
        {
            if ($count % $log_increment === 0)
                logMessage("Processed $count files; deleted $deleted_count of $total_count files");
        }
         else
        {
            if ($count % $log_increment === 0)
                logMessage("Processed $count files; simulated delete $deleted_count of $total_count files");
        }

        if ($count >= $limit)
            break;

        $name = $i;
        $full_name = $path . $name;

        // make sure the item isn't a directory
        if (is_file($full_name) === false)
            continue;

        // if the item exists in the database, don't delete it
        if (array_key_exists($name, $stream_paths))
            continue;

        if ($dryrun === false)
            unlink($full_name);

        $deleted_count++;
    }
}
catch(\Exception $e)
{
    logMessage("Operation failed.");
    exit(0);
}


if ($dryrun === false)
    logMessage("Finished; deleted $deleted_count of $total_count files");
     else
    logMessage("Finished; simulated delete $deleted_count of $total_count files");


function getStreamsInDb($db)
{
    try
    {
        $result = $db->query("select path from tbl_stream");

        $items = array();
        while ($result && ($row = $result->fetch()))
            $items[$row['path']] = 1;

        return $items;
    }
    catch (\Exception $e)
    {
        return false;
    }
}

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

