<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
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
    public const TYPE_UNDEFINED           = '';
    public const TYPE_FLEXIO              = 'flexio';
    public const TYPE_FTP                 = 'ftp';
    public const TYPE_SFTP                = 'sftp';
    public const TYPE_MYSQL               = 'mysql';
    public const TYPE_POSTGRES            = 'postgres';
    public const TYPE_ELASTICSEARCH       = 'elasticsearch';
    public const TYPE_DROPBOX             = 'dropbox';
    public const TYPE_BOX                 = 'box';
    public const TYPE_GMAIL               = 'gmail';
    public const TYPE_GOOGLEDRIVE         = 'googledrive';
    public const TYPE_GOOGLESHEETS        = 'googlesheets';
    public const TYPE_GOOGLECLOUDSTORAGE  = 'googlecloudstorage';
    public const TYPE_GITHUB              = 'github';
    public const TYPE_AMAZONS3            = 'amazons3';
    public const TYPE_EMAIL               = 'email';
    public const TYPE_SMTP                = 'smtp';
    public const TYPE_HTTP                = 'http';
    public const TYPE_SOCRATA             = 'socrata';
    public const TYPE_LINKEDIN            = 'linkedin';
    public const TYPE_KEYRING             = 'keyring'; // general purpose set of key values; here as a place holder constant to match UI


    public static function create(array $connection_properties) // TODO: add return type
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
        $service = self::createService($connection_type, $connection_info);

        // if we have a connection hash, the cache the connection
        if ($connection_hash !== false)
            $g_store->connections[$connection_hash] = $service;

        return $service;
    }

    private static function createService(string $connection_type, array $connection_info) // TODO: add return type
    {
        switch ($connection_type)
        {
            default:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            case self::TYPE_HTTP:
                return \Flexio\Services\Http::create($connection_info);

            case self::TYPE_FTP:
                return \Flexio\Services\Ftp::create($connection_info);

            case self::TYPE_SFTP:
                return \Flexio\Services\Sftp::create($connection_info);

            case self::TYPE_MYSQL:
                return \Flexio\Services\MySql::create($connection_info);

            case self::TYPE_POSTGRES:
                return \Flexio\Services\Postgres::create($connection_info);

            case self::TYPE_ELASTICSEARCH:
                return \Flexio\Services\ElasticSearch::create($connection_info);

            case self::TYPE_DROPBOX:
                return \Flexio\Services\Dropbox::create($connection_info);

            case self::TYPE_BOX:
                return \Flexio\Services\Box::create($connection_info);

            case self::TYPE_GMAIL:
                return \Flexio\Services\Gmail::create($connection_info);

            case self::TYPE_GOOGLEDRIVE:
                return \Flexio\Services\GoogleDrive::create($connection_info);

            case self::TYPE_GOOGLESHEETS:
                return \Flexio\Services\GoogleSheets::create($connection_info);

            case self::TYPE_GOOGLECLOUDSTORAGE:
                return \Flexio\Services\GoogleCloudStorage::create($connection_info);

            case self::TYPE_GITHUB:
                return \Flexio\Services\GitHub::create($connection_info);

            case self::TYPE_LINKEDIN:
                return \Flexio\Services\LinkedIn::create($connection_info);

            case self::TYPE_SMTP:
            case self::TYPE_EMAIL:
                return \Flexio\Services\Email::create($connection_info);

            case self::TYPE_AMAZONS3:
                return \Flexio\Services\AmazonS3::create($connection_info);

            case self::TYPE_SOCRATA:
                return \Flexio\Services\Socrata::create($connection_info);
        }
    }

    private static function createConnectionHash(array $connection_info) : string
    {
        return md5(json_encode($connection_info));
    }
}
