<?php
/*!
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-10-23
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


function isset_and_populated(&$v)
{
    if (isset($v) && strlen($v) > 0)
        return true;
         else
        return false;
}

try
{

    $sql = 'select * from tbl_connection';
    $result = $db->query($sql);


    // iterate through each of the connections and populate the new
    // connection fields with info from the configuration field
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];
        $config = @json_decode($row['configuration'], true);

        $update = [];

        $update['server_username'] = $row['server_username'];
        $update['server_password'] = $row['server_password'];
		$update['token']           = $row['token'];

		if (substr($update['server_username'], 0, 4) == 'ZZXV') $update['server_username'] = \Flexio\Base\Util::decrypt($update['server_username'], $g_store->connection_enckey);
		if (substr($update['server_password'], 0, 4) == 'ZZXV') $update['server_password'] = \Flexio\Base\Util::decrypt($update['server_password'], $g_store->connection_enckey);
		if (substr($update['token'], 0, 4) == 'ZZXV')           $update['token'] = \Flexio\Base\Util::decrypt($update['token'], $g_store->connection_enckey);

        if (isset_and_populated($config['server']))          $update['server'] =           $config['server'];
        if (isset_and_populated($config['port']))            $update['server_port'] =      $config['port'];
        if (isset_and_populated($config['user']))            $update['server_username'] =  $config['user'];
        if (isset_and_populated($config['password']))        $update['server_password'] =  $config['password'];
        if (isset_and_populated($config['database']))        $update['location_group'] =   $config['database'];
        if (isset_and_populated($config['key']))/*twilio*/   $update['server_username'] =  $config['key'];
        if (isset_and_populated($config['token']))           $update['token'] =            $config['token'];
        if (isset_and_populated($config['expires']))         $update['token_expires'] =    $config['expires'];


		$update['server_username'] = \Flexio\Base\Util::encrypt($update['server_username'], $g_store->connection_enckey);
		$update['server_password'] = \Flexio\Base\Util::encrypt($update['server_password'], $g_store->connection_enckey);
		$update['token'] = \Flexio\Base\Util::encrypt($update['token'], $g_store->connection_enckey);

        $db->update('tbl_connection', $update, 'eid = ' . $db->quote($eid));
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
