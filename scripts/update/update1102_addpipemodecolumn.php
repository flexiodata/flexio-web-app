<?php
/*!
 *
 * Copyright (c) 2018, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-07-13
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
    // STEP 1: add the new field
    $sql = <<<EOT
        alter table tbl_pipe
            add column pipe_mode varchar(1) NOT NULL default '';
EOT;
    $db->exec($sql);

    // STEP 2: populate the new field
    // public const PIPE_DEPLOY_MODE_UNDEFINED  = '';
    // public const PIPE_DEPLOY_MODE_BUILD      = 'B';
    // public const PIPE_DEPLOY_MODE_RUN        = 'R';
    $sql = <<<EOT
        update tbl_pipe set pipe_mode = 'B' where pipe_mode = '';
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
