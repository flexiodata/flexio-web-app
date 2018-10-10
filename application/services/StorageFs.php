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

    public function init($fspath /* can be string, or handle, or sqlite object */, $structure = null, $mode = 'r+') : bool
    {
        // make sure mode is allowed value
        $mode = str_replace('b', '', $mode);
        $idx = array_search($mode, ['r','r+', 'w', 'w+', 'a', 'a+','+']);
        if ($idx === false)
        {
            return false; // unknown mode
        }

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
                    $lc_colname = strtolower((string)$col['name']);
                    $lc_storename = strtolower((string)$col['store_name'] ?? (string)$col['name']);

                    $this->keyed_structure[$lc_colname] = $col;

                    if ($lc_colname != $lc_storename)
                    {
                        $this->storefield_map[$lc_storename] = $col['name'];
                    }
                }
            }
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


        if (($mode == 'w' || $mode == 'w+') && $structure !== null)
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

        /*
        if ($access == \Flexio\IFace\IStream::ACCESS_TRUNCATE)
        {
            $mode = 'w+b';
        }
         else
        {
            if ($access == \Flexio\IFace\IStream::ACCESS_READ)
                $mode = 'rb';
            else
                $mode =  $exists ? 'r+b' : 'w+b';
        }
        */

        $this->fspath = $fspath;

        if (IS_DEBUG())
            $this->file = fopen($fspath, $mode . 'b');
             else
            $this->file = @fopen($fspath, $mode . 'b');

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
                //fseek($this->file, 0, $access == \Flexio\IFace\IStream::ACCESS_APPEND ? SEEK_END : SEEK_SET);
            }
        }

        return true;
    }

    public function read($length = 1024) // TODO: add return type
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

    public function readRow() // TODO: add return type
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
        else if ($this->file)
        {
            $line = fgets($this->file);
            if ($line === false)
                return false;
            return rtrim($line, "\r\n");
        }


        return false;
    }

    public function getRows(int $offset, int $limit) // TODO: add return type
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
        if ($this->storefield_map !== null && count($this->storefield_map) > 0)
        {
            // rename any store column names back to column names user expects
            $newrow = [];
            foreach ($row as $col => $v)
            {
                $lc_col = strtolower((string)$col);
                if (array_key_exists($lc_col, $this->storefield_map))
                    $newrow[$this->storefield_map[$lc_col]] = $v;
                     else
                    $newrow[$lc_col] = $v;
            }
            $row = $newrow;
        }

        if ($this->keyed_structure !== null && count($this->keyed_structure) > 0)
        {
            foreach ($row as $col => &$value)
            {
                $lc_col = strtolower((string)$col);
                $type = $this->keyed_structure[$lc_col]['type'] ?? '';

                if ($type == 'boolean')
                {
                    if ($value === 0 || $value === '0' || $value === false)
                        $value = false;
                        else
                        $value = is_null($value) ? null : true;
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

    private $cur_schema = null;
    private $insert_rows = [];
    private $insert_length = 0;

    public function write($data) // TODO: add return type
    {
        if ($this->sqlite)
        {
            if (!is_array($data))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "data must be presented in array format");

            // check if the column schema being inserted is the same as the last;
            // if it's different, flush rows in buffer

            $new_schema = array_keys($data);
            if ($new_schema !== $this->cur_schema)
            {
                if (count($this->insert_rows) > 0)
                {
                    $this->flushRows();
                }
                $this->cur_schema = $new_schema;
            }

            $this->insert_rows[] = $data;

            foreach ($data as $d)
            {
                $this->insert_length += (strlen((string)$d)+3);
            }

            if ($this->insert_length >= 5000000)
            {
                $this->flushRows();
                $this->cur_schema = $new_schema;
            }

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

    public function getBytesWritten() // TODO: add return type
    {
        return $this->bytes_written;
    }

    private function flushRows() // TODO: add return type
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
            $this->insert_length = 0;
            return;
        }

        $field_info = [];

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
                $lc_name = strtolower((string)$name);
                $info = $this->keyed_structure[$lc_name] ?? null;
                if ($info)
                {
                    $field_info[] = $info;
                    if (isset($info['store_name']))
                        $name = $info['store_name'];
                }
                $sql .= StorageFs::quoteIdentifierIfNecessary($name);
            }

            $sql .= ') ';
        }

        // non-assoc array was passed; use all fields
        if (count($field_info) == 0)
        {
            $field_info = $this->structure;
        }

        $sql .= 'values ';

        $all_values = [];
        $all_types = [];
        $count = 0;

        $first = true;
        foreach ($this->insert_rows as $row)
        {
            $row = array_values($row);
            $colidx = 0;
            foreach ($row as &$val)
            {
                $coltype = $field_info[$colidx]['type'] ?? null;
                if ($coltype === 'boolean' || $coltype === 'date' || $coltype === 'datetime')
                {
                    if ($val === '')
                        $val = null;
                }

                /*
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
                */


                if (is_null($val))
                {
                    $type = SQLITE3_NULL;
                }
                 else if (is_bool($val))
                {
                    $val = $val ? 1 : 0;
                    $type = SQLITE3_INTEGER;
                }
                 else
                {
                    $val = (string)$val;
                    $type = SQLITE3_TEXT;
                }

                $all_values[] = $val;
                $all_types[] = $type;
                $count++;

                ++$colidx;
            }

            if ($first)
            {
                $first = false;
            }
             else
            {
                $sql .= ',';
            }

            $sql .= '(' . rtrim(str_repeat('?,', count($row)),',') . ')';
        }

        //var_dump($this->fspath);
        //echo $sql;
        //var_dump($sql);
        //die();


        $stmt = @$this->sqlite->prepare($sql);
        $res = false;

        if ($stmt)
        {
            for ($i = 0; $i < $count; ++$i)
            {
                $stmt->bindValue($i+1, $all_values[$i], $all_types[$i]);
            }
            $res = @$stmt->execute();
        }

        //$res = @$this->sqlite->exec($sql);

        if (!$res)
        {
            $errstr = $this->sqlite->lastErrorMsg();
            $marker = strpos($errstr, "has no column named ");
            if ($marker !== false)
            {
                $bad_field = trim(substr($errstr, $marker + 20));
                foreach ($this->insert_rows as &$row)
                    unset($row[$bad_field]);

                unset($all_values);
                unset($all_types);

                return $this->flushRows();
            }

            // var_dump($this->sqlite->lastErrorCode());
            $this->insert_rows = [];
            $this->insert_length = 0;
            return false;
        }

        $this->insert_rows = [];
        $this->insert_length = 0;
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
        return $this->openStream('r');
    }

    public function getWriter($mode = 'w+') : \Flexio\IFace\IStreamWriter
    {
        return $this->openStream($mode);
    }

    public function getInserter() : \Flexio\IFace\IStreamWriter
    {
        return $this->openStream($mode = 'r+');
    }

    public function setStructure(array $structure) : void
    {
        // optional ability to set structure
        $this->structure = $structure;
    }

    private function openStream($mode) : \Flexio\Services\StorageFileReaderWriter
    {
        $stream = new StorageFileReaderWriter();

        if ($this->sqlite)
        {
            $stream->init($this->sqlite, $this->structure, $mode);
        }
        else if ($this->file)
        {
            $stream->init($this->file, $this->structure, $mode);
            $this->file = null;
        }
        else
        {
            $stream->init($this->fspath, $this->structure, $mode);
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

    private function initialize($params) : bool
    {
        if (!isset($params['base_path']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "'base_path' must be specified in StorageFs::create()");
        if (!is_dir($params['base_path']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "'base_path' does not exist");

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
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "params['structure'] must be an array");

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, IS_DEBUG() ? "File '$path' does not exist" : null);
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

    public function unlink(string $path) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function move(string $old_path, string $new_path) : bool
    {
        $old_fspath = self::getFsPath($old_path);
        $new_fspath = self::getFsPath($new_path);

        if (!@file_exists($old_fspath))
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE, "File '$old_fspath' does not exist");
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

    public function list(string $path = '', array $options = []) : array
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
                    'modified' => null
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
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, "relative paths not allowed");
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

        if ($fieldsql === '')
            return 'empty_column_0 text';
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
