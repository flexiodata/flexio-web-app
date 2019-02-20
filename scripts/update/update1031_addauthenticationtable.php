<?php
/*!
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-24
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
    // STEP 1: make sure the authentication table doesn't exist
    $sql = <<<EOT
        DROP TABLE IF EXISTS tbl_authentication;
EOT;
    $db->exec($sql);


    // STEP 2: add the authentication table
    $sql = <<<EOT
CREATE TABLE tbl_authentication (
    id serial,
    eid varchar(12) NOT NULL,
    user_eid varchar(12) NOT NULL,
    access_code varchar(255) NOT NULL default '',
    secret_code varchar(255) NOT NULL default '',
    created timestamp NULL default NULL,
    updated timestamp NULL default NULL,
    PRIMARY KEY (id),
    UNIQUE (eid),
    UNIQUE (access_code)
);
EOT;
    $db->exec($sql);


    // STEP 3: add the indexes
    $sql = <<<EOT
        CREATE INDEX idx_authentication_user_eid ON tbl_authentication (user_eid);
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
