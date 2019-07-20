<?php
/*!
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-07-19
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
    // we want to allow user records to be created that don't yet have a username
    // or email assigned to them, but we still want username and email to be
    // unique if they are assigned

    // STEP 1: add unique conditional indexes that allow empty strings
    $db->exec("drop index if exists idx_user_username");
    $db->exec("drop index if exists idx_user_email");
    $db->exec("drop index if exists idx_user_verify_code");
    $db->exec("create unique index idx_user_username on tbl_user (username) where username != '';");
    $db->exec("create unique index idx_user_email on tbl_user (email) where email != '';");
    $db->exec("create unique index idx_user_verify_code on tbl_user (verify_code) where verify_code != '';");

    // STEP 2: drop the old unique indexes that didn't have the condition
    // these were created as unique constraints in the tbl_user create
    // statement as:
    //     UNIQUE (username)
    //     UNIQUE (email)
    // which translate into the index names
    //     tbl_user_username_key
    //     tbl_user_email_key
    $sql = <<<EOT
        alter table tbl_user
            drop constraint if exists tbl_user_username_key,
            drop constraint if exists tbl_user_user_name_key,
            drop constraint if exists tbl_user_email_key;
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

