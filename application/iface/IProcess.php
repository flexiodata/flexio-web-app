<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-12-11
 *
 * @package flexio
 * @subpackage IFace
 */


declare(strict_types=1);
namespace Flexio\IFace;


interface IProcess
{
    public function setOwner(string $owner_eid);
    public function getOwner();
    public function setParams(array $arr);
    public function getParams();
    public function addFile(string $name, \Flexio\IFace\IStream $stream);
    public function setStdin(\Flexio\IFace\IStream $stream);
    public function getStdin();
    public function setStdout(\Flexio\IFace\IStream $stream);
    public function getStdout();
    public function setResponseCode(int $code);
    public function getResponseCode();
    public function setError(string $code = '', string $message = null, string $file = null, int $line = null, string $type = null, array $trace = null);
    public function getError();
    public function hasError();
    public function validate(array $task);
    public function execute(array $task);
    public function stop();
    public function isStopped();
    public function signal(string $event, array $properties);
}

