<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
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

    $sql = 'select count(*) as cnt from tbl_object';
    $r = $db->query($sql);
    $row = $r->fetch();
    echo("Total To Process: " . $row['cnt']);
    echo("\n\n");


    // STEP 1: get a list of all objects
    $sql = 'select eid from tbl_object';
    $result = $db->query($sql);

    // STEP 2: iterate through each of the objects and populate the
    // rights column with the default rights

    $count = 0;
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $count++;
        if ($count % 1000 === 0)
            echo("Finished With: $count\n");

        // load the object
        $eid = $row['eid'];
        $object = \Flexio\Object\Store::load($eid);
        if ($object === false)
            continue;

        $type = $object->getType();
        switch ($type)
        {
            default:
                break;

            case \Model::TYPE_PROCESS: // processes inherit owner privileges for the pipe at this time
            case \Model::TYPE_PIPE:
                $rights = array(
                    array('action' => 'rights.read',    'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'rights.write',   'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.read',    'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.write',   'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.delete',  'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.execute', 'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.read',    'access_code' => \Flexio\Object\User::MEMBER_GROUP, 'access_type' => ''),
                    array('action' => 'object.write',   'access_code' => \Flexio\Object\User::MEMBER_GROUP, 'access_type' => ''),
                    array('action' => 'object.delete',  'access_code' => \Flexio\Object\User::MEMBER_GROUP, 'access_type' => ''),
                    array('action' => 'object.execute', 'access_code' => \Flexio\Object\User::MEMBER_GROUP, 'access_type' => '')
                );
                \Flexio\System\System::getModel()->addRights($eid, $rights);
                break;

            case \Model::TYPE_PROJECT:
            case \Model::TYPE_CONNECTION:
                $rights = array(
                    array('action' => 'rights.read',   'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'rights.write',  'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.read',   'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.write',  'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.delete', 'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.read',   'access_code' => \Flexio\Object\User::MEMBER_GROUP, 'access_type' => ''),
                    array('action' => 'object.write',  'access_code' => \Flexio\Object\User::MEMBER_GROUP, 'access_type' => '')
                );
                // don't allow delete by default for group members for connections
                \Flexio\System\System::getModel()->addRights($eid, $rights);
                break;

            case \Model::TYPE_USER:
                $rights = array(
                    array('action' => 'rights.read',  'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'rights.write', 'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.read',  'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.write', 'access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => ''),
                    array('action' => 'object.delete','access_code' => \Flexio\Object\User::MEMBER_OWNER, 'access_type' => '')
                );
                // only allow owners to access user info
                \Flexio\System\System::getModel()->addRights($eid, $rights);
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

