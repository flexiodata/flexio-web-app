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


namespace Flexio\Services;


class Store
{
    public static function load($connection_info)
    {
        global $g_store;

        if (!is_array($connection_info))
            return false;

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
        $connection_type = isset_or($connection_info['connection_type'],'');
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
                    $service = \Flexio\Services\RssService::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_FTP:
                    $auth_params = array(
                        'host' => isset_or($connection_info['host'],''),
                        'port' => isset_or($connection_info['port'],''),
                        'username' => isset_or($connection_info['username'],''),
                        'password' => isset_or($connection_info['password'],'')
                    );
                    $service = \Flexio\Services\FtpService::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_SFTP:
                    $auth_params = array(
                        'host' => isset_or($connection_info['host'],''),
                        'port' => isset_or($connection_info['port'],''),
                        'username' => isset_or($connection_info['username'],''),
                        'password' => isset_or($connection_info['password'],'')
                    );
                    $service = \Flexio\Services\SftpService::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_MYSQL:
                    $auth_params = array(
                        'host' => isset_or($connection_info['host'],''),
                        'port' => isset_or($connection_info['port'],''),
                        'username' => isset_or($connection_info['username'],''),
                        'password' => isset_or($connection_info['password'],''),
                        'database' => isset_or($connection_info['database'],'')
                    );
                    $service = \Flexio\Services\MysqlService::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_POSTGRES:
                    $auth_params = array(
                        'host' => isset_or($connection_info['host'],''),
                        'port' => isset_or($connection_info['port'],''),
                        'username' => isset_or($connection_info['username'],''),
                        'password' => isset_or($connection_info['password'],''),
                        'database' => isset_or($connection_info['database'],'')
                    );
                    $service = \Flexio\Services\PostgresService::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_DROPBOX:
                    $auth_params = array(
                        'access_token' => isset_or($connection_info['token'],'')
                    );
                    $service = \Flexio\Services\Dropbox::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_GOOGLEDRIVE:
                    $auth_params = array(
                        'access_token' => isset_or($connection_info['token'],''),
                        'refresh_token' => isset_or($connection_info['refresh_token'],''),
                        'expires' => isset_or($connection_info['token_expires'],0)
                    );
                    $service = \Flexio\Services\GoogleDrive::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_GOOGLESHEETS:
                    $auth_params = array(
                        'access_token' => isset_or($connection_info['token'],''),
                        'refresh_token' => isset_or($connection_info['refresh_token'],''),
                        'expires' => isset_or($connection_info['token_expires'],0)
                    );
                    $service = \Flexio\Services\GoogleSheets::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_AMAZONS3:
                    $auth_params = array(
                        'region' => isset_or($connection_info['host'],''),
                        'bucket' => isset_or($connection_info['database'],''),
                        'accesskey' => isset_or($connection_info['username'],''),
                        'secretkey' => isset_or($connection_info['password'],'')
                    );
                    $service = \Flexio\Services\AmazonS3::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_SOCRATA:
                   $auth_params = array(
                        'host' => isset_or($connection_info['host'],''),
                        'port' => isset_or($connection_info['port'],''),
                        'username' => isset_or($connection_info['username'],''),
                        'password' => isset_or($connection_info['password'],'')
                    );
                    $service = \Flexio\Services\SocrataService::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_PIPELINEDEALS:
                   $auth_params = array(
                        'token' => isset_or($connection_info['token'],'')
                    );
                    $service = \Flexio\Services\PipelineDealsService::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_MAILJET:
                   $auth_params = array(
                        'username' => isset_or($connection_info['username'],''),
                        'password' => isset_or($connection_info['password'],'')
                    );
                    $service = \Flexio\Services\MailJetService::create($auth_params);
                break;

            case \Model::CONNECTION_TYPE_TWILIO:
                    $auth_params = array(
                        'key' => isset_or($connection_info['username'],''),
                        'token' => isset_or($connection_info['token'],'')
                    );
                    $service = \Flexio\Services\TwilioService::create($auth_params);
                break;
        }

        if (!$service)
            return false;

        // if we have a connection hash, the cache the connection
        if ($connection_hash !== false)
            $g_store->connections[$connection_hash] = $service;

        return $service;
    }

    private static function createConnectionHash($connection_info)
    {
        return md5(json_encode($connection_info));
    }
}
