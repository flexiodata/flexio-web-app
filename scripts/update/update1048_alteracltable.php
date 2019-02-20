<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-06-02
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
    // STEP 1: rename the acl table
    $sql = <<<EOT
        ALTER TABLE tbl_acl RENAME TO tbl_acl_old;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_acl_object_eid;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_acl_object_eid_action;
EOT;
    $db->exec($sql);


    // STEP 2: create a new acl table
    $sql = <<<EOT
        DROP TABLE IF EXISTS tbl_acl;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_acl_object_eid;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        DROP INDEX IF EXISTS idx_acl_access_code;
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        CREATE TABLE IF NOT EXISTS tbl_acl (
            id serial,
            eid varchar(12) NOT NULL,
            object_eid varchar(12) NOT NULL default '',
            access_type varchar(3) NOT NULL default '',
            access_code varchar(255) NOT NULL default '',
            actions json,
            created timestamp NULL default NULL,
            updated timestamp NULL default NULL,
        PRIMARY KEY (id),
        UNIQUE (eid)
        );
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        CREATE INDEX idx_acl_object_eid ON tbl_acl (object_eid);
EOT;
    $db->exec($sql);

    $sql = <<<EOT
        CREATE INDEX idx_acl_access_code ON tbl_acl (access_code);
EOT;
    $db->exec($sql);


    // STEP 3: copy the values
    copyAclInfo($db);


    // STEP 4: drop the old acl table
    $sql = <<<EOT
        DROP TABLE IF EXISTS tbl_acl_old;
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


function copyAclInfo($db)
{
    // STEP 1: get a list of the existing acl entries
    $query_sql = "select
                      object_eid,
                      access_type,
                      access_code,
                      json_agg(action) as actions,
                      min(created) as created,
                      min(updated) as updated
                  from tbl_acl_old
                  group by
                      object_eid,
                      access_type,
                      access_code
                ";
    $result = $db->query($query_sql);

    // STEP 2: for each of the old acl action lists, transfer the
    // values to the new acl table
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        // transfer the acl items
        $actions = json_decode($row['actions'], true);
        if (is_array($actions))
        {
            sort($actions);
            $row['actions'] = json_encode($actions);
        }
        $eid = \Flexio\System\System::getModel()->right->create($row);

        // set the created/updated date
        $update = array();
        $update['created'] = $row['created'];
        $update['updated'] = $row['updated'];
        $db->update('tbl_acl', $update, 'eid = ' . $db->quote($eid));
    }
}
