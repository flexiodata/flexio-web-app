<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-09-12
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Firebase implements \Flexio\Services\IConnection, \Flexio\Services\IFileSystem
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////

    private $is_ok = false;


    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create(array $params = null) : \Flexio\Services\Firebase
    {
        $service = new self;
        return $service;
    }

    public function connect() : \Flexio\Services\Firebase
    {
        return $this;
    }

    public function isOk() : bool
    {
        return $this->is_ok;
    }

    public function close()
    {
        $this->is_ok = false;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function list(string $path = '') : array
    {
        if (!$this->isOk())
            return array();

        // get the tables in the database
        $qdbname = "'" . $this->db->real_escape_string($this->database) . "'";
        $sql = "select table_name from information_schema.tables where table_schema=$qdbname;";
        $result = $this->db->query($sql);

        $fields = array();
        while ($result && ($row = $result->fetch_assoc()))
        {
            // TODO: filter based on the path

            $fields[] = array(
                'name' => $row['table_name'],
                'path' => $row['table_name'],
                'size' => null,
                'modified' => null,
                'is_dir' => false,
                'root' => 'Firebase'
            );
        }

        return $fields;
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
}
