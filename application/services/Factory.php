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


class Factory
{
    const TYPE_UNDEFINED     = '';
    const TYPE_FLEXIO        = 'flexio';
    const TYPE_FTP           = 'ftp';
    const TYPE_SFTP          = 'sftp';
    const TYPE_MYSQL         = 'mysql';
    const TYPE_POSTGRES      = 'postgres';
    const TYPE_ELASTICSEARCH = 'elasticsearch';
    const TYPE_DROPBOX       = 'dropbox';
    const TYPE_BOX           = 'box';
    const TYPE_GMAIL         = 'gmail';
    const TYPE_GOOGLEDRIVE   = 'googledrive';
    const TYPE_GOOGLESHEETS  = 'googlesheets';
    const TYPE_GITHUB        = 'github';
    const TYPE_AMAZONS3      = 'amazons3';
    const TYPE_EMAIL         = 'email';
    const TYPE_SMTP          = 'smtp';
    const TYPE_HTTP          = 'http';
    const TYPE_RSS           = 'rss';
    const TYPE_SOCRATA       = 'socrata';
    const TYPE_PIPELINEDEALS = 'pipelinedeals';
    const TYPE_MAILJET       = 'mailjet';
    const TYPE_TWILIO        = 'twilio';


    public static function create(array $connection_properties)
    {
        global $g_store;

        // create a connection hash for storing/retrieving caches of
        // the service
        $connection_hash = self::createConnectionHash($connection_properties);

        // if we have a cached connection, use it
        if ($connection_hash !== false && isset($g_store->connections[$connection_hash]))
            return $g_store->connections[$connection_hash];

        // get the connection type and the corresponding service
        $connection_type = $connection_properties['connection_type'] ?? '';
        $connection_info = $connection_properties['connection_info'] ?? array();

        switch ($connection_type)
        {
            default:
                return false;

            case self::TYPE_HTTP:
                    $auth_params = array(
                        // TODO: add parameters for basic auth?
                    );
                    $service = \Flexio\Services\Http::create($auth_params);
                break;

            case self::TYPE_RSS:
                    $auth_params = array(
                        // TODO: add parameters for basic auth?
                    );
                    $service = \Flexio\Services\Rss::create($auth_params);
                break;

            case self::TYPE_FTP:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? ''
                    );
                    $service = \Flexio\Services\Ftp::create($auth_params);
                break;

            case self::TYPE_SFTP:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? '',
                        'base_path' => $connection_info['base_path'] ?? ''
                    );
                    $service = \Flexio\Services\Sftp::create($auth_params);
                break;

            case self::TYPE_MYSQL:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? '',
                        'database' => $connection_info['database'] ?? ''
                    );
                    $service = \Flexio\Services\MySql::create($auth_params);
                break;

            case self::TYPE_POSTGRES:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? '',
                        'database' => $connection_info['database'] ?? ''
                    );
                    $service = \Flexio\Services\Postgres::create($auth_params);
                break;

            case self::TYPE_ELASTICSEARCH:
                    $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? '',
                        'database' => $connection_info['database'] ?? ''
                    );
                    $service = \Flexio\Services\ElasticSearch::create($auth_params);
                break;

            case self::TYPE_DROPBOX:
                    $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? ''
                    );
                    $service = \Flexio\Services\Dropbox::create($auth_params);
                break;

            case self::TYPE_BOX:
                    $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? '',
                        'refresh_token' => $connection_info['refresh_token'] ?? ''
                    );
                    if (isset($connection_info['expires'])) $auth_params['expires'] = $connection_info['expires'];
                    $service = \Flexio\Services\Box::create($auth_params);
                break;

            case self::TYPE_GOOGLEDRIVE:
                    $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? '',
                        'refresh_token' => $connection_info['refresh_token'] ?? ''
                    );
                    if (isset($connection_info['expires'])) $auth_params['expires'] = $connection_info['expires'];
                    $service = \Flexio\Services\GoogleDrive::create($auth_params);
                break;

            case self::TYPE_GOOGLESHEETS:
                    $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? '',
                        'refresh_token' => $connection_info['refresh_token'] ?? ''
                    );
                    if (isset($connection_info['expires'])) $auth_params['expires'] = $connection_info['expires'];
                    $service = \Flexio\Services\GoogleSheets::create($auth_params);
                break;

            case self::TYPE_GITHUB:
                $auth_params = array(
                    'access_token' => $connection_info['access_token'] ?? '',
                    'refresh_token' => $connection_info['refresh_token'] ?? ''
                );
                if (isset($connection_info['expires'])) $auth_params['expires'] = $connection_info['expires'];
                $service = \Flexio\Services\GitHub::create($auth_params);
                break;

            case self::TYPE_SMTP:
            case self::TYPE_EMAIL:
                $service = \Flexio\Services\Email::create($connection_info);
                break;

            case self::TYPE_AMAZONS3:
                    $auth_params = array(
                        'region' => $connection_info['region'] ?? '',
                        'bucket' => $connection_info['bucket'] ?? '',
                        'accesskey' => $connection_info['aws_key'] ?? '',
                        'secretkey' => $connection_info['aws_secret'] ?? ''
                    );
                    $service = \Flexio\Services\AmazonS3::create($auth_params);
                break;

            case self::TYPE_SOCRATA:
                   $auth_params = array(
                        'host' => $connection_info['host'] ?? '',
                        'port' => $connection_info['port'] ?? '',
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? ''
                    );
                    $service = \Flexio\Services\Socrata::create($auth_params);
                break;

            case self::TYPE_PIPELINEDEALS:
                   $auth_params = array(
                        'access_token' => $connection_info['access_token'] ?? ''
                    );
                    $service = \Flexio\Services\PipelineDeals::create($auth_params);
                break;

            case self::TYPE_MAILJET:
                   $auth_params = array(
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? ''
                    );
                    $service = \Flexio\Services\MailJet::create($auth_params);
                break;

            case self::TYPE_TWILIO:
                    $auth_params = array(
                        'username' => $connection_info['username'] ?? '',
                        'password' => $connection_info['password'] ?? ''
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
