<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
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

    public function getFlags() : int
    {
        return 0;
    }

    public function list(string $path = '', array $options = []) : array
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

        if ($folder == 'var')
        {
            $params = $this->process->getParams();
            foreach ($params as $k => $v)
            {
                $result[] = array(
                    'name' => $k,
                    'path' => $k,
                    'size' => null,
                    'modified' => null,
                    'hash' => '', // TODO: available?
                    'type' => 'FILE'
                );
            }
        }
        else if ($folder == 'query')
        {
            $params = $this->process->getParams();
            foreach ($params as $k => $v)
            {
                if (substr($k, 0, 6) != 'query.')
                    continue;
                $k = substr($k, 6);

                $result[] = array(
                    'name' => $k,
                    'path' => $k,
                    'size' => null,
                    'modified' => null,
                    'hash' => '', // TODO: available?
                    'type' => 'FILE'
                );
            }
        }
        else if ($folder == 'form')
        {
            $params = $this->process->getParams();
            foreach ($params as $k => $v)
            {
                if (substr($k, 0, 5) != 'form.')
                    continue;
                $k = substr($k, 5);

                $result[] = array(
                    'name' => $k,
                    'path' => $k,
                    'size' => null,
                    'modified' => null,
                    'hash' => '', // TODO: available?
                    'type' => 'FILE'
                );
            }
        }
        else if ($folder == 'files')
        {
            $params = $this->process->getFiles();
            foreach ($params as $k => $v)
            {
                $result[] = array(
                    'name' => $k,
                    'path' => $k,
                    'size' => null,
                    'modified' => null,
                    'hash' => '', // TODO: available?
                    'type' => 'FILE'
                );
            }
        }
        else if ($folder == 'fileno')
        {
            $params = $this->process->getLocalFiles();
            foreach ($params as $k => $v)
            {
                $result[] = array(
                    'name' => "$k",
                    'path' => "$k",
                    'size' => $v->getSize(),
                    'modified' => null,
                    'hash' => '', // TODO: available?
                    'type' => 'FILE'
                );
            }
        }

        return $result;
    }

    public function getFileInfo(string $path) : array
    {
        $stream = $this->open($path);

        return [
            'name' => $path,
            'path' => $path,
            'size' => $stream->getSize(),
            'modified' => null,
            'hash' => '', // TODO: available?
            'type' => 'FILE'
        ];
    }

    public function exists(string $path) : bool
    {
        try
        {
            $stream = $this->open($path);
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function unlink(string $path) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function open($params) : \Flexio\IFace\IStream
    {
        $path = $params['path'] ?? (is_string($params) ? $params : '');

        $path = trim($path,'/');

        if ($path == 'input' || $path == 'stdin')
        {
            return $this->process->getStdin();
        }

        $parts = explode('/', $path);
        $folder = $parts[0] ?? '';
        $file = $parts[1] ?? '';

        if ($file == '')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        $val = null;

        if ($folder == 'var' || $folder == 'vars' || $folder == 'params')
        {
            $params = $this->process->getParams();
            if (!isset($params[$file]))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $val = $params[$file];
        }
        else if ($folder == 'query')
        {
            $params = $this->process->getParams();
            if (!isset($params['query.' . $file]))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $val = $params['query.' . $file];
        }
        else if ($folder == 'form')
        {
            $params = $this->process->getParams();
            if (!isset($params['form.' . $file]))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $val = $params['form.' . $file];
        }
        else if ($folder == 'files')
        {
            $params = $this->process->getFiles();
            if (!isset($params[$file]))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

            $val = $params[$file];
        }
        else if ($folder == 'fileno')
        {
            $val = $this->process->getLocalFile((int)$file);

            if (!isset($val))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
        else
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }

        if ($val instanceof \Flexio\Base\Stream)
        {
            return $val;
        }
        else
        {
            $stream = \Flexio\Base\Stream::create();
            $stream->getWriter()->write((string)$v);
            return $stream;
        }
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? (is_string($params) ? $params : '');

        $stream = $this->open($path);

        $reader = $stream->getReader();
        while (($buf = $reader->read(16384)) !== false)
            $callback($buf);
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? (is_string($params) ? $params : '');
        $path = trim($path,'/');
        $parts = explode('/', $path);
        $folder = $parts[0] ?? '';
        $file = $parts[1] ?? '';

        if ($file == '')
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);

        if ($folder == 'params' || $folder == 'var')
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        }
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function isOk() : bool
    {
        return $this->process ? true : false;
    }
}
