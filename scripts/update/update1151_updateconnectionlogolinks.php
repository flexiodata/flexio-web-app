<?php
/*!
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-07-14
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
                      icon,
                      setup_template
                  from tbl_connection
                  where
                      connection_mode = 'F'
                ";
    $result = $db->query($query_sql);

    // STEP 2: for each of the connection items, update the icon path
    $objects = array();
    while ($result && ($row = $result->fetch()))
    {
        $eid = $row['eid'];

        // get the updated icon
        $icon_original = $row['icon'];
        $icon_updated = getUpdatedLogoUrl($icon_original);

        // get the updated template image src
        $setup_template_original = $row['setup_template'];
        $setup_template_original = \Flexio\Base\Util::decrypt($setup_template_original, $GLOBALS['g_store']->connection_enckey);
        $setup_template_original = @json_decode($setup_template_original, true);

        $setup_template_image_src_original = false;
        $setup_template_image_src_updated = false;
        if (isset($setup_template_original['image']['src']))
        {
            $setup_template_image_src_original = $setup_template_original['image']['src'];
            if (is_string($setup_template_image_src_original))
                $setup_template_image_src_updated = getUpdatedLogoUrl($setup_template_image_src_original);
        }

        $connection_updated = array();

        if ($icon_original !== $icon_updated)
            $connection_updated['icon'] = $icon_updated;

        if ($setup_template_image_src_original !== $setup_template_image_src_updated)
        {
            $setup_template_updated = $setup_template_original;
            $setup_template_updated['image']['src'] = $setup_template_image_src_updated;
            $setup_template_updated = json_encode($setup_template_updated);
            $setup_template_updated = \Flexio\Base\Util::encrypt($setup_template_updated, $GLOBALS['g_store']->connection_enckey);
            $connection_updated['setup_template'] = $setup_template_updated;
        }

        if (count($connection_updated) === 0)
            continue;

        // update the icon path
        echo ("Updating $eid: \n");
        echo ("Old icon url: $icon_original \n");
        echo ("New icon url: $icon_updated \n");
        echo ("Old setup template url: $setup_template_image_src_original \n");
        echo ("New setup template url: $setup_template_image_src_updated \n");

        $db->update('tbl_connection', $connection_updated, 'eid = ' . $db->quote($eid));
    }
}

function getUpdatedLogoUrl(string $url) : ?string
{
    switch ($url)
    {
        default:
            return $url;

        // big commerce
        case 'https://logo.clearbit.com/bigcommerce.com':
        case 'https://static.flex.io/assets/logos2/bigcommerce.png':
            return 'https://static.flex.io/assets/logos/bigcommerce.png';

        // capsule crm
        case 'https://static.flex.io/assets/logos/icon-capsulecrm-128-df125ea.png':
        case 'https://static.flex.io/assets/logos2/capsule-crm.png':
            return 'https://static.flex.io/assets/logos/capsule-crm.png';

        // covid-19
        case 'https://static.flex.io/assets/logos2/covid-19.png':
            return 'https://static.flex.io/assets/logos/covid-19.png';

        // crunchbase
        case 'https://logo.clearbit.com/crunchbase.com':
        case 'https://static.flex.io/assets/logos2/crunchbase.png':
            return 'https://static.flex.io/assets/logos/crunchbase.png';

        // currency
        case 'https://logo.clearbit.com/zentral-bank.eu':
        case 'https://static.flex.io/assets/logos2/currency.png':
            return 'https://static.flex.io/assets/logos/currency.png';

        // flexio
        case 'https://logo.clearbit.com/flex.io':
        case 'https://static.flex.io/assets/logos2/flexio.png':
            return 'https://static.flex.io/assets/logos/flexio.png';

        // full contact
        case 'https://logo.clearbit.com/fullcontact.com':
        case 'https://static.flex.io/assets/logos2/fullcontact.png':
            return 'https://static.flex.io/assets/logos/fullcontact.png';

        // github
        case 'https://logo.clearbit.com/github.com':
        case 'https://static.flex.io/assets/logos2/github.png':
            return 'https://static.flex.io/assets/logos/github.png';

        // hackernews
        case 'https://logo.clearbit.com/ycombinator.com':
        case 'https://static.flex.io/assets/logos2/hacker-news.png':
            return 'https://static.flex.io/assets/logos/hacker-news.png';

        // hubspot
        case 'https://logo.clearbit.com/hubspot.com':
        case 'https://static.flex.io/assets/logos2/hubspot.png':
            return 'https://static.flex.io/assets/logos/hubspot.png';

        // hunter
        case 'https://logo.clearbit.com/hunter.io':
        case 'https://static.flex.io/assets/logos2/hunter.png':
            return 'https://static.flex.io/assets/logos/hunter.png';

        // intercom
        case 'https://logo.clearbit.com/intercom.com':
        case 'https://static.flex.io/assets/logos2/intercom.png':
            return 'https://static.flex.io/assets/logos/intercom.png';

        // pipedrive
        case 'https://logo.clearbit.com/pipedrive.com':
        case 'https://static.flex.io/assets/logos2/pipedrive.png':
            return 'https://static.flex.io/assets/logos/pipedrive.png';

        // pipl
        case 'https://static.flex.io/assets/logos2/pipl.png':
            return 'https://static.flex.io/assets/logos/pipl.png';

        // producthunt
        case 'https://logo.clearbit.com/producthunt.com':
        case 'https://static.flex.io/assets/logos2/product-hunt.png':
            return 'https://static.flex.io/assets/logos/product-hunt.png';

        // quandl
        case 'https://logo.clearbit.com/quandl.com':
        case 'https://static.flex.io/assets/logos2/quandl.png':
            return 'https://static.flex.io/assets/logos/quandl.png';

        // sample data
        case 'https://static.flex.io/assets/logos/icon-sampledata-128-283729f.png':
        case 'https://static.flex.io/assets/logos2/sample-data.png':
            return 'https://static.flex.io/assets/logos/sample-data.png';

        // shopfiy
        case 'https://logo.clearbit.com/shopify.com':
        case 'https://static.flex.io/assets/logos2/shopify.png':
            return 'https://static.flex.io/assets/logos/shopify.png';

        // web
        case 'https://static.flex.io/assets/logos/icon-web-128-e0d8c37.png':
        case 'https://static.flex.io/assets/logos2/web.png':
            return 'https://static.flex.io/assets/logos/web.png';

        // wikipedia
        case 'https://logo.clearbit.com/en.wikipedia.org':
        case 'https://logo.clearbit.com/wikipedia.org':
        case 'https://static.flex.io/assets/logos2/wikipedia.png':
            return 'https://static.flex.io/assets/logos/wikipedia.png';
    }
}
