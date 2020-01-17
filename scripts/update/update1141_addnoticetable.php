<?php
/*!
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-01-14
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
    // STEP 1: make sure the tbl_notice table doesn't exist
    $sql = <<<EOT
        DROP TABLE IF EXISTS tbl_notice;
EOT;
    $db->exec($sql);

    // STEP 2: add the notice table
    $sql = <<<EOT
        CREATE TABLE tbl_notice (
            id serial,
            to_eid varchar(12) NOT NULL default '',
            notice_type varchar(40) NOT NULL default '',
            owned_by varchar(12) NOT NULL default '',
            created_by varchar(12) NOT NULL default '',
            created timestamp NULL default NULL,
            updated timestamp NULL default NULL,
            PRIMARY KEY (id)
        );
EOT;
    $db->exec($sql);

    // STEP 3: add the indexes
    $db->exec("CREATE INDEX idx_notice_to_eid ON tbl_notice (to_eid);");
    $db->exec("CREATE INDEX idx_notice_owned_by ON tbl_notice (owned_by);");
    $db->exec("CREATE INDEX idx_notice_created_by ON tbl_notice (created_by);");
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
