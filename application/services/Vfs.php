<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie LLC. All rights reserved.
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
    private $root_connection_identifier = null;  // if set, this forces the vfs to operate only on one connection type

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

    public function list(string $path = '', array $options = []) : array
    {
        $results = [];

        $connection_identifier = '';
        $rpath = '';
        $service = $this->getServiceFromPath($path, $connection_identifier, $rpath);

        $service_list = $service->list($rpath);

        $results = array();
        if ($service_list === false)
            return $results;

        foreach ($service_list as $entry)
        {
            if ($this->root_connection_identifier === null)
            {
                $full_path = $connection_identifier . ':/' . ltrim($entry['path'],'/');
            }
             else
            {
                $full_path = $entry['path'];
                if (substr($full_path, 0, 1) !== '/')
                    $full_path = '/' . $full_path;
            }

            $item = array(
                'id' => $entry['id'] ?? sha1($full_path),
                'name' => $entry['name'],
                'path' =>  $entry['path'],
                'full_path' => $full_path,
                'size' => $entry['size'],
                'modified' => $entry['modified'],
                'hash' => $entry['hash'] ?? '', // TODO: remove ?? when all services have hash implemented
                'type' => $entry['type']
            );

            if ($this->root_connection_identifier === null)
            {
               // $item['connection'] = $connection_identifier;
            }

            $results[] = $item;
        }

        return $results;
    }

    public function getFileInfo(string $path) : array
    {
        try
        {
            $connection_identifier = '';
            $rpath = '';
            $service = $this->getServiceFromPath($path, $connection_identifier, $rpath);

            return $service->getFileInfo($rpath);
        }
        catch(\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE); // use unavailable rather than 'no-service' so path behavior is consistent
        }
    }

    public function exists(string $path) : bool
    {
        $connection_identifier = '';
        $rpath = '';
        $service = $this->getServiceFromPath($path, $connection_identifier, $rpath);

        return $service->exists($rpath);
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $connection_identifier = '';
        $rpath = '';
        $service = $this->getServiceFromPath($path, $connection_identifier, $rpath);

        return $service->createFile($rpath, $properties);
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $connection_identifier = '';
        $rpath = '';
        $service = $this->getServiceFromPath($path, $connection_identifier, $rpath);

        return $service->createDirectory($rpath, $properties);
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $connection_identifier = '';
        $rpath = '';
        $service = $this->getServiceFromPath($path, $connection_identifier, $rpath);

        return $service->open([ 'path' => $rpath ]);
    }

    public function unlink($path) : bool
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $connection_identifier = '';
        $rpath = '';
        $service = $this->getServiceFromPath($path, $connection_identifier, $rpath);

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $connection_identifier = '';
        $rpath = '';
        $service = $this->getServiceFromPath($path, $connection_identifier, $rpath);

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $arr = $this->splitPath($pathstr);

        $connection_identifier = '';
        $rpath = '';
        $service = $this->getServiceFromPath($pathstr, $connection_identifier, $rpath);

        $arr = [ 'path' => $rpath ];
        if (is_array($path))
        {
            unset($path['path']);
            $arr = array_merge($arr, $path);
        }

        return $service->write($arr, $callback);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function getOwner() : string
    {
        return $this->owner_eid;
    }

    public function setRootConnection($connection_identifier)
    {
        $this->root_connection_identifier = $connection_identifier;
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
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "Invalid parameter 'path'. Only the last part of the path may contain a wildcard");
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

    public function insert(string $path, array $rows) // TODO: add return type
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $connection_identifier = '';
        $rpath = '';
        $service = $this->getServiceFromPath($path, $connection_identifier, $rpath);

        return $service->insert([ 'path' => $rpath ], $rows);
    }

    public function getServiceFromPath(string $path, string &$connection_identifier, string &$rpath) // TODO: add return type
    {
        if ($this->root_connection_identifier !== null)
        {
            $connection_identifier = $this->root_connection_identifier;
            $rpath = $path;
            return $this->getService($connection_identifier);
        }

        $service_identifier_len = strpos($path, '://');
        if ($service_identifier_len !== false)
        {
            $service_identifier_len += 3;

            if (substr($path, 0, 8) == 'context:')
                $rpath_start = 9;
                 else
                $rpath_start = 0; // url remote paths include the protocol and the ://
        }
         else
        {
            $is_url = false;
            $service_identifier_len = strpos($path, ':');
            if ($service_identifier_len !== false)
            {
                $rpath_start = $service_identifier_len+1;
            }
        }

        if ($service_identifier_len !== false)
        {
            $connection_identifier = substr($path, 0, $service_identifier_len);
            $rpath = substr($path, $rpath_start);
        }
         else
        {
            //$connection_identifier = 'flex';
            //$rpath = $path;

            try
            {
                $arr = $this->splitPath($path);
                $connection_identifier = $arr[0];
                $rpath = rtrim(trim($arr[1]), '/');

                return $this->getService($connection_identifier);
            }
            catch (\Exception $e)
            {
                // connection not found -- use flexio storage
                $connection_identifier = 'flex';
                $rpath = $path;
            }
        }

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

        if ($connection_identifier == 'home' || $connection_identifier == 'flex' || $connection_identifier == 'flexio')
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
                    throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE, "Process-local service not found");

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($connection->allows($owner_user_eid, \Flexio\Api\Action::TYPE_CONNECTION_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $connection_info = $connection->get();
        if (!self::isStorageConnectionType($connection_info['connection_type'] ?? ''))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $service = $connection->getService();
        $this->service_map[$connection_identifier] = $service;
        return $service;
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
            case 'googlecloudstorage':
            case 'amazons3':
            case 'elasticsearch':
                return true;
        }
    }
}
