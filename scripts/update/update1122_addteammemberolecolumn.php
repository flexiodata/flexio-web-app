<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-08-06
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
    // STEP 1: add the role column
    $db->exec("alter table tbl_teammember add column role varchar(1) NOT NULL default ''");

    // STEP 2: populate the role column; at this time, members that are also the owners
    // are the owners, and everyone else is a contributor
    // public const TEAM_ROLE_UNDEFINED       = '';
    // public const TEAM_ROLE_USER            = 'U';
    // public const TEAM_ROLE_CONTRIBUTOR     = 'C';
    // public const TEAM_ROLE_ADMINISTRATOR   = 'A';
    // public const TEAM_ROLE_OWNER           = 'O';
    $db->exec("update tbl_teammember set role = 'O' where member_eid = owned_by");
    $db->exec("update tbl_teammember set role = 'C' where role != 'O'");
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
