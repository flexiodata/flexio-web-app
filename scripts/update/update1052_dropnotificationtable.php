<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-07-27
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
    // STEP 1: drop the old notification table
    $sql = <<<EOT
        DROP TABLE IF EXISTS tbl_notification;
EOT;
    $db->exec($sql);


    // STEP 2: drop the indexes associated with the table
    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_notification_user_eid;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_notification_source_user_eid;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_notification_object_eid;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_notification_subject_eid;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_notification_notice_type;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_notification_created;
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
