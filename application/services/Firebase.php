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


class Firebase implements \Flexio\IFace\IFileSystem
{
    private $is_ok = false;

    public static function create(array $params = null) : \Flexio\Services\Firebase
    {
        $service = new self;
        return $service;
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

    public function createFile(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
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
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;
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
        return $this->is_ok;
    }
}
