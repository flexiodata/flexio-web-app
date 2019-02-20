<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-03-29
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
    $db->exec("DROP TABLE IF EXISTS tbl_action");


    // STEP 2: add the new action table
    $sql = <<<EOT
CREATE TABLE tbl_action (
    id serial,
    eid varchar(12) NOT NULL default '',
    eid_status varchar(1) NOT NULL default '',
    action_type varchar(40) NOT NULL default '',
    request_ip varchar(40) NOT NULL default '',
    request_type varchar(12) NOT NULL default '',
    request_method varchar(12) NOT NULL default '',
    request_route text,
    request_created_by varchar(12) NOT NULL default '',
    request_created timestamp NULL default NULL,
    request_params json,
    target_eid varchar(12) NOT NULL default '',
    target_eid_type varchar(3) NOT NULL default '',
    target_owned_by varchar(12) NOT NULL default '',
    response_type varchar(12) NOT NULL default '',
    response_code varchar(12) NOT NULL default '',
    response_params json,
    response_created timestamp NULL default NULL,
    owned_by varchar(12) NOT NULL default '',
    created_by varchar(12) NOT NULL default '',
    created timestamp NULL default NULL,
    updated timestamp NULL default NULL,
    PRIMARY KEY (id),
    UNIQUE (eid)
    );
EOT;
    $db->exec($sql);


    // STEP 3: add the indexes
    $db->exec("CREATE INDEX idx_action_action_type ON tbl_action (action_type);");
    $db->exec("CREATE INDEX idx_action_request_created_by ON tbl_action (request_created_by);");
    $db->exec("CREATE INDEX idx_action_target_eid ON tbl_action (target_eid);");
    $db->exec("CREATE INDEX idx_action_target_eid_type ON tbl_action (target_eid_type);");
    $db->exec("CREATE INDEX idx_action_target_owned_by ON tbl_action (target_owned_by);");
    $db->exec("CREATE INDEX idx_action_owned_by ON tbl_action (owned_by);");
    $db->exec("CREATE INDEX idx_action_created ON tbl_action (created);");
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
