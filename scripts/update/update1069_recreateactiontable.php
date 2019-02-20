<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-07
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
    // STEP 1: drop the existing action table; not used
    $sql = <<<EOT
        DROP TABLE IF EXISTS tbl_action;
EOT;
    $db->exec($sql);


    // STEP 2: add the new action table
    $sql = <<<EOT
CREATE TABLE tbl_action (
    id serial,
    eid varchar(12) NOT NULL default '',
    invoked_from varchar(3) NOT NULL default '',
    invoked_by varchar(12) NOT NULL default '',
    action_type text default '',
    action_info json,
    action_target varchar(12) NOT NULL default '',
    result_type text default '',
    result_info json,
    started timestamp NULL default NULL,
    finished timestamp NULL default NULL,
    created timestamp NULL default NULL,
    updated timestamp NULL default NULL,
    PRIMARY KEY (id),
    UNIQUE (eid)
);
EOT;
    $db->exec($sql);


    // STEP 3: add the indexes
    $sql = <<<EOT
        CREATE INDEX idx_action_invoked_by ON tbl_action (invoked_by);
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        CREATE INDEX idx_action_action_target ON tbl_action (action_target);
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
