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
        $object = \Flexio\Object\Store::load($eid);
        if ($object === false)
            continue;

        $type = $object->getType();
        switch ($type)
        {
            default:
            case \Model::TYPE_PROJECT: // TODO: set default rights for project
            case \Model::TYPE_PROCESS: // TODO: set default rights for process
                break;

            case \Model::TYPE_PIPE:
                $object->grant(\Flexio\Base\Action::TYPE_READ_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_WRITE_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_EXECUTE, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_GROUP);
                $object->grant(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_GROUP);
                $object->grant(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_GROUP);
                $object->grant(\Flexio\Base\Action::TYPE_EXECUTE, \Flexio\Base\User::MEMBER_GROUP);
                break;

            case \Model::TYPE_CONNECTION:
                $object->grant(\Flexio\Base\Action::TYPE_READ_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_WRITE_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_GROUP);
                $object->grant(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_GROUP);
                // don't allow delete by default for group members for connections
                break;

            case \Model::TYPE_USER:
                $object->grant(\Flexio\Base\Action::TYPE_READ_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_WRITE_RIGHTS, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_READ, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_WRITE, \Flexio\Base\User::MEMBER_OWNER);
                $object->grant(\Flexio\Base\Action::TYPE_DELETE, \Flexio\Base\User::MEMBER_OWNER);
                // don't allow group memebers to access user info
                break;
        }
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

