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


// TODO: require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Vfs // TODO: implements \Flexio\Services\IFileSystem
{
    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

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
            $filter = array('eid_type' => array(\Model::TYPE_CONNECTION), 'eid_status' => array(\Model::STATUS_AVAILABLE));
            $connections = $user->getObjects($filter);

            // add an entry for local storage
            $results[] = array(
                'name' => 'local',
                'path' => '/local',
                'size' => null,
                'modified' => null,
                'type' => 'DIR',
                'is_dir' => true,
                '.connection_type' => 'local'
            );

            foreach ($connections as $c)
            {
                if ($c->allows($current_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                    continue;

                $info = $c->get();
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

                $entry['.connection_eid'] = $info['eid'];
                $entry['.connection_type'] = $info['connection_type'];
                $results[] = $entry;
            }

            return $results;
        }


        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');


        if ($connection_identifier == 'local')
        {
            return $this->listLocal();
        }



        // load the connection
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the connection
        if ($connection->allows($current_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $connection_info = $connection->get();

        $service = \Flexio\Services\Factory::create($connection_info);
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

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

    public function listLocal() : array
    {
        $current_user_eid = \Flexio\System\System::getCurrentUserEid();

        // load the object
        $user = \Flexio\Object\User::load($current_user_eid);
        if ($user === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // get the pipes
        $filter = array('eid_type' => array(\Model::TYPE_STREAM), 'eid_status' => array(\Model::STATUS_AVAILABLE));
        $streams = $user->getObjects($filter);

        $result = array();
        foreach ($streams as $s)
        {
            if ($s->allows($current_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
                continue;

            $result[] = $s->get();
        }

        return $result;
    }

    public function read($path, callable $callback)
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $current_user_eid = \Flexio\System\System::getCurrentUserEid();

        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        // load the connection
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the connection
        if ($connection->allows($current_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $connection_info = $connection->get();
        $service = \Flexio\Services\Factory::create($connection_info);
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service->read([ 'path' => $rpath ], $callback);
    }

    public function write($path, callable $callback)
    {
        // path can either be an array [ 'path' => value ] or a string containing the path
        if (is_array($path))
        {
            $path = $path['path'] ?? '';
        }

        $current_user_eid = \Flexio\System\System::getCurrentUserEid();

        $arr = $this->splitPath($path);
        $connection_identifier = $arr[0];
        $rpath = rtrim(trim($arr[1]), '/');

        // load the connection
        $connection = \Flexio\Object\Connection::load($connection_identifier);
        if ($connection === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // check the rights on the connection
        if ($connection->allows($current_user_eid, \Flexio\Object\Right::TYPE_WRITE) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $connection_info = $connection->get();
        $service = \Flexio\Services\Factory::create($connection_info);
        if ($service === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service->write([ 'path' => $rpath ], $callback);
    }

    public function splitPath(string $path) : array
    {
        $path = trim($path);
        if (strlen($path) == 0)
            return [];

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
}
