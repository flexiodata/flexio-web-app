<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-03-28
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class ProcessPipe
{
    public $pipes = [null,null,null];
    public $process = null;

    function __destruct()
    {
        $this->closeWrite();
        $this->closeRead();
        $this->closeError();
        if ($this->process)
            proc_close($this->process);
    }

    public function exec($cmd, $cwd, $env = null) : bool
    {
        $descriptor_spec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $this->process = proc_open($cmd, $descriptor_spec, $this->pipes, $cwd, $env);
        if (!is_resource($this->process))
        {
            $this->process = null;
            return false;
        }

        stream_set_blocking($this->pipes[0], true);
        stream_set_blocking($this->pipes[1], false);

        //stream_set_blocking($this->pipes[0], false);
        //stream_set_blocking($this->pipes[1], false);
        //stream_set_blocking($this->pipes[0], true);
        //stream_set_blocking($this->pipes[1], true);
        return true;
    }

    public function isRunning() : bool
    {
        $status = proc_get_status($this->process);
        if ($status['running'])
            return true;
             else
            return false;
    }

    public function closeWrite() : void
    {
        if ($this->pipes[0])
        {
            fclose($this->pipes[0]);
            $this->pipes[0] = null;
        }
    }

    public function closeRead() : void
    {
        if ($this->pipes[1])
        {
            fclose($this->pipes[1]);
            $this->pipes[1] = null;
        }
    }

    public function closeError() : void
    {
        if ($this->pipes[2])
        {
            fclose($this->pipes[2]);
            $this->pipes[2] = null;
        }
    }

    public function write($buf) // TODO: add return type
    {
        //fxdebug("Want to write " . strlen($buf) . " bytes");

        $b = fwrite($this->pipes[0], $buf);
        //fxdebug("Actually wrote $b bytes");
        //fflush($this->pipes[0]);
        return $b;
    }

    public function read($size) // TODO: add return type
    {
        //return stream_get_contents($this->pipes[1]);
        return fread($this->pipes[1], $size);
        //fgets($this->pipes[1]);
    }

    public function canRead() : bool
    {
        return !feof($this->pipes[1]);
        /*
        $read = array($this->pipes[1]);
        $write = array();
        $except = array();
        $res = stream_select($read, $write, $except, 1);
        if ($res === false || $res == 0)
            return false;

        $s = fstat($this->pipes[1]);
        if (isset($s['size']) && $s['size'] > 0)
            return true;
             else
            return false;
        */
    }

    public function getError() : ?string
    {
        $str = fread($this->pipes[2], 8192);
        if (strlen($str) == 0)
            return null;
        return $str;
    }
}
