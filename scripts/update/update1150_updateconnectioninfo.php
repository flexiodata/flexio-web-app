<?php
/*!
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-06-16
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
    // STEP 1: update the url of the connection info for http connections
    updateConnectionInfo($db);
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


function updateConnectionInfo($db)
{
    // STEP 1: get a list of the http connection integrations/mounts
    $query_sql = "select
                      eid,
                      connection_info
                  from tbl_connection
                  where
                      connection_mode = 'F' and
                      connection_type = 'http'
                ";
    $result = $db->query($query_sql);

    // STEP 2: for each of the connection items, update the connection info url if appropraite
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];

        // get the the connection info url
        $connection_info = $row['connection_info'];
        $connection_info = \Flexio\Base\Util::decrypt($connection_info, $GLOBALS['g_store']->connection_enckey);
        $connection_info = @json_decode($connection_info, true);

        if (!isset($connection_info['url']))
            continue;

        $connection_info_url = $connection_info['url'];
        if (!is_string($connection_info_url))
            continue;

        // get the update connection info url
        $connection_info_url_updated = getUpdatedConnectionUrl($connection_info_url);
        if (!isset($connection_info_url_updated))
            continue;

        echo ("Updating $eid: \n");
        echo ("Old url: $connection_info_url \n");
        echo ("New url: $connection_info_url_updated \n");

        // update the connection info
        $connection_info_updated = array();
        $connection_info_updated['url'] = $connection_info_url_updated;
        $connection_info_updated = json_encode($connection_info_updated);
        $connection_info_updated = \Flexio\Base\Util::encrypt($connection_info_updated, $GLOBALS['g_store']->connection_enckey);

        $update = array();
        $update['connection_info'] = $connection_info_updated;
        $db->update('tbl_connection', $update, 'eid = ' . $db->quote($eid));
    }
}

function getUpdatedConnectionUrl(string $url) : ?string
{
    switch ($url)
    {
        default:
            return null;

        case 'https://static.flex.io/integrations/functions-bigcommerce/flexio.yml':
            return 'https://static.flex.io/integrations/bigcommerce/functions-bigcommerce/flexio.yml';

        case 'https://static.flex.io/integrations/functions-capsule/flexio.yml':
            return 'https://static.flex.io/integrations/capsule/functions-capsule/flexio.yml';

        case 'https://static.flex.io/integrations/functions-covid-19/flexio.yml':
            return 'https://static.flex.io/integrations/covid-19/functions-covid-19/flexio.yml';

        case 'https://static.flex.io/integrations/functions-crunchbase/flexio.yml':
            return 'https://static.flex.io/integrations/crunchbase/functions-crunchbase/flexio.yml';

        case 'https://static.flex.io/integrations/functions-currency/flexio.yml':
            return 'https://static.flex.io/integrations/currency/functions-currency/flexio.yml';

        case 'https://static.flex.io/integrations/functions-database/flexio.yml':
            return 'https://static.flex.io/integrations/database/functions-database/flexio.yml';

        case 'https://static.flex.io/integrations/functions-docusign/flexio.yml':
            return 'https://static.flex.io/integrations/docusign/functions-docusign/flexio.yml';

        case 'https://static.flex.io/integrations/functions-fullcontact/flexio.yml':
            return 'https://static.flex.io/integrations/fullcontact/functions-fullcontact/flexio.yml';

        case 'https://static.flex.io/integrations/functions-github/flexio.yml':
            return 'https://static.flex.io/integrations/github/functions-github/flexio.yml';

        case 'https://static.flex.io/integrations/functions-hackernews/flexio.yml':
            return 'https://static.flex.io/integrations/hackernews/functions-hackernews/flexio.yml';

        case 'https://static.flex.io/integrations/functions-hubspot/flexio.yml':
            return 'https://static.flex.io/integrations/hubspot/functions-hubspot/flexio.yml';

        case 'https://static.flex.io/integrations/functions-hunter/flexio.yml':
            return 'https://static.flex.io/integrations/hunter/functions-hunter/flexio.yml';

        case 'https://static.flex.io/integrations/functions-intercom/flexio.yml':
            return 'https://static.flex.io/integrations/intercom/functions-intercom/flexio.yml';

        case 'https://static.flex.io/integrations/functions-moz/flexio.yml':
            return 'https://static.flex.io/integrations/moz/functions-moz/flexio.yml';

        case 'https://static.flex.io/integrations/functions-pipedrive/flexio.yml':
            return 'https://static.flex.io/integrations/pipedrive/functions-pipedrive/flexio.yml';

        case 'https://static.flex.io/integrations/functions-producthunt/flexio.yml':
            return 'https://static.flex.io/integrations/producthunt/functions-producthunt/flexio.yml';

        case 'https://static.flex.io/integrations/functions-quandl/flexio.yml':
            return 'https://static.flex.io/integrations/quandl/functions-quandl/flexio.yml';

        case 'https://static.flex.io/integrations/functions-sampledata/flexio.yml':
            return 'https://static.flex.io/integrations/sampledata/functions-sampledata/flexio.yml';

        case 'https://static.flex.io/integrations/functions-shopify/flexio.yml':
            return 'https://static.flex.io/integrations/shopify/functions-shopify/flexio.yml';

        case 'https://static.flex.io/integrations/functions-twitter/flexio.yml':
            return 'https://static.flex.io/integrations/twitter/functions-twitter/flexio.yml';

        case 'https://static.flex.io/integrations/functions-web/flexio.yml':
            return 'https://static.flex.io/integrations/web/functions-web/flexio.yml';

        case 'https://static.flex.io/integrations/functions-word/flexio.yml':
            return 'https://static.flex.io/integrations/word/functions-word/flexio.yml';

        case 'https://static.flex.io/integrations/functions-wikipedia/flexio.yml':
            return 'https://static.flex.io/integrations/wikipedia/functions-wikipedia/flexio.yml';
    }
}
