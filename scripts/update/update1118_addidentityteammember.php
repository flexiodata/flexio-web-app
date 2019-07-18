<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-07-18
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
    // STEP 1: all users to the team member table as a team member
    // of their own team; member status is active by default for
    // the identify team member; rights are currently '[]' since
    // they're not yet set anywhere

    // public const TEAM_MEMBER_STATUS_UNDEFINED = '';
    // public const TEAM_MEMBER_STATUS_PENDING = 'P';
    // public const TEAM_MEMBER_STATUS_INACTIVE = 'I';
    // public const TEAM_MEMBER_STATUS_ACTIVE = 'A';

    $sql = <<<EOT
        insert into tbl_teammember (
            member_eid,
            member_status,
            rights,
            owned_by,
            created_by,
            created,
            updated
        )
        select
            eid as member_eid,
            'A' as member_status,
            '[]' as rights,
            eid as owned_by,
            eid as created_by,
            created as created,
            created as updated
        from tbl_user;
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
