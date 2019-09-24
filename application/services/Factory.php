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
    private static $manifest = array(
        \Model::CONNECTION_TYPE_FTP                 => '\Flexio\Services\Ftp',
        \Model::CONNECTION_TYPE_SFTP                => '\Flexio\Services\Sftp',
        \Model::CONNECTION_TYPE_MYSQL               => '\Flexio\Services\MySql',
        \Model::CONNECTION_TYPE_POSTGRES            => '\Flexio\Services\Postgres',
        \Model::CONNECTION_TYPE_ELASTICSEARCH       => '\Flexio\Services\ElasticSearch',
        \Model::CONNECTION_TYPE_DROPBOX             => '\Flexio\Services\Dropbox',
        \Model::CONNECTION_TYPE_BOX                 => '\Flexio\Services\Box',
        \Model::CONNECTION_TYPE_GMAIL               => '\Flexio\Services\Gmail',
        \Model::CONNECTION_TYPE_GOOGLEDRIVE         => '\Flexio\Services\GoogleDrive',
        \Model::CONNECTION_TYPE_GOOGLESHEETS        => '\Flexio\Services\GoogleSheets',
        \Model::CONNECTION_TYPE_GOOGLECLOUDSTORAGE  => '\Flexio\Services\GoogleCloudStorage',
        \Model::CONNECTION_TYPE_GITHUB              => '\Flexio\Services\GitHub',
        \Model::CONNECTION_TYPE_AMAZONS3            => '\Flexio\Services\AmazonS3',
        \Model::CONNECTION_TYPE_EMAIL               => '\Flexio\Services\Email',
        \Model::CONNECTION_TYPE_SMTP                => '\Flexio\Services\Email', // use email for SMTP
        \Model::CONNECTION_TYPE_HTTP                => '\Flexio\Services\Http',
        \Model::CONNECTION_TYPE_SOCRATA             => '\Flexio\Services\Socrata',
        \Model::CONNECTION_TYPE_LINKEDIN            => '\Flexio\Services\LinkedIn',
        \Model::CONNECTION_TYPE_TWITTER             => '\Flexio\Services\Twitter'
    );

    public static function create(array $connection_properties) // TODO: add return type
    {
        global $g_store;

        // create a connection hash for storing/retrieving caches of the service
        $connection_hash = self::createConnectionHash($connection_properties);

        // if we have a cached connection, use it
        if ($connection_hash !== false && isset($g_store->connections[$connection_hash]))
            return $g_store->connections[$connection_hash];

        // get the connection type and info
        $connection_type = $connection_properties['connection_type'] ?? '';
        $connection_info = $connection_properties['connection_info'] ?? array();

        // try to load a corresponding service for the connection
        $service_class_name = self::$manifest[$connection_type] ?? false;
        if ($service_class_name === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE, _('Service doesn\'t exist for connection type'));

        $class_name_parts = explode("\\", $service_class_name);
        if (!isset($class_name_parts[3]))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        $service_class_file = \Flexio\System\System::getApplicationDirectory() . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR . $class_name_parts[3] . '.php';
        if (!@file_exists($service_class_file))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        // load the job's php file and instantiate the job object
        include_once $service_class_file;
        $service = $service_class_name::create($connection_info);

        // if we have a connection hash, then cache the connection
        if ($connection_hash !== false)
            $g_store->connections[$connection_hash] = $service;

        return $service;
    }

    private static function createConnectionHash(array $connection_info) : string
    {
        return md5(json_encode($connection_info));
    }
}
