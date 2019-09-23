<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2015-05-19
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


class Postgres implements \Flexio\IFace\IConnection,
                          \Flexio\IFace\IFileSystem
{
    private $authenticated = false;
    private $db = null;
    private $host, $port, $database, $username, $password;

    public static function create(array $params = null) : \Flexio\Services\Postgres
    {
        if (isset($params['port']))
        $params['port'] = (string)$params['port'];

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'host'     => array('type' => 'string', 'required' => true),
                'port'     => array('type' => 'string', 'required' => true),
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true),
                'database' => array('type' => 'string', 'required' => true),
                'path'     => array('type' => 'string', 'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_params = $validator->getParams();
        $host = $validated_params['host'];
        $port = intval($validated_params['port']);
        $database = $validated_params['database'];
        $username = $validated_params['username'];
        $password = $validated_params['password'];

        $service = new self;
        if ($service->initialize($host, $port, $database, $username, $password) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_SERVICE);

        return $service;
    }

    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public function connect() : bool
    {
        $host = $this->host;
        $port = $this->port;
        $database = $this->database;
        $username = $this->username;
        $password = $this->password;

        if ($this->initialize($host, $port, $database, $username, $password) === false)
            return false;

        return true;
    }

    public function disconnect() : void
    {
    }

    public function authenticated() : bool
    {
        return $this->authenticated;
    }

    public function get() : array
    {
        $properties = array(
            'host'     => $this->host,
            'port'     => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'database' => $this->database,
            'path'     => $this->path
        );

        return $properties;
    }

    ////////////////////////////////////////////////////////////
    // IFileSystem interface
    ////////////////////////////////////////////////////////////

    public function getFlags() : int
    {
        return 0;
    }

    public function list(string $path = '', array $options = []) : array
    {
        $db = $this->newConnection();

        // get the tables in the database
        $sql = "select tablename from pg_tables where schemaname = current_schema()";
        $result = $this->db->query($sql);
        $results = $result->fetchAll();

        $tables = [];
        foreach ($results as $r)
        {
            // TODO: filter based on the path

            $tables[] = array(
                'name' => $r['tablename'],
                'path' => $r['tablename'],
                'size' => null,
                'modified' => null,
                'hash' => '', // TODO: available?
                'type' => 'FILE'
            );
        }

        return $tables;
    }

    public function getFileInfo(string $path) : array
    {
        $qtbl = $this->db->quote($path);
        $sql = "select t.tablename as name, coalesce(d.description,'') as type from pg_tables t " .
               'inner join pg_class as c on c.relname=t.tablename ' .
               'left outer join pg_description as d on c.oid=d.objoid ' .
               'where t.schemaname = current_schema() and c.relname=' . $qtbl;

        $stmt = $this->db->query($sql);
        $res = null;
        if ($stmt) $res = $stmt->fetchAll();

        if (count($res) != 1)
            return array();

        $type = $res[0]['type'];
        $type_parts = explode(';',$type);
        $type_parts = array_map('trim', $type_parts);

        $result = [];
        if (count($type_parts) == 3 && $type_parts[0] == 'stream')
        {
            $result['name'] = $path;
            $result['type'] = 'stream';
            $result['format'] = 'default';
            $result['mime_type'] = $type_parts[1];
            $result['encoding'] = $type_parts[2];
        }
         else
        {
            $result['name'] = $path;
            $result['type'] = 'table';
            $result['format'] = 'default';
            $result['mime_type'] = 'text/plain';
            $result['encoding'] = 'default';
        }

        return $result;
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function createFile(string $path, array $properties = []) : bool
    {
        $mime_type = $properties['mime_type'] ?? 'text/plain';
        $encoding = $properties['encoding'] ?? 'default';

        $this->deleteFile($path);

        $qtbl = self::quoteIdentifierIfNecessary($path);
        $qmimetype = $this->db->quote($mime_type);
        $qencoding = $this->db->quote($encoding);

        $this->db->beginTransaction();

        $sql  = "CREATE TABLE $qtbl (xdpgsql_stream VARCHAR(80), mime_type VARCHAR(80), encoding VARCHAR(80), blob_id OID)";
        $res = $this->db->exec($sql);
        if ($res === false)
        {
            $this->db->rollBack();
            return false;
        }

        $res = $this->db->query('SELECT lo_create(0) as blob_id');
        if (!$res || !($row = $res->fetch()))
        {
            $this->db->rollBack();
            return false;
        }
        $oid = $row['blob_id'];

        $sql = "INSERT INTO $qtbl (xdpgsql_stream, mime_type, encoding, blob_id) VALUES ('', $qmimetype, $qencoding, $oid)";
        $res = $this->db->exec($sql);
        if ($res != 1)
        {
            $this->db->rollBack();
            return false;
        }

        // TODO: check for injection
        $sql = "COMMENT ON TABLE $qtbl IS 'stream; $mime_type; $encoding'";
        $res = $this->db->exec($sql);
        if ($res === false)
        {
            $this->db->rollBack();
            return false;
        }

        $this->db->commit();
        return true;
    }

    public function createDirectory(string $path, array $properties = []) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function unlink(string $path) : bool
    {
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function open($path) : \Flexio\IFace\IStream
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    public function read(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? '';

        // find out if we have a table or not
        $is_table = false;
        $finfo = $this->getFileInfo($path);
        if ($finfo && $finfo['type'] == 'table')
            $is_table = true;

        $stream = false;

        if ($is_table === true)
        {
            // get the rows
            $iter = $this->query(['table' => $path]);
            while (true)
            {
                $row = $iter->fetchRow();
                if (!$row)
                    break;

                $callback($row);
            }
        }
         else
        {
            // get the data
            $length = 1024; // TODO: read in some other chunk size?
            $stream = $this->openFile($path);
            while (true)
            {
                $data = $stream->read($length);
                if (!$data)
                    break;

                $callback($data);
            }
        }
    }

    public function write(array $params, callable $callback) // TODO: add return type
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::STREAM;

        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }

    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function newConnection() // TODO: add return type
    {
        $dsn = 'pgsql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->database;

        if (isset($GLOBALS['g_config']->query_log))
        {
            $t1 = (int)microtime(true);
            $t1_micropart = sprintf("%06d", ($t1 - floor($t1)) * 1000000);
            $date = new \DateTime(date('Y-m-d H:i:s.' . $t1_micropart, $t1));
            $timestamp = $date->format("Y-m-d H:i:s.u");

            $log = "Timestamp: $timestamp\nDATA PROVIDER CONNECTING TO: $dsn\n\n";
            \Flexio\System\System::log($log);
        }

        try
        {
            $db = new \PDO($dsn, $this->username, $this->password);
            $db->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            $db->exec("SET SESSION TIME ZONE 'UTC'");
            $db->exec("SET SESSION lc_monetary='en_US.UTF-8'");

            return $db;
        }
        catch (\Exception $e)
        {
            \Flexio\System\System::log('Could not connect to Postgres server. Exception message: ' . $e->getMessage());
        }

        return null;
    }

    private function initialize(string $host, int $port, string $database, string $username, string $password) : bool
    {
        $this->host = $host;
        $this->port = intval($port);
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;

        $this->db = $this->newConnection();
        if (!is_null($this->db))
            $this->authenticated = true;

        return $this->authenticated;
    }

    public function exec(string $sql) : bool
    {
        try
        {
            $this->db->exec($sql);
            return true;
        }
        catch (\Exception $e)
        {
            \Flexio\System\System::log('Could not execute statement. Exception message: ' . $e->getMessage());
            return false;
        }
    }

    public function query(array $query_params) // TODO: add return type
    {
        // TODO: add validation; quote table parameter

        if (!isset($query_params['table']) || strlen($query_params['table']) == 0)
            return null;

        $table = $query_params['table'];
        $rough_count = -1;
        $primary_key = '';

        // figure out roughly how many rows are in the table
        $result = $this->db->query('explain select * from ' . $table);

        if (!$result)
        {
            // table doesn't exist
            return null;
        }

        $row = $result->fetch();
        if ($row)
        {
            $row = array_values($row);
            $str = $row[0];
            $pos = strpos($str, 'rows=');
            if ($pos !== false)
            {
                $str = substr($str, $pos+5);
                $rough_count = intval($str);
            }
        }

        // does the table have an xdrowid?
        $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '$table' AND column_name='xdrowid'";
        $result = $this->db->query($sql);
        $row = $result ? $result->fetchAll() : null;
        if ($row && count($row) == 1)
        {
            $primary_key = 'xdrowid';
        }

        // figure out primary key
        if (strlen($primary_key) == 0)
        {
            $sql = "SELECT pg_attribute.attname, format_type(pg_attribute.atttypid, pg_attribute.atttypmod) FROM pg_index, pg_class, pg_attribute WHERE " .
                   "pg_class.oid = '$table'::regclass AND indrelid = pg_class.oid AND pg_attribute.attrelid = pg_class.oid AND pg_attribute.attnum = any(pg_index.indkey) AND indisprimary";

            $result = $this->db->query($sql);
            $row = $result ? $result->fetchAll() : null;
            if ($row && count($row) == 1)
            {
                // for now, only accept tables with one primary key field
                $primary_key = $row[0]['attname'];
            }
        }

        $sql = 'select count(*) as cnt from ' . $table;
        $t1 = (int)microtime(true);
        $result = $this->db->query($sql);
        $t2 = (int)microtime(true);

        if (isset($GLOBALS['g_config']->query_log))
        {
            $t1_micropart = sprintf("%06d", ($t1 - floor($t1)) * 1000000);
            $date = new \DateTime(date('Y-m-d H:i:s.' . $t1_micropart, $t1));
            $timestamp = $date->format("Y-m-d H:i:s.u");

            $log = ("Timestamp: $timestamp; Query time: " . sprintf("%0.4f sec", ($t2-$t1)) . "\nDATA: $sql\n\n");
            \Flexio\System\System::log($log);
        }

        if ($result)
            $row = $result->fetch();
        if (!isset($row['cnt']))
            return null;
        $row_count = $row['cnt'];

        $iter = new PostgresIterator;
        $iter->db = $this->db;
        $iter->table = $table;
        $iter->primary_key = $primary_key;
        $iter->row_count = $row_count;
        $iter->cursor_type = 'xh1';

        return $iter;
    }

    public function queryAll(string $table) // TODO: add return type
    {
        $sql = "select * from " . self::quoteIdentifierIfNecessary($table);

        $iter = new PostgresIteratorAll;
        $iter->result = $this->db->query($sql);

        if (!$iter->result)
            return null;

        return $iter;
    }

    public function createTable(string $table, array $structure) : bool
    {
        try
        {
            // map each field into an sql definition
            $fieldsql = '';
            foreach ($structure as $fieldinfo)
            {
                if (strlen($fieldsql) > 0)
                    $fieldsql .= ',';
                $fieldsql .= self::getFieldString($fieldinfo);
            }

            // create the table
            $qdbtable = self::quoteIdentifierIfNecessary($table);

            $sql = "drop table if exists $qdbtable";
            $this->db->exec($sql);

            $sql = "create table $qdbtable ($fieldsql)";
            $this->db->exec($sql);
        }
        catch (\Exception $e)
        {
            \Flexio\System\System::log('Could not create table. Exception message: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function selectInto(string $where, string $order, string $output) : bool
    {
        try
        {
            $sql = "select ...";
            $this->db->exec($sql);
        }
        catch (\Exception $e)
        {
            \Flexio\System\System::log('Could not execute statement. Exception message: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function openFile(string $path) // TODO: add return type
    {
        $db = $this->newConnection();
        $qtbl = self::quoteIdentifierIfNecessary($path);

        try
        {
            $res = $db->query("SELECT blob_id from $qtbl");
            if (!$res || !($row = $res->fetch()))
                return false;
            $oid = $row['blob_id'];
        }
        catch (\Exception $e)
        {
            \Flexio\System\System::log('Could not open file. Exception message: ' . $e->getMessage());
            return null;
        }

        $db->beginTransaction();
        $f = $db->pgsqlLOBOpen((string)$oid, 'r+b');
        if (!$f)
            return null;

        $binf = new PostgresBinaryFile;
        $binf->db = $db;
        $binf->f = $f;

        return $binf;
    }

    public function bulkInsert(string $table) // TODO: add return type
    {
        $inserter = new PostgresInserterMultiRow;

        // TODO: old implementation
        //$inserter = new PostgresInserter;

        if (!$inserter->init($this, $this->db, $table))
            return false;
        return $inserter;
    }

    public function deleteFile(string $path) : void
    {
        $qdbtable = self::quoteIdentifierIfNecessary($path);

        $sql = "drop table if exists $qdbtable";
        $this->db->exec($sql);
    }

    public function describeTable(string $table) // TODO: add return type
    {
        $handle_type = substr($table,0,3);

        if ($handle_type == 'xh1')
        {
            $iter = new PostgresIterator;
            if (!$iter->fromHandle($table))
                return null;
            $table = $iter->table;
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        $sql  = "select attname,atttypid,atttypmod from pg_attribute where ";
        $sql .= "attrelid = (select oid from pg_class where relname='$table') and ";
        $sql .= "attnum >= 1 order by attnum";

        $t1 = (int)microtime(true);
        $result = $this->db->query($sql);
        $t2 = (int)microtime(true);

        if (isset($GLOBALS['g_config']->query_log))
        {
            $t1_micropart = sprintf("%06d", ($t1 - floor($t1)) * 1000000);
            $date = new \DateTime(date('Y-m-d H:i:s.' . $t1_micropart, $t1));
            $timestamp = $date->format("Y-m-d H:i:s.u");

            $log = ("Timestamp: $timestamp; Query time: " . sprintf("%0.4f sec", ($t2-$t1)) . "\nDATA: $sql\n\n");
            \Flexio\System\System::log($log);
        }

        $structure = [];

        foreach ($result as $row)
        {
            $name = $row['attname'];

            if ($name == 'xdrowid' || substr($name, 0, 8) == 'xdpgsql_')
                continue;

            $pgtype = $row['atttypid'];
            $pgtypemod = $row['atttypmod'];

            $type = self::pgtypeToDbtype($pgtype);
            $width = 0;
            $scale = 0;

            if ($type == 'numeric' || $type == 'double')
            {
                if ($pgtypemod == -1)
                {
                    $width = 12;
                    $scale = 4;
                }
                 else
                {
                    $pgtypemod -= 4;
                    $width = ($pgtypemod >> 16);
                    $scale = ($pgtypemod & 0xffff);
                }
            }
             else if ($type == 'text')
            {
                $width = null;
                $scale = 0;
            }
             else if ($type == 'character')
            {
                $width = $pgtypemod - 4;
                $scale = 0;

                if ($width < 0)
                    $width = null;
            }
             else if ($type == 'datetime')
            {
                $width = 8;
                $scale = 0;
            }
             else if ($type == 'integer' || $type == 'date')
            {
                $width = 4;
                $scale = 0;
            }
             else if ($type == 'boolean')
            {
                $width = 1;
                $scale = 0;
            }
             else
            {
                $width = 8;
                $scale = 0;
            }

            $structure[] = array('name' => $name, 'type' => $type, 'width' => $width, 'scale' => $scale);
        }

        if (count($structure) == 0)
            return null;

        return $structure;
    }

    public function getTableRowCount(string $table) : int
    {
        $qtbl = self::quoteIdentifierIfNecessary($table);

        $res = $this->db->query("select count(*) as cnt from $qtbl");
        if (!$res)
            return -1;

        $rows = $res->fetchAll();
        return isset($rows[0]['cnt']) ? $rows[0]['cnt'] : null;
    }

    public function getIteratorFromHandle(string $handle) // TODO: add return type
    {
        $iter = new PostgresIterator;
        $iter->db = $this->db;
        $iter->fromHandle($handle);
        return $iter;
    }

    public function getPDO()
    {
        return $this->db;
    }

    public static function quoteIdentifierIfNecessary(string $str) : string
    {
        $str = str_replace('?', '', $str);

        if (false === strpbrk($str, "\"'-/\\!@#$%^&*() \t"))
            return $str;
             else
            return ('"' . $str . '"');
    }

    public static function createDelimitedFieldList(array $fields) : string
    {
        $result = '';
        $first = true;

        foreach ($fields as $f)
        {
            if (!$first)
                $result .= ',';

            if (isset($f['name']))
                $f = $f['name'];

            $result .= self::quoteIdentifierIfNecessary($f);
            $first = false;
        }

        return $result;
    }

    public static function base64url_encode($data) // TODO: add return type
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data) // TODO: add return type
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    private static function pgtypeToDbtype(int $pg_type) : string
    {
        switch ($pg_type)
        {
            case 16:  // boolean
                return 'boolean';

            case 17:  // bytea
            case 20:  // int8
            case 21:  // int2
            case 23:  // int4
            case 26:  // oid
                return 'integer';

            case 1700: // numeric
            case 790:  // money
                return 'numeric';

            case 700:  // float4
            case 701:  // float8
                return 'double';

            case 1114: // timestamp
            case 1184: // timestamptz
                return 'datetime';

            case 1083: // time
            case 1266: // timetz
            case 1082: // date
                return 'date';

            case 25:   // text
                return 'text';

            default:
            case 18:   // char
            case 1043: // varchar
                return 'character';
        }

        return 'invalid';
    }

    private static function getFieldString(array $field) : string
    {
        if (!isset($field['name']))
            return '';
        if (!isset($field['type']))
            return '';

        $name = $field['name'];
        $type = $field['type'];
        $width = isset($field['width']) ? $field['width'] : null;
        $scale = isset($field['scale']) ? $field['scale'] : null;

        $qname = self::quoteIdentifierIfNecessary($name);

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

            case 'double':
                return "$qname double precision";

            case 'real':
                return "$qname real";

            case 'integer':
                return "$qname integer";

            case 'date':
                return "$qname date";

            case 'timestamp':
            case 'datetime':
                return "$qname timestamp";

            case 'boolean':
                return "$qname boolean";

            case 'serial':
                return "$qname serial";

            case 'bigserial':
                return "$qname bigserial";
        }
    }
}



class PostgresIterator
{
    public $db;
    public $table;
    public $row_count = -1;
    public $primary_key = '';
    public $cursor_type;

    public $fetch_offset = 0;
    public $row_pos = 0;
    public $rows = [];
    public $last_block = false;

    public function fromHandle(string $handle) : bool
    {
        $this->cursor_type = substr($handle,0,3);

        if ($this->cursor_type == 'xh1')
        {
            $decoded = \Flexio\Services\Postgres::base64url_decode(substr($handle,3));
            $arr = explode("\t", $decoded);
            if (count($arr) != 3)
                return false;

            $this->table = $arr[0];
            $this->row_count = $arr[1];
            $this->primary_key = $arr[2];
            return true;
        }
    }

    public function getHandle() // TODO: add return type
    {
        return $this->cursor_type . \Flexio\Services\Postgres::base64url_encode($this->table . "\t" . $this->row_count . "\t" . $this->primary_key);
    }

    public function getRows(int $offset, int $limit) // TODO: add return type
    {
        if ($this->cursor_type == 'xh1')
        {
            // offset / limit
            $order = $this->primary_key;
            if (strlen($order) == 0)
                $order = 'ctid';

            $sql = 'select * from '. $this->table;
            $sql .= ' order by ' . $order;
            $sql .= ' offset ' . intval($offset);
            $sql .= ' limit ' . intval($limit);

            $t1 = (int)microtime(true);
            $stmt = $this->db->query($sql);
            $t2 = (int)microtime(true);

            if (isset($GLOBALS['g_config']->query_log))
            {
                $t1_micropart = sprintf("%06d", ($t1 - floor($t1)) * 1000000);
                $date = new \DateTime(date('Y-m-d H:i:s.' . $t1_micropart, $t1));
                $timestamp = $date->format("Y-m-d H:i:s.u");

                $log = ("Timestamp: $timestamp; Query time: " . sprintf("%0.4f sec", ($t2-$t1)) . "\nDATA: $sql\n\n");
                \Flexio\System\System::log($log);
            }

            return $stmt;
        }

        return null;
    }

    public function fetchRow() // TODO: add return type
    {
        $cnt = count($this->rows);

        if ($this->row_pos >= $cnt)
        {
            // need more data
            $this->rows = $this->getRows($this->fetch_offset, 1000);
            if ($this->rows)
            {
                $this->rows = $this->rows->fetchAll();
                $cnt = count($this->rows);
                $this->fetch_offset += $cnt;
                $this->row_pos = 0;
            }
             else
            {
                $this->rows = [];
                $this->row_pos = 0;
            }
        }

        if ($this->row_pos >= $cnt)
            return null;

        $this->row_pos++;
        return $this->rows[$this->row_pos - 1];
    }
}


class PostgresIteratorAll
{
    public $result;

    public function fetchRow() // TODO: add return type
    {
        $row = $this->result->fetch(\PDO::FETCH_ASSOC);
        unset($row['xdrowid']);
        return $row;
    }
}


/*
class PostgresInserterBulkCopy
{
    public $db;
    public $table = '';
    private $rows = array();
    private $stmt = null;
    private $fields = '';
    private $structure = null;
    private $columns = null;

    public function init($service, $db, $table) // TODO: add parameter type
    {
        $this->db = $db;
        $this->table = $table;
        $this->structure = $service->describeTable($table);
        if (!$this->structure)
            return false;
        return true;
    }

    public function startInsert(array $fields) : bool
    {
        $structure_indexed = [];
        foreach ($this->structure as $column)
            $structure_indexed[mb_strtolower($column['name'])] = $column;


        $this->columns = [];
        foreach ($fields as $field)
        {
            if (!array_key_exists(mb_strtolower($field), $structure_indexed))
            {
                // field not found
                return false;
            }

            $this->columns[] = $structure_indexed[mb_strtolower($field)];
        }

        $this->fields = \Flexio\Services\Postgres::createDelimitedFieldList($fields);
        return true;
    }

    public function insertRow(array $row) : bool
    {
        $field_idx = 0;
        $column_count = count($this->columns);

        foreach ($row as &$f)
        {
            if (false !== strpbrk($f, "\b\f\n\r\t\v"))
            {
                $f = strtr($f, array("\b" => "\\b", "\f" => "\\f", "\n" => "\\n", "\r" => "\\r", "\t" => "\\t", "\v" => "\\v"));
            }

            if (is_null($f))
            {
                $f = "\\N";
                ++$field_idx;
                continue;
            }

            if ($field_idx >= $column_count)
                break;

            // check field types
            switch ($this->columns[$field_idx]['type'])
            {
                case 'date':
                    if (strlen($f) < 10)
                        $f = "\\N";
                    break;
                case 'datetime':
                    if (strlen($f) < 19)
                        $f = "\\N";
                    break;
                case 'boolean':
                    $f = ($f ? 'TRUE':'FALSE');
                    break;
            }

            ++$field_idx;
        }

        $rowcolcnt = count($row);

        if ($rowcolcnt != $column_count)
        {
            while ($rowcolcnt < $column_count)
            {
                $row[] = "\\N";
                ++$rowcolcnt;
            }

            if ($rowcolcnt > $column_count)
            {
                array_splice($row,$column_count);
            }
        }

        $this->rows[] = join("\t", $row);

        if (count($this->rows) <= 1000)
            return true;

        return $this->flush();
    }

    public function flush() : bool
    {
        try
        {
            if (count($this->rows) > 0)
                $res = $this->db->pgsqlCopyFromArray($this->table, $this->rows, "\t", "\\\\N", $this->fields);

            $this->rows = [];

            file_put_contents('/tmp/abc', $this->table);
            return true;
        }
        catch (\Exception $e)
        {
            \Flexio\System\System::log('Could not flush rows. Exception message: ' . $e->getMessage());
            $this->rows = [];
            return false;
        }
    }

    public function finishInsert()
    {
        return $this->flush();
    }
}
*/



class PostgresInserterMultiRow
{
    // https://www.depesz.com/2007/07/05/how-to-insert-data-to-database-as-fast-as-possible

    public $db;
    public $table = '';
    private $rows = array();
    private $stmt = null;
    private $fields = '';
    private $structure = null;
    private $columns = null;

    public function init($service, $db, $table) : bool // TODO: add parameter type
    {
        $this->db = $db;
        $this->table = $table;
        $this->structure = $service->describeTable($table);
        if (!$this->structure)
            return false;
        return true;
    }

    public function startInsert(array $fields) : bool
    {
        $structure_indexed = [];
        foreach ($this->structure as $column)
            $structure_indexed[mb_strtolower($column['name'])] = $column;

        $this->columns = [];
        foreach ($fields as $field)
        {
            $cleaned_fieldname = mb_strtolower($field);
            if (!array_key_exists($cleaned_fieldname, $structure_indexed))
                return false;

            $this->columns[] = $structure_indexed[$cleaned_fieldname];
        }

        $this->fields = \Flexio\Services\Postgres::createDelimitedFieldList($fields);
        return true;
    }

    public function insertRow(array $row) : bool
    {
        $field_idx = 0;
        $column_count = count($this->columns);

        foreach ($row as &$f)
        {
            if (is_null($f))
            {
                $f = "null";
                ++$field_idx;
                continue;
            }

            if ($field_idx >= $column_count)
                break;

            // check field types
            switch ($this->columns[$field_idx]['type'])
            {
                case 'date':
                    if (strlen($f) < 10)
                        $f = "null";
                         else
                        $f = $this->db->quote($f);
                    break;
                case 'datetime':
                    if (strlen($f) < 19)
                        $f = "null";
                         else
                        $f = $this->db->quote($f);
                    break;
                case 'boolean':
                    $f = ($f ? 'TRUE':'FALSE');
                    $f = $this->db->quote($f);
                    break;

                default:
                    $f = $this->db->quote((string)$f);
                    break;
            }

            ++$field_idx;
        }

        $rowcolcnt = count($row);

        if ($rowcolcnt != $column_count)
        {
            while ($rowcolcnt < $column_count)
            {
                $row[] = "null";
                ++$rowcolcnt;
            }

            if ($rowcolcnt > $column_count)
            {
                array_splice($row,$column_count);
            }
        }

        $this->rows[] = $row;

        if (count($this->rows) <= 100)
            return true;

        return $this->flush();
    }

    public function flush() : bool
    {
        try
        {
            if (count($this->rows) > 0)
            {
                $sql = self::buildInsertStatement($this->table, $this->fields, $this->rows);
                $this->db->exec($sql);
            }

            $this->rows = [];
            return true;
        }
        catch (\Exception $e)
        {
            \Flexio\System\System::log('Could not flush rows. Exception message: ' . $e->getMessage());
            $this->rows = [];
            return false;
        }
    }

    public function finishInsert() : bool
    {
        return $this->flush();
    }

    private static function buildInsertStatement(string $table, string $fields, array $rows) : string
    {
        $sql = '';

        $sql .= "INSERT INTO $table ";
        $sql .= "($fields) ";
        $sql .= 'VALUES ';

        $first = true;
        foreach ($rows as $r)
        {
            if ($first !== true)
                $sql .= ',';

            $sql .= '(';
            $sql .= join(',', $r);
            $sql .= ')';

            $first = false;
        }

        return $sql;
    }
}


/*
class PostgresInserterSimple
{
    public $db;
    public $table = '';
    private $structure;
    private $rows = array();
    private $stmt = null;

    public function init($service, $db, $table) // TODO: add parameter type
    {
        $this->db = $db;
        $this->table = $table;
        $this->structure = $service->describeTable($table);
        if (!$this->structure)
            return false;
        return true;
    }

    public function startInsert(array $fields) : bool
    {
        $sql =  'insert into '. \Flexio\Services\Postgres::quoteIdentifierIfNecessary($this->table) . ' (';
        $sql .= \Flexio\Services\Postgres::createDelimitedFieldList($fields);
        $sql .= ') VALUES ( ' . trim(str_repeat('?,',count($fields)),',') . ')';

        $this->stmt = $this->db->prepare($sql);
        return true;
    }

    public function insertRow(array $row) : bool
    {
        // only take sequential arrays
        if (isset($row[0]))
        {
            $this->stmt->execute($row);
        }
        return true;
    }

    public function flush() : bool
    {
        return true;
    }

    public function finishInsert()
    {
        return $this->flush();
    }
}
*/



class PostgresBinaryFile
{
    public $db = null;
    public $f = null;

    function __destruct()
    {
        $this->db->commit();
    }

    public function read($length) // TODO: add return type
    {
        // for some reason, fread() has a 8192-byte limit,
        // so we use stream_get_contents
        $ret = stream_get_contents($this->f, $length);
        if (strlen($ret) == 0)
            return false;
        return $ret;
    }

    public function write($data) // TODO: add return type
    {
        return fwrite($this->f, $data);
    }

    public function seek($offset, $whence = SEEK_SET) // TODO: add return type
    {
        return fseek($this->f, $offset, $whence);
    }
}
