<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-10-04
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class Vfs // TODO: implements \Flexio\IFace\IFileSystem
{
    private $owner_eid = ''; // the user to use when evaluating vfs paths
    private $process_context_service = null;
    private $store_service = null;
    private $process = null;

    public function __construct(string $owner_eid)
    {
        $this->owner_eid = $owner_eid;
    }

    public function setProcess(\Flexio\IFace\IProcess $process) : void
    {
        $this->process = $process;
        $this->process_context_service = new \Flexio\Services\ProcessContext($process);
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return 0;
    }

    public function getOwner() : string
    {
        return $this->owner_eid;
    }

    private function isStorageConnectionType(string $type) : bool
    {
        switch ($type)
        {
            default:
                return false;
            case 'ftp':
            case 'sftp':
            case 'mysql':
            case 'postgres':
            case 'dropbox':
            case 'box':
            case 'github':
            case 'googledrive':
            case 'googlesheets':
            case 'amazons3':
            case 'elasticsearch':
            case 'twilio':
                return true;
        }
    }

    public function list(string $path = '', array $options = []) : array
    {
        $results = [];

        $owner_user_eid = $this->getOwner();

        if ($path == '' || $path == '/')
        {
            if (strlen($owner_user_eid)==0)
            {
                // no user logged in; return empty array
                return [];
            }

            // load the object
            $user = \Flexio\Object\User::load($owner_user_eid);
            if ($user->getStatus() === \Model::STATUS_DELETED)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            // get the connections
            $filter = array('owned_by' => $user->getEid(), 'eid_status' => \Model::STATUS_AVAILABLE);
            $connections = \Flexio\Object\Connection::list($filter);

            // add an entry for home folder (local) storage
            $results[] = array(
                'name' => 'home',
                'path' => '/home',
                'remote_path' => '/',
                'size' => null,
                'modified' => "2017-01-20T10:00:01+0000",
                'type' => 'DIR',
                'connection' => [ 'connection_type' => 'home' ]
            );

            foreach ($connections as $c)
            {
                if ($c->allows($owner_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                    continue;

                $info = $c->get();

                if (!self::isStorageConnectionType($info['connection_type'] ?? ''))
                    continue;
                if ($info['connection_status'] != 'A') // only return active connections
                    continue;

                $name = $info['alias'];
                if (strlen($name) == 0)
                    $name = $info['eid'];

                $entry = array(
                    'name' => $name,
                    'path' => '/'.$name,
                    'remote_path' => '/',
                    'size' => null,
                    'modified' => $info['updated'],
                    'type' => 'DIR'
                );

                //$entry['.connection_eid'] = $info['eid'];
                //$entry['.connection_type'] = $info['connection_type'];

                $entry['connection'] = \Flexio\Base\Util::filterArray($info, ['eid','alias','name','connection_type']);

                $results[] = $entry;
            }

            return $results;
        }


        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);
        $service_list = $service->list($rpath);

        $results = array();
        if ($service_list === false)
            return $results;

        foreach ($service_list as $entry)
        {
            $full_path = '/' . $connection_identifier;
            if (strlen($full_path) == 0 || substr($full_path, -1) != '/')
                $full_path .= '/';
            $full_path .= $entry['path'];

            $results[] = array(
                'name' => $entry['name'],
                'path' =>  $full_path,
                'remote_path' =>  $entry['path'],
                'size' => $entry['size'],
                'modified' => $entry['modified'],
                'type' => $entry['type']
            );
        }

        return $results;
/*
            foreach ($results as &$v)
            {
                $remote_path = $v['path'];
                $v['path'] = '/' . $connection_identifier . $remote_path;
                $v['remote_path'] = $remote_path;
            }
            unset($v);
        }
        return $results;
*/
    }


    public function listWithWildcard(string $path = '', array $options = []) : array
    {
        $parts = \Flexio\Base\File::splitPath($path);
        $lastpart = array_pop($parts);

        foreach ($parts as $part)
        {
            if (strpos($part, '*') !== false)
            {
                // only the last part of the path may contain a wildcard
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "Invalid parameter 'path'. Only the last part of the path may contain a wildcard");
            }
        }

        $wildcard = null;
        if ($lastpart !== null)
        {
            if (strpos($lastpart, '*') !== false)
                $wildcard = $lastpart;
                else
                $parts[] = $lastpart;
        }

        $path = '/' . implode('/', $parts);

        $files = $this->list($path);

        $results = [];
        foreach ($files as $f)
        {
            if ($wildcard !== null)
            {
                if (!\Flexio\Base\File::matchPath($f['name'], $wildcard, false))
                    continue;
            }

            $results[] = $f;
        }

        return $results;
    }

    public function getFileInfo(string $path) : array
    {
        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        try
        {
            $service = $this->getService($connection_identifier);
        }
        catch(\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);
        }

        return $service->getFileInfo($rpath);
    }

    public function exists(string $path) : bool
    {
        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        return $service->exists($rpath);
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        return $service->createFile($rpath, $properties);
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        return $service->createDirectory($rpath, $properties);
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        return $service->open([ 'path' => $rpath ]);
    }

    public function unlink($path) : bool
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        return $service->unlink($rpath);
    }

    public function read($path, callable $callback) // TODO: add return type
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        if (strlen(trim($path)) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);

        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        return $service->read([ 'path' => $rpath ], $callback);
    }

    public function write($path, callable $callback) // TODO: add return type
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        $pathstr = $path;
        if (is_array($path))
        {
            $pathstr = $path['path'] ?? '';
        }

        if (strlen(trim($pathstr)) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);


        $arr = $this->splitPath($pathstr);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        $arr = [ 'path' => $rpath ];
        if (is_array($path))
        {
            unset($path['path']);
            $arr = array_merge($arr, $path);
        }

        return $service->write($arr, $callback);
    }

    public function insert(string $path, array $rows) // TODO: add return type
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        return $service->insert([ 'path' => $rpath ], $rows);
    }

    public function splitPath(string $path) : array
    {
        $path = trim($path);
        if (strlen($path) == 0)
            return [];

        $urlsep_pos = strpos($path, '://');
        if ($urlsep_pos !== false)
        {
            $protocol = substr($path, 0, $urlsep_pos);
            if ($protocol == 'context')
            {
                // split off the schema portion context://; the path portion will retain the preceding slash
                return [ substr($path, 0, $urlsep_pos+3), substr($path, 9) ];
            }
            else if ($protocol == 'https' || $protocol == 'http' || $protocol == 's3')
            {
                return [ substr($path, 0, $urlsep_pos+3), $path ];
            }

        }


        $off = ($path[0] == '/' ? 1:0);

        $pos = strpos($path, '/', $off);
        if ($pos === false)
        {
            return [ substr($path, $off), '/' ];
        }
         else
        {
            return [ substr($path, $off, $pos-$off), substr($path, $pos) ];
        }
    }

    public function getServiceFromPath(string $path) // TODO: add return type
    {
        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        return $this->getService($connection_identifier);
    }

    private $service_map = [];
    private function getService(string $connection_identifier)
    {
        $conn = $this->service_map[$connection_identifier] ?? null;
        if (isset($conn))
            return $conn;

        if ($connection_identifier == 'context://')
        {
            if ($this->process_context_service === null)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);
            $this->service_map[$connection_identifier] = $this->process_context_service;
            return $this->process_context_service;
        }

        if ($connection_identifier == 'http://' || $connection_identifier == 'https://')
        {
            $http_service = \Flexio\Services\Http::create();
            $this->service_map[$connection_identifier] = $http_service;
            return $http_service;
        }

        if ($connection_identifier == 's3://')
        {
            $s3_service = \Flexio\Services\AmazonS3::create();
            $this->service_map[$connection_identifier] = $s3_service;
            return $s3_service;
        }

        if ($connection_identifier == 'home')
        {
            if ($this->store_service === null)
            {
                $params = array('owned_by' => $this->getOwner());
                $this->store_service = \Flexio\Services\Store::create($params);
            }
            $this->service_map[$connection_identifier] = $this->store_service;
            return $this->store_service;
        }


        if ($this->process)
        {
            // first, check the process's local connections for a hit
            $connection_properties = $this->process->getLocalConnection($connection_identifier);
            if ($connection_properties)
            {
                $service = \Flexio\Services\Factory::create($connection_properties);
                if (!$service)
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND, "Process-local service not found");

                $this->service_map[$connection_identifier] = $service;
                return $service;
            }
        }


        $owner_user_eid = $this->getOwner();

        // load the connection
        if (\Flexio\Base\Eid::isValid($connection_identifier) === false)
        {
            $eid_from_identifier = \Flexio\Object\Connection::getEidFromName($owner_user_eid, $connection_identifier);
            $connection_identifier = $eid_from_identifier !== false ? $eid_from_identifier : '';
        }

        $connection = \Flexio\Object\Connection::load($connection_identifier);

        // check the rights on the connection
        if ($connection->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        if ($connection->allows($owner_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $connection_info = $connection->get();
        if (!self::isStorageConnectionType($connection_info['connection_type'] ?? ''))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);

        $service = $connection->getService();
        $this->service_map[$connection_identifier] = $service;
        return $service;
    }
}
