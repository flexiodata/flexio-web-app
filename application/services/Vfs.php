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
    private $process_context_service = null;
    private $store_service = null;

    public function setProcess(\Flexio\IFace\IProcess $process)
    {
        $this->process_context_service = new \Flexio\Services\ProcessContext($process);
    }


    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return 0;
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
            case 'googledrive':
            case 'googlesheets':
            case 'amazons3':
                return true;
        }
    }

    public function list(string $path = '', array $options = []) : array
    {
        $results = [];

        $current_user_eid = \Flexio\System\System::getCurrentUserEid();

        if ($path == '' || $path == '/')
        {
            if (strlen($current_user_eid)==0)
            {
                // no user logged in; return empty array
                return [];
            }

            // load the object
            $user = \Flexio\Object\User::load($current_user_eid);
            if ($user === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            // get the connections
            $connections = $user->getConnectionList();

            // add an entry for home folder (local) storage
            $results[] = array(
                'name' => 'home',
                'path' => '/home',
                'size' => null,
                'modified' => "2017-01-20T10:00:01+0000",
                'type' => 'DIR',
                'connection' => [ 'connection_type' => 'home' ]
            );

            foreach ($connections as $c)
            {
                if ($c->allows($current_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                    continue;

                $info = $c->get();

                if (!self::isStorageConnectionType($info['connection_type'] ?? ''))
                    continue;
                if ($info['connection_status'] != 'A') // only return active connections
                    continue;

                $name = $info['ename'];
                if (strlen($name) == 0)
                    $name = $info['eid'];

                $entry = array(
                    'name' => $name,
                    'path' => '/'.$name,
                    'size' => null,
                    'modified' => $info['updated'],
                    'type' => 'DIR'
                );

                //$entry['.connection_eid'] = $info['eid'];
                //$entry['.connection_type'] = $info['connection_type'];

                $entry['connection'] = \Flexio\Base\Util::filterArray($info, ['eid','ename','name','connection_type']);

                $results[] = $entry;
            }

            return $results;
        }


        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        $results = $service->list($rpath);
        if (is_array($results))
        {
            foreach ($results as &$v)
            {
                $v['path'] = '/' . $connection_identifier . $v['path'];
            }
            unset($v);
        }
        return $results;
    }

    public function getFileInfo(string $path) : array
    {
        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        $service = $this->getService($connection_identifier);

        return $service->getFileInfo($rpath);
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
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

    public function read($path, callable $callback)
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

        return $service->read([ 'path' => $rpath ], $callback);
    }

    public function write($path, callable $callback)
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

        $arr = [ 'path' => $rpath ];
        if (isset($path['size']))
            $arr['size'] = $path['size'];

        return $service->write($arr, $callback);
    }

    public function insert(string $path, array $rows)
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

    public function getServiceFromPath(string $path)
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
                $this->store_service = \Flexio\Services\Store::create();
            $this->service_map[$connection_identifier] = $this->store_service;
            return $this->store_service;
        }

        $current_user_eid = \Flexio\System\System::getCurrentUserEid();

        // load the connection
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the connection
        if ($connection->allows($current_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $connection_info = $connection->get();
        if (!self::isStorageConnectionType($connection_info['connection_type'] ?? ''))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);

        $service = $connection->getService();
        $this->service_map[$connection_identifier] = $service;
        return $service;

            /*
        $connection_info = $connection->get();

        if (!self::isStorageConnectionType($connection_info['connection_type'] ?? ''))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND);

        $service = \Flexio\Services\Factory::create($connection_info);
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        $this->service_map[$connection_identifier] = $service;
        return $service;
        */
    }
}
