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

    public function list(string $path = '') : array
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
                'modified' => null,
                'type' => 'DIR',
                'is_dir' => true,
                'connection' => [ 'connection_type' => 'home' ]
            );

            foreach ($connections as $c)
            {
                if ($c->allows($current_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                    continue;

                $info = $c->get();

                if (!self::isStorageConnectionType($info['connection_type'] ?? ''))
                    continue;

                $name = $info['ename'];
                if (strlen($name) == 0)
                    $name = $info['eid'];

                $entry = array(
                    'name' => $name,
                    'path' => '/'.$name,
                    'size' => null,
                    'modified' => null,
                    'type' => 'DIR',
                    'is_dir' => true
                );

                //$entry['.connection_eid'] = $info['eid'];
                //$entry['.connection_type'] = $info['connection_type'];

                unset($info['connection_info']);
                unset($info['owned_by']);
                unset($info['expires']);
                $entry['connection'] = $info;

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
                $v['type'] = ($v['is_dir'] ? 'DIR' : 'FILE');
                $v['path'] = '/' . $connection_identifier . $v['path'];
            }
            unset($v);
        }
        return $results;
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

        return $service->write([ 'path' => $rpath ], $callback);
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

        if (substr($path, 0, 10) == 'context://')
        {
            // split off the schema portion context://; the path portion will retain the preceding slash
            return [ substr($path, 0, 10), substr($path, 9) ];
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

    private function getService(string $connection_identifier)
    {
        if ($connection_identifier == 'context://')
        {
            if ($this->process_context_service === null)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);
            return $this->process_context_service;
        }

        if ($connection_identifier == 'home')
        {
            if ($this->store_service === null)
                $this->store_service = \Flexio\Services\Store::create();
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

        $service = \Flexio\Services\Factory::create($connection_info);
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }
}
