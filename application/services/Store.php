<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-12
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class Store
{
    public static function load(array $connection_info)
    {
        global $g_store;

        // create a connection hash for storing/retrieving caches of
        // the service
        $connection_hash = self::createConnectionHash($connection_info);

        // if we have a cached connection, use it
        if ($connection_hash !== false && isset($g_store->connections[$connection_hash]))
        {
            $service = $g_store->connections[$connection_hash];
            if ($service->isOk())
                return $service;
        }

        // get the connection type and the corresponding service
        $connection_type = $connection_info['connection_type'] ?? '';
        switch ($connection_type)
        {
            default:
                return false;

            case \Model::CONNECTION_TYPE_HTTP:
                    $auth_params = array(
                        // TODO: add parameters for basic auth?
                    );
                    $service = \Flexio\Services\Http::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_RSS:
                    $auth_params = array(
                        // TODO: add parameters for basic auth?
                    );
                    $service = \Flexio\Services\Rss::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_FTP:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? ''
                    );
                    $service = \Flexio\Services\Ftp::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_SFTP:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? ''
                    );
                    $service = \Flexio\Services\Sftp::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_MYSQL:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? '',
                        'database' => $connection_info['database'] ?? ''
                    );
                    $service = \Flexio\Services\MySql::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_POSTGRES:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? '',
                        'database' => $connection_info['database'] ?? ''
                    );
                    $service = \Flexio\Services\Postgres::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_ELASTICSEARCH:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? '',
                        'database' => $connection_info['database'] ?? ''
                    );
                    $service = \Flexio\Services\ElasticSearch::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_DROPBOX:
                    $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? ''
                    );
                    $service = \Flexio\Services\Dropbox::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_BOX:
                    $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? '',
                        'refresh_token' => $connection_info['refresh_token'] ?? '',
                        'expires' => $connection_info['expires'] ?? 0
                    );
                    $service = \Flexio\Services\Box::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_GOOGLEDRIVE:
                    $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? '',
                        'refresh_token' => $connection_info['refresh_token'] ?? '',
                        'expires' => $connection_info['expires'] ?? 0
                    );
                    $service = \Flexio\Services\GoogleDrive::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_GOOGLESHEETS:
                    $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? '',
                        'refresh_token' => $connection_info['refresh_token'] ?? '',
                        'expires' => $connection_info['expires'] ?? 0
                    );
                    $service = \Flexio\Services\GoogleSheets::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_AMAZONS3:
                    $auth_params = array(
                        'region' => $connection_info['host'] ?? '',
                        'bucket' => $connection_info['database'] ?? '',
                        'accesskey' => $connection_info['username'] ?? '',
                        'secretkey' => $connection_info['password'] ?? ''
                    );
                    $service = \Flexio\Services\AmazonS3::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_SOCRATA:
                   $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? ''
                    );
                    $service = \Flexio\Services\Socrata::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_PIPELINEDEALS:
                   $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? ''
                    );
                    $service = \Flexio\Services\PipelineDeals::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_MAILJET:
                   $auth_params = array(
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? ''
                    );
                    $service = \Flexio\Services\MailJet::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_TWILIO:
                    $auth_params = array(
                        'key' => $connection_info['username'] ?? '',
                        'access_token' => $connection_info['access_token'] ?? ''
                    );
                    $service = \Flexio\Services\Twilio::create($auth_params);
                break;
        }

        if (!$service)
            return false;

        // if we have a connection hash, the cache the connection
        if ($connection_hash !== false)
            $g_store->connections[$connection_hash] = $service;

        return $service;
    }

    private static function createConnectionHash(array $connection_info) : string
    {
        return md5(json_encode($connection_info));
    }
}
