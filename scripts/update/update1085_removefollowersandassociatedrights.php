<?php
/*!
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-03-16
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
    // note: sharing has been disabled in the current version, and we want
    // to remove lingering rights so that when it's reactivated, we have
    // a fresh slate

    // STEP 1: remove rights for non-owners by deleting entries where
    // the acl access_code is an eid and the acl object_eid and access_code
    // don't correspond to any known eid and owned_by eid in any of the tables
    // that have owner info set (accept the acl table itself, which doesn't
    // use the owned by column)

    // const ACCESS_CODE_TYPE_UNDEFINED = '';
    // const ACCESS_CODE_TYPE_EID       = 'E;
    // const ACCESS_CODE_TYPE_TOKEN     = 'TKN';
    // const ACCESS_CODE_TYPE_EMAIL     = 'EML';
    // const ACCESS_CODE_TYPE_CATEGORY  = 'CAT';

    $sql = <<<EOT
        delete from
            tbl_acl
        where
            tbl_acl.access_type = 'EID' and
            not exists (select 1 from tbl_user where tbl_acl.object_eid = tbl_user.eid and tbl_acl.access_code = tbl_user.owned_by) and
            not exists (select 1 from tbl_token where tbl_acl.object_eid = tbl_token.eid and tbl_acl.access_code = tbl_token.owned_by) and
            not exists (select 1 from tbl_pipe where tbl_acl.object_eid = tbl_pipe.eid and tbl_acl.access_code = tbl_pipe.owned_by) and
            not exists (select 1 from tbl_connection where tbl_acl.object_eid = tbl_connection.eid and tbl_acl.access_code = tbl_connection.owned_by) and
            not exists (select 1 from tbl_process where tbl_acl.object_eid = tbl_process.eid and tbl_acl.access_code = tbl_process.owned_by) and
            not exists (select 1 from tbl_processlog where tbl_acl.object_eid = tbl_processlog.eid and tbl_acl.access_code = tbl_processlog.owned_by) and
            not exists (select 1 from tbl_stream where tbl_acl.object_eid = tbl_stream.eid and tbl_acl.access_code = tbl_stream.owned_by) and
            not exists (select 1 from tbl_comment where tbl_acl.object_eid = tbl_comment.eid and tbl_acl.access_code = tbl_comment.owned_by) and
            not exists (select 1 from tbl_action where tbl_acl.object_eid = tbl_action.eid and tbl_acl.access_code = tbl_action.owned_by);
EOT;
    $db->exec($sql);


    // STEP 2: remove the followers associations

    // const EDGE_FOLLOWING     = 'FLW';  // user A is following object B
    // const EDGE_FOLLOWED_BY   = 'FLB';  // object A is followed by user B

    $db->exec("delete from tbl_association where association_type in ('FLW', 'FLB')");

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
