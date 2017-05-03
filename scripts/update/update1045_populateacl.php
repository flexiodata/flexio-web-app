<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-04-24
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
    // STEP 1: get a list of all objects
    $sql = 'select eid from tbl_object';
    $result = $db->query($sql);


    // STEP 2: iterate through each of the objects and populate the
    // rights column with the default rights
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        // load the object
        $eid = $row['eid'];
        $type = $row['type'];

        $acl = \Flexio\Object\Acl::create();

        if ($type === \Model::TYPE_PROJECT)
        {
            // TODO: set default project rights
        }

        if ($type === \Model::TYPE_PIPE)
        {
            $acl->add(\Flexio\Base\Action::TYPE_READ_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_WRITE_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);

            $acl->add(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_EXECUTE, \Flexio\Base\User::MEMBER_OWNER);

            $acl->add(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_GROUP);
            $acl->add(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_GROUP);
            $acl->add(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_GROUP);
            $acl->add(\Flexio\Base\Action::TYPE_EXECUTE, \Flexio\Base\User::MEMBER_GROUP);
        }

        if ($type === \Model::TYPE_CONNECTION)
        {
            $acl->add(\Flexio\Base\Action::TYPE_READ_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_WRITE_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);

            $acl->add(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_OWNER);

            $acl->add(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_GROUP);
            $acl->add(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_GROUP);
            // turn off delete by default for group members for pipes
        }

        if ($type === \Model::TYPE_PROCESS)
        {
            // TODO: set default process rights
        }

        if ($type === \Model::TYPE_USER)
        {
            $acl->add(\Flexio\Base\Action::TYPE_READ_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_WRITE_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);

            $acl->add(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_OWNER);
            $acl->add(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_OWNER);

            // turn of rights for group members for user person info
        }

        $rights = json_encode($acl->get());
        \Flexio\System\System::getModel()->setRights($rights);
    }
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

