<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-12-12
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
    // STEP 1: make sure the tbl_verify_code table doesn't exist
    $sql = <<<EOT
        DROP TABLE IF EXISTS tbl_verifycode;
EOT;
    $db->exec($sql);

    // STEP 2: add the stream table
    $sql = <<<EOT
        CREATE TABLE tbl_verifycode (
            id serial,
            verify_code varchar(20) NOT NULL default '',
            expires timestamp NULL default NULL,
            owned_by varchar(12) NOT NULL default '',
            created_by varchar(12) NOT NULL default '',
            created timestamp NULL default NULL,
            updated timestamp NULL default NULL,
            PRIMARY KEY (id)
        );
EOT;
    $db->exec($sql);

    // STEP 3: add the indexes
    $db->exec("CREATE UNIQUE INDEX idx_verifycode_owned_by_verify_code ON tbl_verifycode (owned_by, verify_code) WHERE verify_code != '';");
    $db->exec("CREATE INDEX idx_verifycode_expires ON tbl_verifycode (expires);");
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
