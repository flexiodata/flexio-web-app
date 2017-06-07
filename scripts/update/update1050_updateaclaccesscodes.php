<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-06-07
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
    // STEP 1: delete all the ACL object entries from the object
    // table; we'll be creating a new set of ACL entries from
    // the existing table records by inserting the new records
    // and deleting the old; these ACL eids aren't currently
    // referenced anywhere else
    deleteOldAclObjectEntries($db);


    // STEP 2: iterate through the rights and convert the owner/group
    // codes into specific user eids; simply copy the public type
    // add associated access code type
    $query_sql = "select * from tbl_acl";
    $result = $db->query($query_sql);

    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        updateAclAccessCodes($db, $row);
    }


    // STEP 3: delete the original records that we've converted (these
    // will be the records without any access code type)
    deleteOldAclAccessEntries($db);
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



function updateAclAccessCodes($db, $acl_entry)
{
    // create a new ACL entry item
    // TODO: implement

    // insert the new ACL entry
    $new_acl_entry = array();
    $db->insert('tbl_acl', $new_acl_entry, true /*ignore duplicate key*/);
}

function deleteOldAclObjectEntries($db)
{
    // TODO: current ACL entries were incorrectly created with the
    // \Model::TYPE_TOKEN eid type; to delete the old ACL entries
    // from the object table, use an inner join to the ACL table

    $sql = "delete from tbl_object where eid_type = '" . \Model::TYPE_RIGHT . "'";
    $db->exec($sql);
}

function deleteOldAclAccessCodes($db)
{
    // TODO: fill out
}

