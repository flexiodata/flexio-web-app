<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-21
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Process
{
    public static function list(string $owner, string $path) : \Flexio\Jobs\Process
    {
        $task = json_decode('{"op": "list", "params": {"path": "'. $path . '"}}',true);
        return \Flexio\Jobs\Process::create()->setOwner($owner)->execute($task);
    }

    public static function mkdir(string $owner, string $path) : \Flexio\Jobs\Process
    {
        $task = json_decode('{"op": "mkdir", "params": {"path": "'. $path . '"}}',true);
        return \Flexio\Jobs\Process::create()->setOwner($owner)->execute($task);
    }

    public static function create(string $owner, string $path) : \Flexio\Jobs\Process
    {
        $task = json_decode('{"op": "create", "params": {"path": "'. $path . '"}}',true);
        return \Flexio\Jobs\Process::create()->setOwner($owner)->execute($task);
    }

    public static function delete(string $owner, string $path) : \Flexio\Jobs\Process
    {
        $task = json_decode('{"op": "delete", "params": {"path": "'. $path . '"}}',true);
        return \Flexio\Jobs\Process::create()->setOwner($owner)->execute($task);
    }

    public static function write(string $owner, string $path, \Flexio\Base\Stream $stream = null) : \Flexio\Jobs\Process
    {
        $task = json_decode('{"op": "write", "params": {"path": "'. $path . '"}}',true);
        $process = \Flexio\Jobs\Process::create();
        $process->setOwner($owner);

        if (isset($stream))
            $process->setStdin($stream);

        $process->execute($task);
        return $process;
    }

    public static function read(string $owner, string $path) : \Flexio\Jobs\Process
    {
        $task = json_decode('{"op": "read", "params": {"path": "'. $path . '"}}',true);
        return \Flexio\Jobs\Process::create()->setOwner($owner)->execute($task);
    }
}
