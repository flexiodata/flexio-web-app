<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2018-02-17
 *
 * @package flexio
 * @subpackage System
 */


declare(strict_types=1);
namespace Flexio\System;


class DatabaseSessionHandler implements \SessionHandlerInterface
{
    private $registry_model;
    private $session_data = '';

    function __construct()
    {
    }

    function open($path, $name) : bool
    {
        $this->registry_model = \Flexio\System\System::getModel()->registry;
        return true;
    }

    function close() : bool
    {
        $this->registry_model = null;
        $this->session_data = '';
        return true;
    }

    function read($session_id) // TODO: add return type
    {
        $this->session_data = $this->registry_model->getString('', "session;$session_id", '');
        //file_put_contents("c:\\fxsite\\ben.txt", "GET ($session_id): ".$this->session_data . "\n", FILE_APPEND);
        return $this->session_data;
    }

    function write($session_id, $data) : void
    {
        if ($this->session_data !== $data)
        {
            // if data changed, write it
            $this->session_data = $this->registry_model->setString('', "session;$session_id", $data, 86400);
            $this->session_data = $data;
            //file_put_contents("c:\\fxsite\\ben.txt", "WRITE ($session_id): ".$data . "\n", FILE_APPEND);
        }
    }

    function destroy($session_id) : void
    {
        $this->registry_model->deleteEntryByName('', "session;$session_id");
        $this->session_data = '';
        //file_put_contents("c:\\fxsite\\ben.txt", "DELETE ($session_id)\n", FILE_APPEND);
    }

    function gc($age) : void
    {
        $this->registry_model->cleanupExpiredEntries();
    }
}
