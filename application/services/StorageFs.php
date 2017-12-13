<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2013-01-10
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;




class StorageFileReaderWriter implements \Flexio\IFace\IStreamReader, \Flexio\IFace\IStreamWriter
{
    private $fspath = null;
    private $file = null;

    private $sqlite = null;
    private $result = null; // sqlite result object

    private $structure = null;
    private $keyed_structure = null;  // structure with field names as key
    private $storefield_map = null;

    function __destruct()
    {
        $this->close();
    }

    public function init($fspath /* can be string, or handle, or sqlite object */, $structure = null, $truncate = false) : bool
    {
        if ($structure !== null && count($structure) > 0)
        {
            $this->structure = $structure;
            $this->keyed_structure = [];
            $this->storefield_map = [];
            foreach ($this->structure as $col)
            {
                // only store columns in $this->keyed_structure that we need for type casting;
                // or if the store name is different than the field name
                if (isset($col['name']))
                {
                    if (isset($col['store_name']) && $col['name'] != $col['store_name'])
                    {
                        $this->keyed_structure[$col['name']] = $col;
                        $this->storefield_map[$col['store_name']] = $col['name'];
                    }
                     else if (isset($col['type']) && $col['type'] == 'boolean')
                    {
                        $this->keyed_structure[$col['name']] = $col;
                    }

                }
            }

            if (count($this->keyed_structure) == 0)
                $this->keyed_structure = null; // no type casting necessary
        }

        if ($fspath instanceof \Sqlite3)
        {
            $this->sqlite = $fspath;
            return true;
        }
         else if (!is_string($fspath))
        {
            $this->file = $fspath;
            return true;
        }

        $exists = file_exists($fspath);


        if ($truncate && $structure !== null)
        {
            // caller wants to start a new table
            try
            {
                if ($exists)
                    @unlink($fspath);
                $this->sqlite = new \SQLite3($fspath);
                $fields = StorageFs::getStructureSql($structure);
                $sql = 'create table fxtbl (' . $fields . ')';
                $this->sqlite->exec($sql);
            }
            catch (\Exception $e)
            {
                return false;
            }
            return true;
        }


        if ($truncate)
        {
            $mode = 'w+b';
        }
         else
        {

            $mode =  $exists ? 'r+b' : 'w+b';
        }


        $this->fspath = $fspath;

        if (IS_DEBUG())
            $this->file = fopen($fspath, $mode);
             else
            $this->file = @fopen($fspath, $mode);

        if ($exists)
        {
            fseek($this->file, 0, SEEK_SET);
            if (fread($this->file, 15) === 'SQLite format 3')
            {
                fclose($this->file);
                $this->file = null;

                try
                {
                    $this->sqlite = new \SQLite3($fspath);
                }
                catch (\Exception $e)
                {
                    return false;
                }
            }
             else
            {
                fseek($this->file, 0, SEEK_SET);
            }
        }

        return true;
    }

    public function read($length = 1024)
    {
        if ($this->isOk() === false)
            return false;
        if ($this->file === NULL)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED, $this->sqlite ? "Cannot read a table as a file stream" : null);
        
        $res = fread($this->file, $length);
        if ($res === false)
            return false;
        if ($length > 0 && strlen($res) == 0)
            return false;
        return $res;
    }

    public function readRow()
    {

        if ($this->isOk() === false)
            return false;

        if ($this->sqlite)
        {
            if (!$this->result)
            {
                $this->result = $this->sqlite->query('select * from fxtbl');
            }

            $row = $this->result->fetchArray(SQLITE3_ASSOC); // returns false if eof
            if ($row === false)
                return false;
            return $this->applyStructureToRow($row);
        }

        return false;
    }

    public function getRows(int $offset, int $limit)
    {
        if ($this->isOk() === false)
            return false;

        $sql = 'select * from fxtbl';

        if (isset($limit) && (int)$limit < PHP_INT_MAX)
            $sql .= ' limit ' . (int)$limit;

        if (isset($offset) && (int)$offset > 0)
            $sql .= ' offset ' . (int)$offset;

        $result = $this->sqlite->query($sql);

        if (!$result)
            return [];

        $res = [];
        while (($row = $result->fetchArray(SQLITE3_ASSOC)) !== false)
            $res[] = $this->applyStructureToRow($row);

        return $res;
    }

    private function applyStructureToRow(array $row) : array
    {
        if (count($this->storefield_map) > 0)
        {
            // rename any store column names back to column names user expects
            $newrow = [];
            foreach ($row as $k => $v)
            {
                if (array_key_exists($k, $this->storefield_map))
                    $newrow[$this->storefield_map[$k]] = $v;
                     else
                    $newrow[$k] = $v;
            }
            $row = $newrow;
        }

        if (count($this->keyed_structure) > 0)
        {
            foreach ($row as $col => &$value)
            {
                if (isset($this->keyed_structure[$col]['type']))
                {
                    if ($this->keyed_structure[$col]['type'] == 'boolean')
                    {
                        if ($value === 0 || $value === '0' || $value === false)
                            $value = false;
                            else
                            $value = true;
                    }
                }
            }
        }

        return $row;
    }

    public function getRowCount() : int
    {
        if ($this->sqlite)
        {
            $rows = $this->sqlite->query("select count(*) as count from fxtbl");
            $row = $rows->fetchArray();
            return (int)$row['count'];
        }

        return 0;
    }

    public function close() : bool
    {
        if ($this->isOk() === false)
            return true;

        if ($this->file)
        {
            fclose($this->file);
            $this->file = null;
        }

        if ($this->sqlite)
        {
            $this->flushRows();
            $this->sqlite = null;
        }

        return true;
    }

    private function isOk() : bool
    {
        if ($this->file !== null || $this->sqlite)
            return true;

        return false;
    }


    // \Flexio\IFace\IStreamWriter

    private $first_write = true;
    private $bytes_written = 0;

    private $last_schema = null;
    private $insert_rows = [];

    public function write($data)
    {
        if ($this->sqlite)
        {
            if (!is_array($data))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "data must be presented in array format");

            // check if the column schema being inserted is the same as the last;
            // if it's different, flush rows in buffer

            $cur_schema = array_keys($data);
            if ($cur_schema !== $this->last_schema)
            {
                $this->flushRows();
                $this->last_schema = $cur_schema;
            }

            if (count($this->insert_rows) >= 100)
            {
                $this->flushRows();
                $this->last_schema = $cur_schema;
            }

            $this->insert_rows[] = $data;

            return;
        }

        if ($this->bytes_written == 0)
        {
            fseek($this->file, 0, SEEK_END);
        }

        $res = fwrite($this->file, $data);

        if ($res === false)
        {
            return false; // error
        }

        $this->bytes_written += $res;
    }

    public function getBytesWritten()
    {
        return $this->bytes_written;
    }

    private function flushRows()
    {
        if (!$this->sqlite)
            return;
        if (count($this->insert_rows) == 0)
            return;

        $sql = 'insert into fxtbl ';

        if (count($this->insert_rows[0]) == 0)
        {
            $sql .= 'default values';
            $res = @$this->sqlite->exec($sql);
            $this->insert_rows = [];
            return;
        }

        // because we checked column schema in write(), all
        // arrays have the same schema in this function

        if (\Flexio\Base\Util::isAssociativeArray($this->insert_rows[0]))
        {
            $sql .= '(';

            $keys = array_keys($this->insert_rows[0]);
            for ($i = 0; $i < count($keys); ++$i)
            {
                if ($i > 0)
                    $sql .= ',';
                $name = $keys[$i];
                if (isset($this->keyed_structure[$name]['store_name']))
                    $name = $this->keyed_structure[$name]['store_name'];
                $sql .= StorageFs::quoteIdentifierIfNecessary($name);
            }

            $sql .= ') ';
        }

        $sql .= 'values ';

        $first = true;
        foreach ($this->insert_rows as $row)
        {
            $row = array_values($row);
            foreach ($row as &$val)
            {
                if (is_null($val))
                {
                    $val = 'null';
                }
                 else if (is_bool($val))
                {
                    $val = $val ? '1' : '0';
                }
                 else
                {
                    $val = (string)$val;
                    if (strpos($val, "'") !== false)
                    {
                        $val = str_replace("'", "''", $val);
                    }
                    $val = "'$val'";
                }
            }

            if ($first)
            {
                $first = false;
            }
             else
            {
                $sql .= ',';
            }

            $sql .= '(' . join(',', $row) . ')';
        }

        //var_dump($this->fspath);
        //echo $sql;

        $res = @$this->sqlite->exec($sql);

        if (!$res)
        {
            $errstr = $this->sqlite->lastErrorMsg();
            $marker = strpos($errstr, "has no column named ");
            if ($marker !== false)
            {
                $bad_field = trim(substr($errstr, $marker + 20));
                foreach ($this->insert_rows as &$row)
                    unset($row[$bad_field]);

                return $this->flushRows();
            }

            // var_dump($this->sqlite->lastErrorCode());
            $this->insert_rows = [];
            return false;
        }

        $this->insert_rows = [];
        return true;
    }
}


class StorageFsFile
{
    public $sqlite = null;
    public $fspath = null;
    public $file = null;
    public $structure = null;

    public function getReader() : \Flexio\IFace\IStreamReader
    {
        return $this->openStream(false);
    }

    public function getWriter() : \Flexio\IFace\IStreamWriter
    {
        return $this->openStream(true);
    }

    public function getInserter() : \Flexio\IFace\IStreamWriter
    {
        return $this->openStream(false);
    }

    public function setStructure(array $structure)
    {
        // optional ability to set structure
        $this->structure = $structure;
    }

    private function openStream($truncate = false) : \Flexio\Services\StorageFileReaderWriter
    {
        $stream = new StorageFileReaderWriter();

        if ($this->sqlite)
        {
            $stream->init($this->sqlite, $this->structure, $truncate);
        }
        else if ($this->file)
        {
            $stream->init($this->file, $this->structure, false);
            $this->file = null;
        }
        else
        {
            $stream->init($this->fspath, $this->structure, $truncate);
        }

        return $stream;
    }
}


class StorageFs
{
    private $base_path = null;

    public static function create(array $params) : \Flexio\Services\StorageFs
    {
        $service = new self;
        if ($service->initialize($params) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    private function initialize($params)
    {
        if (!isset($params['base_path']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "'base_path' must be specified in StorageFs::create()");
        if (!is_dir($params['base_path']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER, "'base_path' does not exist");

        $this->base_path = $params['base_path'];

        return true;
    }

    // createFile() creates a file; optionally $params['structure'] may be specified to
    // create a database table

    public function createFile(string $path, array $params = []) : \Flexio\Services\StorageFsFile
    {
        $fspath = self::getFsPath($path);

        if (isset($params['structure']))
        {
            if (!is_array($params['structure']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "params['structure'] must be an array");

            $is_memory_database = $params['memory'] ?? false;
            if ($is_memory_database)
            {
                $sqlite = new \SQLite3(':memory:');
            }
             else
            {
                if (@file_exists($fspath))
                {
                    if (@unlink($fspath) === false)
                        throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
                }
                
                $sqlite = new \SQLite3($fspath);
            }

            $fields = self::getStructureSql($params['structure']);
            $sql = 'create table fxtbl (' . $fields . ')';

            $sqlite->exec($sql);

            $file = new StorageFsFile();
            $file->sqlite = $sqlite;
            $file->structure = $params['structure'];
            return $file;
        }
         else
        {
            if (IS_DEBUG())
                $f = fopen($fspath, 'w+');
                else
                $f = @fopen($fspath, 'w+');

            if (!$f)
            {
                return false;
            }

            $file = new StorageFsFile();
            $file->fspath = $fspath;
            $file->file = $f;
            return $file;
        }
    }

    public function open(string $path, array $params = []) : \Flexio\Services\StorageFsFile
    {
        $fspath = self::getFsPath($path);

        if (!@file_exists($fspath))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NOT_FOUND, IS_DEBUG() ? "File '$path' does not exist" : null);
        }

        $file = new StorageFsFile();
        $file->fspath = $fspath;

        if (isset($params['structure']))
        {
            $file->setStructure($params['structure']);
        }

        return $file;
    }


    public function createDirectory(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        return @mkdir($fspath, 0750, true) ? true : false;
    }



    public function deleteFile(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        // TODO: implement
        return false;
    }

    public function move(string $old_path, string $new_path) : bool
    {
        $old_fspath = self::getFsPath($old_path);
        $new_fspath = self::getFsPath($new_path);

        if (!@file_exists($old_fspath))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT, "File '$old_fspath' does not exist");
        }

        if (@file_exists($new_fspath))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::OBJECT_ALREADY_EXISTS, "Target file '$new_fspath' already exists");
        }

        if (!@rename($old_fspath, $new_fspath))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, "Unable to rename file");
        }

        return true;
    }

    public function list(string $path)
    {
        $fspath = self::getFsPath($path);

        $arr = array();
        if ($handle = opendir($fspath))
        {
            while (false !== ($file = readdir($handle)))
            {
                $combined = $path;
                if (substr($combined, -1) != '/')
                    $combined .= '/';
                $combined .= $file;

                $isdir = is_dir($fspath . DIRECTORY_SEPARATOR . $file);

                $f = array(
                    'name' => $file,
                    'path' => $combined,
                    'type' => ($isdir ? 'DIR' : 'FILE'),
                    'size' => null,
                    'modified' => null,
                    'is_dir' => $isdir
                );

                $arr[] = $file;
            }
            closedir($handle);
        }

        return $arr;
    }

    public function exists(string $path) : bool
    {
        $fspath = self::getFsPath($path);

        if (IS_DEBUG())
        {
            if (file_exists($fspath))
                return true;
            else if (is_dir($fspath))
                return true;
            else
                return false;
        }
         else
        {
            if (@file_exists($fspath))
                return true;
            else if (@is_dir($fspath))
                return true;
            else
                return false;
        }
    }



    private function getFsPath(string $path) : string
    {
        // trim off trailing/preceding slashes and whitespace
        $path = trim($path, "\ \t\n\r\0\x0B");

        foreach (explode('/', $path) as $part)
        {
            if ($part == '..')
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER, "relative paths not allowed");
        }

        $str = $this->base_path . DIRECTORY_SEPARATOR . $path;

        if (DIRECTORY_SEPARATOR != '/')
            $str = str_replace('/', DIRECTORY_SEPARATOR, $str);

        return $str;
    }






    // helper functions for table functionality

    public static function getStructureSql(array $structure) : string
    {
        // map each field into an sql definition
        $fieldsql = '';
        foreach ($structure as $fieldinfo)
        {
            if (strlen($fieldsql) > 0)
                $fieldsql .= ',';
            $fieldsql .= self::getFieldString($fieldinfo);
        }
        return $fieldsql;
    }

    private static function getFieldString(array $field) : string
    {
        if (!isset($field['name']))
            return '';
        if (!isset($field['type']))
            return '';

        $name = $field['store_name'] ?? $field['name'];
        $type = $field['type'];
        $width = isset($field['width']) ? $field['width'] : null;
        $scale = isset($field['scale']) ? $field['scale'] : null;

        //$qname = self::quoteIdentifierIfNecessary($name);
        // for sqlite, we'll always quote field names
        $qname = self::quoteIdentifier($name);

        switch ($type)
        {
            default:
                return '';

            case 'character':
            case 'widecharacter':
                if ($width == 0 || is_null($width))
                    return "$qname varchar";
                     else
                    return "$qname varchar($width)";

            case 'text':
                return "$qname text";

            case 'numeric':
                if (is_null($scale))
                    return "$qname numeric";
                     else
                    return "$qname numeric($width,$scale)";

            case 'double':    return "$qname double precision";
            case 'real':      return "$qname real";
            case 'integer':   return "$qname integer";
            case 'date':      return "$qname date";
            case 'timestamp':
            case 'datetime':  return "$qname timestamp";
            case 'boolean':   return "$qname boolean";
            case 'serial':    return "$qname serial";
            case 'bigserial': return "$qname bigserial";
        }
    }

    public static function quoteIdentifierIfNecessary(string $str) : string
    {
        if (false === strpbrk($str, "\"'-/\\!@#$%^&*?() \t"))
            return $str;
             else
            return self::quoteIdentifier($str);
    }

    public static function quoteIdentifier(string $str) : string
    {
        return '"' . str_replace('"', '""', $str) . '"';
    }
    
}
