<?php
/*!
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-08
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
    // STEP 1: get a list of pipes
    $sql = 'select eid, input, output, task from tbl_pipe';
    $result = $db->query($sql);


    // STEP 2: for each pipe, get the resource chain, convert it
    // to a task list and save it
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $pipe_eid = $row['eid'];
        $resources = getResourceList($pipe_eid);

        if ($resources === false)
            $resources = array();

        // find out if we already have task info; if we do, don't overwrite it
        $write_task = false;
        $existing_task = @json_decode($row['task'],true);

        if (!is_array($existing_task) || count($existing_task) === 0)
            $write_task = true;

        if ($write_task === true)
        {
            $input = '[]';
            $output = '[]';
            $task = convertToTask($resources);
            writeTask($db, $pipe_eid, $input, $output, $task);
        }

        // regardless of whether or not we saved new task info, remove
        // any associations with resources
        removeResourceAssociations($db, $pipe_eid);
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


function getResourceList($object_eid)
{
    // takes an input object (either a container or a resource) and finds
    // the other resources that are linked to that resource; if we're starting
    // out with a container, the container is not included in the list; if
    // we're starting out with a resources, the initial resource is included
    // in the list; resources are returned in the order of oldest-to-most-recently-
    // added

    // note: pipes formerly used this link list to store the tasks; in the
    // current format, tasks are stored as a single array, so to convert a
    // pipe, we need to read through the linked list and save as a json
    // object in the task field

    $search_path = "$object_eid->(".Model::EDGE_LINKED_FROM.")->(".Model::TYPE_RESOURCE.")";
    $resources = \Flexio\System\System::getModel()->search->recursive_search($search_path);
    $resources = array_reverse($resources);

    if (\Flexio\System\System::getModel()->getType($object_eid) === Model::TYPE_RESOURCE)
        $resources[] = $object_eid;

    return $resources;
}

function writeTask($db, $pipe_eid, $input, $output, $task)
{
    $sql = "update tbl_pipe ".
           "    set ".
           "        input = '$input', ".
           "        output = '$output', ".
           "        task  = '$task' ".
           "    where eid = '$pipe_eid';";

    $db->exec($sql);
}

function convertToTask($resources)
{
    $result = array();

    if (!is_array($resources))
        return json_encode($result);

    foreach ($resources as $r)
    {
        // the the job definition; if we're unable to get a meaningful
        // definition then move on
        if (!isset($r['definition']))
            continue;

        $job_definiton = @json_decode($r['definition'],true);
        if ($job_definition === false)
            continue;

        // if we don't have a recognized job, move on
        if (!isset($job_definition['metadata']) || !isset($job_definition['metadata']['mime_type']))
            continue;

        // get the job type
        $job_mime_type = $job_definition['metadata']['mime_type'];
        switch($job_mime_type)
        {
            // save any job not in the list
            default:
            {
                // if any of the jobs have inputs or outputs, remove these
                // if we don't have a recognized job, move on
                if (isset($job_definition['params']) && isset($job_definition['params']['input']))
                    unset($job_definition['params']['input']);
                if (isset($job_definition['params']) && isset($job_definition['params']['output']))
                    unset($job_definition['params']['output']);

                $result[] = $job_definition;
            }
            break;

            case InputDropboxJob::MIME_TYPE:        'InputDropboxJob';
            case OutputDropboxJob::MIME_TYPE:       'OutputDropboxJob';
            case EmailSendJob::MIME_TYPE:           'EmailSendJob';
            case InputFlexioJob::MIME_TYPE:         'InputFlexioJob';
            case InputFtpJob::MIME_TYPE:            'InputFtpJob';
            case OutputFtpJob::MIME_TYPE:           'OutputFtpJob';
            case InputGoogleDriveJob::MIME_TYPE:    'InputGoogleDriveJob';
            case OutputGoogleDriveJob::MIME_TYPE:   'OutputGoogleDriveJob';
            case HtmlProcessJob::MIME_TYPE:         'HtmlProcessJob';
            case InputHttpJob::MIME_TYPE:           'InputHttpJob';
            case InputJob::MIME_TYPE:               'InputJob';
            case OutputMySqlJob::MIME_TYPE:         'OutputMySqlJob';
            case InputMySqlJob::MIME_TYPE:          'InputMySqlJob';
            case OutputJob::MIME_TYPE:              'OutputJob';
            case InputPipelineDealsJob::MIME_TYPE:  'InputPipelineDealsJob';
            case OutputPostgresJob::MIME_TYPE:      'OutputPostgresJob';
            case InputPostgresJob::MIME_TYPE:       'InputPostgresJob';
            case InputSocrataJob::MIME_TYPE:        'InputSocrataJob';
            case InputTwilioJob::MIME_TYPE:         'InputTwilioJob';
                break;
        }
    }

    // encode the result as an array
    return json_encode($result);
}

function removeResourceAssociations($db, $pipe_eid)
{
    $resources = \Flexio\System\System::getModel()->assoc_range($pipe_eid, Model::EDGE_LINKED_FROM);
    foreach ($resources as $resource_eid)
    {
        \Flexio\System\System::getModel()->assoc_delete($pipe_eid, Model::EDGE_HAS_MEMBER, $resource_eid);
        \Flexio\System\System::getModel()->assoc_delete($resource_eid, Model::EDGE_MEMBER_OF, $pipe_eid);

        \Flexio\System\System::getModel()->assoc_delete($pipe_eid, Model::EDGE_LINKED_FROM, $resource_eid);
        \Flexio\System\System::getModel()->assoc_delete($resource_eid, Model::EDGE_LINKED_TO, $pipe_eid);
    }
}
