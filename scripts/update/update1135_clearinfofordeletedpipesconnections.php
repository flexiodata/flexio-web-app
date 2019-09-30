<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-09-30
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
    // STEP 1: clear connection_info and setup_config for deleted connections;
    // note: connection_info and setup_config are encrypted, so encrypt an empty
    // json object
    $connection_info = \Flexio\Base\Util::encrypt('{}', $GLOBALS['g_store']->connection_enckey);
    $setup_config = \Flexio\Base\Util::encrypt('{}', $GLOBALS['g_store']->connection_enckey);

    $sql = <<<EOT
        update tbl_connection set
            updated=updated, connection_info='$connection_info', setup_config='$setup_config'
        where eid_status = 'D';
EOT;
    $db->exec($sql);

    // STEP 2: clear any lingering names for pending/deleted connections
    $sql = <<<EOT
        update tbl_connection set
            updated=updated, name=''
        where eid_status = 'P' or eid_status = 'D';
EOT;
    $db->exec($sql);

    // STEP 3: clear any lingering names for pending/deleted pipes
    $sql = <<<EOT
        update tbl_pipe set
            updated=updated, name=''
        where eid_status = 'P' or eid_status = 'D';
EOT;
    $db->exec($sql);
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
