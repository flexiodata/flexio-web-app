<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-03-16
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
    // STEP 1: clear out unused associations

    // const EDGE_CREATED       = 'CRT';  // user A created object B
    // const EDGE_CREATED_BY    = 'CRB';  // object A was created by user B
    // const EDGE_OWNS          = 'OWN';  // user A owns object B
    // const EDGE_OWNED_BY      = 'OWB';  // object A is owned by user B
    // const EDGE_MEMBER_OF     = 'MBO';  // object A is a member of object B
    // const EDGE_HAS_MEMBER    = 'HMB';  // object A has member object B
    // const EDGE_PROCESS_OF    = 'PRO';  // object A is a process of object B
    // const EDGE_HAS_PROCESS   = 'HPR';  // object A has process that is object B

    $db->exec("delete from tbl_association where association_type in ('CRT', 'CRB', 'OWN', 'OWB', 'MBO', 'HMB', 'PRO', 'HPR')");
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
