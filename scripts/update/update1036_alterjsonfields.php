<?php
/*!
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-01-17
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
    // convert jsonb fields to json; this allows json fields to retain
    // order of key elements, which might be important in some cases
    // (e.g. json commands specified by user)

    // STEP 1: update the user table
    $sql = <<<EOT
        alter table tbl_user
            alter column config type json using config::json;
EOT;
    $db->exec($sql);

    // STEP 2: update the pipe table
    $sql = <<<EOT
        alter table tbl_pipe
            alter column task type json using task::json,
            alter column input type json using input::json,
            alter column output type json using output::json;
EOT;
    $db->exec($sql);

  // STEP 3: update the process table
    $sql = <<<EOT
        alter table tbl_process
            alter column task type json using task::json,
            alter column input type json using input::json,
            alter column output type json using output::json,
            alter column process_info type json using process_info::json;
EOT;
    $db->exec($sql);

  // STEP 4: update the stream table
    $sql = <<<EOT
        alter table tbl_stream
            alter column structure type json using structure::json;
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
