<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-03-28
 *
 * @package flexio
 * @subpackage Base
 */

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

    public function exec($cmd, $cwd)
    {
        $descriptor_spec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $this->process = proc_open($cmd, $descriptor_spec, $this->pipes, $cwd, NULL);

        if (!is_resource($this->process))
        {
            $this->process = null;
            return false;
        }

        stream_set_blocking($this->pipes[0], 0);
        stream_set_blocking($this->pipes[1], 0);

        return true;
    }

    public function isRunning()
    {
        $status = proc_get_status($this->process);
        if ($status['running'])
            return true;
             else
            return false;
    }


    public function closeWrite()
    {
        if ($this->pipes[0])
        {
            fclose($this->pipes[0]);
            $this->pipes[0] = null;
        }
    }

    public function closeRead()
    {
        if ($this->pipes[1])
        {
            fclose($this->pipes[1]);
            $this->pipes[1] = null;
        }
    }

    public function closeError()
    {
        if ($this->pipes[2])
        {
            fclose($this->pipes[2]);
            $this->pipes[2] = null;
        }
    }



    public function write($buf)
    {
        fwrite($this->pipes[0], $buf);
    }

    public function read($size)
    {
        //return stream_get_contents($this->pipes[1]);
        return fread($this->pipes[1], $size);
        //fgets($this->pipes[1]);
    }


    public function canRead()
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
}
