<?php
/*!
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-08-14
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
    // STEP 1: update the 'flex-sample-contacts' and 'flex-zipcode-stats' pipes
    updatePipeTask($db);
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


function updatePipeTask($db)
{
    // STEP 1: get a list of the 'flex-sample-contacts' and 'flex-zipcode-stats' pipes
    $query_sql = "select
                      eid,
                      name
                  from tbl_pipe
                  where
                      name in ('flex-sample-contacts', 'flex-zipcode-stats')
                ";
    $result = $db->query($query_sql);

    // STEP 2: for each of the pipe items, update the task and delete the
    // corresponding index
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $pipe_eid = $row['eid'];
        $pipe_name = $row['name'];

        $pipe_updated = array(
            'task' => getUpdatedTask($pipe_name)
        );

        if (!$pipe_updated)
            continue;

        // update the icon path
        echo ("Updating $pipe_eid: \n");
        echo ("Name: $pipe_name \n");

        $db->update('tbl_pipe', $pipe_updated, 'eid = ' . $db->quote($pipe_eid));

        $elasticsearch = \Flexio\System\System::getSearchCache();
        $elasticsearch->deleteIndex($pipe_eid);
    }
}


function getUpdatedTask(string $name) : ?string
{
    switch ($name)
    {
        default:
            return null;

        case 'flex-sample-contacts':
            $task = <<<EOD
{
    "op": "redirect",
    "path": "https://api.flex.io/v1/integration-getting-started/pipes/flex-sample-contacts/run"
}
EOD;
            return json_encode(json_decode($task),JSON_UNESCAPED_SLASHES);

        case 'flex-zipcode-stats':
            $task = <<<EOD
{
    "op": "redirect",
    "path": "https://api.flex.io/v1/integration-getting-started/pipes/flex-zipcode-stats/run"
}
EOD;
            return json_encode(json_decode($task),JSON_UNESCAPED_SLASHES);
    }
}
