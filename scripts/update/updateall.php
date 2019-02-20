<?php
/*!
 *
 * Copyright (c) 2012, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2012-06-02
 *
 * @package flexio
 * @subpackage Database_Update
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


// make an associative array of the update scripts.
// Example: array('1001' => '/my/path/update1001_xyz.php')

$min_ver = null;
$max_ver = null;

$files = glob(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'update????_*.php');

$updates = array();
foreach ($files as $f)
{
    $matches = array();
    if (preg_match("/update([0-9]{4})_/", $f, $matches))
    {
        $ver = (int)$matches[1];
        if (!isset($min_ver) || $ver < $min_ver)
            $min_ver = $ver;
        if (!isset($max_ver) || $ver > $max_ver)
            $max_ver = $ver;
        $updates[$ver] = $f;
    }
}

ksort($updates);

$current_version = \Flexio\System\System::getModel()->getDbVersionNumber($db);
if ($current_version === false)
    $current_version = 0;

if ($max_ver <= $current_version)
{
    echo "The database '". $params['dbname'] ."' is currently at version $current_version and is fully up-to-date.\nNo further upgrade is currently necessary.\n";
    exit(0);
}
 else
{
    echo "The database '". $params['dbname'] ."' is currently at version $current_version\nand will be upgraded to $max_ver.\n\n";
}



echo "This will update the following database:\n\n";
echo "         Host: " . $params['host'] . "\n";
echo "     Database: " . $params['dbname'] . "\n\n";

echo "Would you like to proceed? Type 'yes' to continue: ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if (trim($line) != 'yes') {
    echo "Aborted.\n";
    exit(0);
}
fclose($handle);
echo "\n";



// TODO: log message
// logMessage("Starting");

$php = \Flexio\System\System::getBinaryPath('php');





$prog_args = [ $params['host'], $params['username'], $params['password'], $params['dbname'] ];

foreach ($updates as $uv => $uf)
{
    if ($uv > $current_version)
    {
        // only process scripts that are newer than the current database version
        $name = pathinfo($uf, PATHINFO_BASENAME);

        // TODO: log message
        // logMessage("Running $name");
        $cmd = "$php $uf " . join(' ', $prog_args);
        system($cmd);
        echo "\n\n";
    }
}

$current_version = \Flexio\System\System::getModel()->getDbVersionNumber($db);

// TODO: log message
logMessage("Update complete.");
echo "Database has been upgraded to version $current_version.\n";



function logMessage($s)
{
    echo date("D M j G:i:s T Y") . ' - ' . $s . "\n";
}
