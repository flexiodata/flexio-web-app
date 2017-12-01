<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-01
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class StoredProcess implements \Flexio\Jobs\IProcess
{
    private $engine;        // instance of \Flexio\Jobs\Process
    private $obj_process;   // instance of \Flexio\Object\Process (for persisting process info)

    public function __construct()
    {
        $this->engine = \Flexio\Jobs\Process::create();
    }

    public static function create() : \Flexio\Jobs\StoredProcess
    {
        $object = new static();
        return $object;
    }

    public function addEventHandler($handler)
    {
        return $this->engine->addEventHandler($handler);
    }

    public function setMetadata(array $metadata)
    {
        return $this->engine->setMetadata($handler);
    }

    public function getMetadata() : array
    {
        return $this->engine->getMetadata();
    }

    public function addTasks(array $tasks)
    {
        return $this->engine->addTasks($tasks);
    }

    public function getTasks() : array
    {
        return $this->engine->getTasks();
    }

    public function setParams(array $arr)
    {
        return $this->engine->setParams($arr);
    }

    public function getParams() : array
    {
        return $this->engine->getParams();
    }

    public function addFile(string $name, \Flexio\Base\IStream $stream)
    {
        return $this->engine->addFile($name, $stream);
    }

    public function getStdin() : \Flexio\Base\IStream
    {

    }

    public function getStdout() : \Flexio\Base\IStream
    {

    }

    public function setResponseCode(int $code)
    {
        return $this->engine->setResponseCode($code);
    }

    public function getResponseCode() : int
    {
        return $this->engine->getResponseCode();
    }

    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null)
    {
        return $this->engine->setError($code, $message, $file, $line, $type, $trace);
    }

    public function getError() : array
    {
        return $this->engine->getError();
    }

    public function hasError() : bool
    {
        return $this->engine->hasError();
    }

    public function getStatusInfo() : array
    {
        return $this->engine->getStatusInfo();
    }

    public function execute()
    {
        return $this->engine->execute();
    }
}
