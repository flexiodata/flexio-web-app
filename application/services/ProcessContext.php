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

class ProcessContext implements \Flexio\Services\IConnection, \Flexio\Services\IFileSystem
{
    private $process = null;

    public function __construct(\Flexio\Base\IProcess $process)
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

        $result = array();
        if (!$this->isOk())
            return $result;

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
        // TODO: implement
        $path = $params['path'] ?? '';
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function write(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM;
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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
