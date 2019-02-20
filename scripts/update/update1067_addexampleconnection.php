<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-05
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
    // STEP 1: get the directory for the example connection
    $demo_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'demo' . DIRECTORY_SEPARATOR;
    $file_name = $demo_dir . 'connection_amazons3.json';

    // STEP 2: get a list of users; for each user add the new example connection
    $sql = 'select eid from tbl_user';
    $result = $db->query($sql);

    while ($result && ($row = $result->fetch()))
    {
        $user_eid = $row['eid'];
        $user = \Flexio\Object\User::load($user_eid);
        if ($user === false)
            continue;

        \Flexio\Api\User::createConnectionFromFile($user_eid, $file_name);
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

