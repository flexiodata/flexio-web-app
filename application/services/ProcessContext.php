<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-06
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class ProcessContext implements \Flexio\IFace\IFileSystem
{
    private $process = null;

    public function __construct(\Flexio\IFace\IProcess $process)
    {
        $this->process = $process;
    }

    public static function create(array $params = null) : \Flexio\Services\ProcessContext
    {
        $service = new self($params['process']);
        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function list(string $path = '') : array
    {
        if (!$this->isOk())
            return array();

        $folder = trim($path,'/');
        $folder = explode('/', $folder);
        $folder = $folder[0] ?? '';
        if ($folder == '')
            return array();

        $result = array();
        if (!$this->isOk())
            return $result;

        if ($folder == 'query')
        {
            $params = $this->process->getParams();
            foreach ($params as $k => $v)
            {
                if (substr($k, 0, 6) != 'query.')
                    continue;
                $k = substr($k, 6);

                // TODO: filter based on the path

                $result[] = array(
                    'name' => $k,
                    'path' => $k,
                    'size' => null,
                    'modified' => null,
                    'is_dir' => false,
                    'root' => 'ProcessContext'
                );
            }
        }

        if ($folder == 'params')
        {
            $params = $this->process->getParams();
            foreach ($params as $k => $v)
            {
                // TODO: filter based on the path

                $result[] = array(
                    'name' => $k,
                    'path' => $k,
                    'size' => null,
                    'modified' => null,
                    'is_dir' => false,
                    'root' => 'ProcessContext'
                );
            }
        }

        return $result;
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function read(array $params, callable $callback)
    {
        $path = $params['path'] ?? (is_string($params) ? $params : '');
        $path = trim($path,'/');
        $parts = explode('/', $path);
        $folder = $parts[0] ?? '';
        $file = $parts[1] ?? '';

        if ($file == '')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($folder == 'params')
        {
            $params = $this->process->getParams();
            if (!isset($params[$file]))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

            if ($params[$file] instanceof \Flexio\Base\Stream)
            {
                $reader = $params[$file]->getReader();
                while (($buf = $reader->read(16384)) !== false)
                    $callback($buf);
            }
             else
            {
                $callback($params[$file]);
            }
        }
         else
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
    }

    public function write(array $params, callable $callback)
    {
        $path = $params['path'] ?? (is_string($params) ? $params : '');
        $path = trim($path,'/');
        $parts = explode('/', $path);
        $folder = $parts[0] ?? '';
        $file = $parts[1] ?? '';

        if ($file == '')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        if ($folder == 'params')
        {
            $stream = \Flexio\Base\Stream::create();

            $params = $this->process->getParams();
            $params[$file] = $stream;
            $this->process->setParams($params);

            $streamwriter = $stream->getWriter();

            while (($buf = $callback(16384)) !== false)
                $streamwriter->write($buf);
        }
         else
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);
        }
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function connect() : bool
    {
        return true;
    }

    private function isOk() : bool
    {
        return $this->process ? true : false;
    }
}
