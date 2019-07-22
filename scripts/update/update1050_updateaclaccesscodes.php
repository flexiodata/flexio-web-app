<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
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
    $sql = 'select count(*) as cnt from tbl_acl';
    $r = $db->query($sql);
    $row = $r->fetch();
    echo("Starting.  Total To Process: " . $row['cnt']);
    echo("\n\n");


    // TODO: activate when other parts of the script are finished
    // STEP 1: delete all the ACL object entries from the object
    // table; we'll be creating a new set of ACL entries from
    // the existing table records by inserting the new records
    // and deleting the old; these ACL eids aren't currently
    // referenced anywhere else
    echo("Deleting old acl object table entries...");
    echo("\n");

    deleteOldObjectTableAclEntries($db);


    // STEP 2: iterate through the rights and convert the owner/group
    // codes into specific user eids; simply copy the public type
    // add associated access code type

    echo("Converting ACL table entries...");
    echo("\n");

    $query_sql = "select * from tbl_acl";
    $result = $db->query($query_sql);

    $count = 0;
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $count++;
        if ($count % 1000 === 0)
            echo("Converted: $count\n");

        convertAclEntry($db, $row);
    }

    // TODO: activate when other parts of the script are finished
    // STEP 3: delete the original records that we've converted (these
    // will be the records without any access code type)
    echo("Deleting old acl table entries...");
    echo("\n\n");

    deleteOldAclTableAclEntries($db);
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



function convertAclEntry($db, $acl_entry)
{
    // load the object
    $object_eid = $acl_entry['object_eid'];
    $object = \Flexio\Object\Store::load($object_eid);
    if ($object === false)
        return;

    // note: following constants were previous defined and used
    // here in place of values:
    // const MEMBER_UNDEFINED = '';
    // const MEMBER_OWNER     = 'owner';
    // const MEMBER_GROUP     = 'member';
    // const MEMBER_PUBLIC    = 'public';

    $access_code = $acl_entry['access_code'];
    if ($access_code === 'owner')
    {
        // if the access code is for the owner class, replace the owner
        // class identifier with the owner eid and add a new acl entry
        // with the updated info

        $owner_eid = $object->getOwner();
        if (is_string($owner_eid))
        {
            // create a new ACL entry item
            $new_acl_eid = createObjectBaseLocal($db, \Model::TYPE_RIGHT, \Model::STATUS_AVAILABLE, $acl_entry['created'], $acl_entry['updated']);

            // originally in Model:
            // public const ACCESS_CODE_TYPE_UNDEFINED = '';
            // public const ACCESS_CODE_TYPE_EID       = 'EID';
            // public const ACCESS_CODE_TYPE_TOKEN     = 'TKN';
            // public const ACCESS_CODE_TYPE_EMAIL     = 'EML';
            // public const ACCESS_CODE_TYPE_CATEGORY  = 'CAT';

            // insert the new ACL entry
            $new_acl_entry = array();
            $new_acl_entry['eid'] = $new_acl_eid;
            $new_acl_entry['object_eid'] = $object_eid;
            $new_acl_entry['access_type'] = 'EID'; // ACCESS_CODE_TYPE_EMAIL
            $new_acl_entry['access_code'] = $owner_eid;
            $new_acl_entry['actions'] = $acl_entry['actions'];
            $new_acl_entry['created'] = $acl_entry['created'];
            $new_acl_entry['updated'] = $acl_entry['updated'];

            $db->insert('tbl_acl', $new_acl_entry);
        }
    }

    if ($access_code === 'member')
    {
        // if the access code is for the member class, add a new acl entry
        // for each of the object followers

        $followers = \Flexio\System\System::getModel()->assoc_range($object_eid, \Model::EDGE_FOLLOWED_BY);
        if (!is_array($followers))
            return;

        foreach ($followers as $f)
        {
            $follower_eid = $f['eid'];
            if (is_string($follower_eid))
            {
                // create a new ACL entry item
                $new_acl_eid = createObjectBaseLocal($db, \Model::TYPE_RIGHT, \Model::STATUS_AVAILABLE, $acl_entry['created'], $acl_entry['updated']);

                // originally in Model:
                // public const ACCESS_CODE_TYPE_UNDEFINED = '';
                // public const ACCESS_CODE_TYPE_EID       = 'EID';
                // public const ACCESS_CODE_TYPE_TOKEN     = 'TKN';
                // public const ACCESS_CODE_TYPE_EMAIL     = 'EML';
                // public const ACCESS_CODE_TYPE_CATEGORY  = 'CAT';

                // insert the new ACL entry
                $new_acl_entry = array();
                $new_acl_entry['eid'] = $new_acl_eid;
                $new_acl_entry['object_eid'] = $object_eid;
                $new_acl_entry['access_type'] = 'EID'; // ACCESS_CODE_TYPE_EID
                $new_acl_entry['access_code'] = $follower_eid;
                $new_acl_entry['actions'] = $acl_entry['actions'];
                $new_acl_entry['created'] = $acl_entry['created'];
                $new_acl_entry['updated'] = $acl_entry['updated'];

                $db->insert('tbl_acl', $new_acl_entry);
            }
        }
    }

    if ($access_code === 'public')
    {
        // if the access code is for the public class, add a new entry
        // with the appropriate access type value

        // create a new ACL entry item
        $new_acl_eid = createObjectBaseLocal($db, \Model::TYPE_RIGHT, \Model::STATUS_AVAILABLE, $acl_entry['created'], $acl_entry['updated']);

        // originally in Model:
        // public const ACCESS_CODE_TYPE_UNDEFINED = '';
        // public const ACCESS_CODE_TYPE_EID       = 'EID';
        // public const ACCESS_CODE_TYPE_TOKEN     = 'TKN';
        // public const ACCESS_CODE_TYPE_EMAIL     = 'EML';
        // public const ACCESS_CODE_TYPE_CATEGORY  = 'CAT';

        // insert the new ACL entry
        $new_acl_entry = array();
        $new_acl_entry['eid'] = $new_acl_eid;
        $new_acl_entry['object_eid'] = $object_eid;
        $new_acl_entry['access_type'] = 'CAT'; // ACCESS_CODE_TYPE_CATEGORY
        $new_acl_entry['access_code'] = 'public';
        $new_acl_entry['actions'] = $acl_entry['actions'];
        $new_acl_entry['created'] = $acl_entry['created'];
        $new_acl_entry['updated'] = $acl_entry['updated'];

        $db->insert('tbl_acl', $new_acl_entry);
    }
}

function deleteOldObjectTableAclEntries($db)
{
    // note: current ACL entries were incorrectly created with the
    // \Model::TYPE_TOKEN eid type; to delete the old ACL entries
    // from the object table, use the eids in the acl table (use
    // the old acl format indicator that had no access_type to identify
    // older records)

    $sql = <<<EOT
        delete from
            tbl_object
        where
            (tbl_object.eid_type = 'ATH' or tbl_object.eid_type = 'ACL') and
            not exists (select 1 from tbl_token where tbl_object.eid = tbl_token.eid) and
            not exists (select 1 from tbl_acl where tbl_object.eid = tbl_acl.eid and tbl_acl.access_type != '');
EOT;
    $db->exec($sql);
}

function deleteOldAclTableAclEntries($db)
{
    // originally in Model:
    // public const ACCESS_CODE_TYPE_UNDEFINED = '';
    // public const ACCESS_CODE_TYPE_EID       = 'EID';
    // public const ACCESS_CODE_TYPE_TOKEN     = 'TKN';
    // public const ACCESS_CODE_TYPE_EMAIL     = 'EML';
    // public const ACCESS_CODE_TYPE_CATEGORY  = 'CAT';

    $sql = "delete from tbl_acl where access_type = ''"; // ACCESS_CODE_TYPE_UNDEFINED
    $db->exec($sql);
}

function createObjectBaseLocal($db, $type, $status, $created, $updated)
{
    $eid = generateUniqueEidLocal($db);
    $process_arr = array(
        'eid'           => $eid,
        'eid_type'      => $type,
        'eid_status'    => $status,
        'created'       => $created,
        'updated'       => $updated
    );

    $db->insert('tbl_object', $process_arr);
    return $eid;
}

function generateUniqueEidLocal($db)
{
    // generate an eid that doesn't already exists in tbl_object
    $eid = \Flexio\Base\Eid::generate();

    $qeid = $db->quote($eid);
    $result = $db->fetchOne("select eid from tbl_object where eid = $qeid");

    if ($result === false)
        return $eid;

    return generateUniqueEid();
}
