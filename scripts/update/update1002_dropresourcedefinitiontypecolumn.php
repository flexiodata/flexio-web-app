<?php
/*!
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-08-14
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
    // SETUP: copy of upload job template in LoadJob.php
    $upload_job_template = <<<EOD
    {
        "metadata": {
            "type": "application/vnd.flexio.upload-job",
            "version": 1,
            "description": ""
        },
        "params": {
        }
    }
EOD;
    $upload_job_template = json_decode($upload_job_template);
    $upload_job_template = json_encode($upload_job_template);

    // SETUP: copy of load job template in LoadJob.php
    $load_job_template = <<<EOD
    {
        "metadata": {
            "type": "application/vnd.flexio.load-job",
            "version": 1,
            "description": ""
        },
        "params": {
            "load_type": "table",
            "add_xdrowid": true
        }
    }
EOD;
    $load_job_template = json_decode($load_job_template);
    $load_job_template = json_encode($load_job_template);

    // STEP 1: make sure we have appropriate job definitions for situations
    // where the mime_type is 'fx.resource.upload' or 'fx.resource.define'
    // (for example, the 'job definition' for 'fx.resource.upload' was previously
    // '{}', so after removing the mime_type, we wouldn't have any record
    // of the job specifics without this replace)
    $sql = "update tbl_resource set definition = '$upload_job_template' where definition_type = 'fx.resource.upload'";
    $db->exec($sql);
    $sql = "update tbl_resource set definition = '$load_job_template' where definition_type = 'fx.resource.define'";
    $db->exec($sql);

    // STEP 2: drop the definition_type column from tbl_resource
    $sql = "alter table tbl_resource drop column definition_type";
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
