<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-02-11
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class MySql implements \Flexio\Services\IConnection
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////

    private $is_ok = false;
    private $host = '';
    private $port = '';
    private $database = '';
    private $user = '';
    private $password = '';
    private $db = null;
    private $dbresult = null;
    private $dbtablestructure = null;
    private $rowbuffersize = 100;


    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create(array $params = null) : \Flexio\Services\MySql
    {
        $service = new self;

        if (isset($params))
            $service->connect($params);

        return $service;
    }

    public function connect(array $params) : bool
    {
        $this->close();

        if (isset($params['port']))
            $params['port'] = (string)$params['port'];

        $validator = \Flexio\Base\Validator::getInstance();
        if (($params = $validator->check($params, array(
                'host' => array('type' => 'string', 'required' => true),
                'port' => array('type' => 'string', 'required' => true),
                'username' => array('type' => 'string', 'required' => true),
                'password' => array('type' => 'string', 'required' => true),
                'database' => array('type' => 'string', 'required' => true),
                'path' => array('type' => 'string', 'required' => false, 'default' => '')
            ))) === false)
            return false;

        $this->initialize($params['host'], intval($params['port']), $params['database'], $params['username'], $params['password']);
        $this->dbtable = $params['path'];

        return $this->isOk();
    }

    public function isOk() : bool
    {
        return $this->is_ok;
    }

    public function close()
    {
        $this->is_ok = false;
        $this->host = '';
        $this->port = '';
        $this->database = '';
        $this->user = '';
        $this->password = '';
        $this->db = null;
        $this->dbresult = null;
        $this->dbtablestructure = null;
        $this->rowbuffersize = 100;
    }

    public function listObjects(string $path = '') : array
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
                'root' => 'mysql'
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

    public function getInfo(string $path) : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return array();
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


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    private function initialize(string $host, int $port, string $database, string $username, string $password)
    {
        $this->close();

        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;

        $this->db = $this->newConnection();

        if (!is_null($this->db))
            $this->is_ok = true;

        return $this->is_ok;
    }

    private function newConnection()
    {
        // connect to the database
        try
        {
            $mysqli = @new \mysqli($this->host,
                                 $this->username,
                                 $this->password,
                                 $this->database,
                                 $this->port);

            if ($mysqli->connect_error)
            {
                return null;
                //$errno = $mysqli->connect_errno;
                //$errstr = $mysqli->connect_error;
            }

            return $mysqli;
        }
        catch (\Exception $e)
        {
        }

        return null;
    }

    public function queryAll(string $table)
    {
        $sql = "select * from " . self::quoteIdentifier($table);

        $iter = new \Flexio\Services\MysqlIteratorAll;
        $iter->result = $this->db->query($sql);

        if (!$iter->result)
            return null;

        return $iter;
    }

    public function createTable(string $table, array $structure) : bool
    {
        if (!$this->isOk())
            return false;

        $this->dbtable = $table;

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
            $qdbtable = self::quoteIdentifier($table);

            $sql = "drop table if exists $qdbtable";
            if (false === $this->db->query($sql))
                return false;

            $sql = "create table $qdbtable ($fieldsql) engine=InnoDB default charset=utf8";

            if (false === $this->db->query($sql))
                return false;
        }
        catch (\Exception $e)
        {
             return false;
        }

        return true;
    }

    public function bulkInsert(string $table)
    {
        $inserter = new MysqlInserter;
        if (!$inserter->init($this, $this->db, $table))
            return false;
        return $inserter;
    }

    public function describeTable(string $table)
    {
        // STEP 1: query the database for the structure and the rows
        $structure = array();

        try
        {
            $qtable = self::quoteIdentifier($table);

            // get the structure
            $sql = "describe $qtable";
            $result = $this->db->query($sql);


            while ($result && ($row = $result->fetch_assoc()))
            {
                self::getFieldInfo($row, $name, $type, $width, $scale);

                $field = array();
                $field['name'] = $name; // TODO: convert the field names? e.g. lowercase?
                $field['type'] = $type;
                $field['width'] = $width;
                $field['scale'] = $scale;
                $structure[] = $field;
            }
        }
        catch (\Exception $e)
        {
            return false;
        }

        return $structure;
    }

    public function getTableRowCount(string $table) : int
    {
        $db = $this->newConnection();
        if (!$db)
            return -1;

        $qtbl = self::quoteIdentifier($table);

        $res = $db->query("select count(*) as cnt from $qtbl");
        if (!$res)
            return -1;

        $result = $res->fetch_assoc();
        return isset($result['cnt']) ? $result['cnt'] : null;
    }

    public function fetchRow()
    {
        return $this->dbresult->fetch_assoc();
    }

    private static function getFieldInfo(array $info, string &$name, string &$type, int &$width, int &$scale)
    {
        // MySQL types (from the create statement documentation)
        // note: describe table returns these in lowercase; when
        // determining the type from the describe table results,
        // we'll perform a case insensitive match (/i):
        //
        // TINYINT(length)
        // SMALLINT(length)
        // MEDIUMINT(length)
        // INT(length)  // alias for INTEGER
        // INTEGER(length)
        // BIGINT(length)
        // REAL(length, decimals)
        // DOUBLE(length, decimals)
        // FLOAT(length, decimals)
        // DECIMAL(length, decimals)
        // NUMERIC(length, length)
        // DATE
        // TIME
        // TIMESTAMP
        // DATETIME
        // YEAR
        // CHAR(length)
        // VARCHAR(length)
        // BINARY(length)
        // VARBINARY(length)
        // TINYBLOB
        // BLOB
        // MEDIUMBLOB
        // LONGBLOB
        // TINYTEXT
        // TEXT
        // MEDIUMTEXT
        // LONGTEXT
        // ENUM(value1,value2,value3,...)
        // SET(value1,value2,value3,...)

        // split the output field info into parts
        $name = '';
        $type = '';
        $width = 0;
        $scale = 0;

        $name = $info['Field'];

        // STEP 1: handle character types

        // VARCHAR, CHAR
        if (preg_match('/^((?:var)?char)\((\d+)\)/i', $info['Type'], $matches))
        {
            $type = 'character';
            $width = intval($matches[2]);
            $scale = 0;
            return;
        }

        // LONGTEXT, MEDIUM, TINYTEXT, TEXT
        if (preg_match('/^((?:long|medium|tiny)?text)/i', $info['Type'], $matches))
        {
            $type = 'character';    // TODO: equivalent to text?
            $width = 255;           // TODO: suitable default width
            $scale = 0;
            return;
        }

        // ENUM, SET
        if (preg_match('/^(enum|set)/i', $info['Type'], $matches))
        {
            $type = 'character';    // TODO: suitable for all types of enumeration?
            $width = 255;           // TODO: suitable for all widths of enumerated values?
            $scale = 0;
            return;
        }


        // STEP 2: handle numeric types

        // REAL, DECIMAL, NUMERIC
        if (preg_match('/^(real|decimal|numeric)\((\d+),(\d+)\)/i', $info['Type'], $matches))
        {
            $type = 'numeric';
            $width = intval($matches[2]);   // TODO: large enough to accomodate real?
            $scale = intval($matches[3]);
            return;
        }

        // DOUBLE, FLOAT
        if (preg_match('/^(double|float)\((\d+),(\d+)\)/i', $info['Type'], $matches))
        {
            $type = 'double';
            $width = intval($matches[2]);
            $scale = intval($matches[3]);
            return;
        }

        // BIGINT, MEDIUMINT, SMALLINT, TINYINT, INT
        if (preg_match('/^((?:big|medium|small|tiny)?int)\((\d+)\)/i', $info['Type'], $matches))
        {
            $type = 'integer';
            $width = 4;             // TODO: need more space for all types
            $scale = 0;
            return;
        }

        // STEP 3: handle date types

        // DATE, TIME
        if (preg_match('/^(date|time)/i', $info['Type'], $matches))
        {
            $type = 'date';         // TODO: is this a suitable representation for the TIME type?
            $width = 4;
            $scale = 0;
            return;
        }

        // DATETIME, TIMESTAMP
        if (preg_match('/^(datetime|timestamp)/i', $info['Type'], $matches))
        {
            $type = 'datetime';     // TODO: is this a suitable representation for the TIME type?
            $width = 8;
            $scale = 0;
            return;
        }

        // TIME
        if (preg_match('/^(time)/i', $info['Type'], $matches))
        {
            $type = 'character';    // TODO: is this a suitable representation for the TIME type?
            $width = 10;
            $scale = 0;
            return;
        }

        // YEAR
        if (preg_match('/^(year)/i', $info['Type'], $matches))
        {
            $type = 'integer';
            $width = 4;
            $scale = 0;
            return;
        }


        // STEP 4: handle binary types

        // BINARY, VARBINARY
        if (preg_match('/^((?:var)?binary)\((\d+)\)/i', $info['Type'], $matches))
        {
            // TODO: fill out
        }

        // LONGBLOB, MEDIUMBLOB, TINYBLOB, BLOB
        if (preg_match('/^((?:long|medium|tiny)?blob)/i', $info['Type'], $matches))
        {
            // TODO: fill out
        }
    }

    public static function quoteIdentifier(string $str) : string
    {
        return '`' . str_replace('`', '``',$str) . '`';
    }

    private static function getFieldString(string $field) : string
    {
        $name = $field['name'];
        $type = $field['type'];
        $width = isset($field['width']) ? $field['width'] : null;
        $scale = isset($field['scale']) ? $field['scale'] : null;


        $qname = self::quoteIdentifier($name);

        switch ($type)
        {
            default:
                return '';

            case 'character':
            case 'widecharacter':
                return "$qname varchar($width)";

            case 'numeric':
                if (is_null($scale))
                    return "$qname numeric";
                     else
                    return "$qname numeric($width,$scale)";

            case 'double':
                if (is_null(width))
                    return "$qname double";
                if (is_null($scale))
                    $scale = 0;
                return "$qname double($width,$scale)";

            case 'integer':
                return "$qname integer($width)";

            case 'date':
                return "$qname date";

            case 'datetime':
                return "$qname datetime";

            case 'boolean':
                return "$qname boolean";
        }
    }
}


class MysqlIteratorAll
{
    public $result;

    public function fetchRow()
    {
        $row = $this->result->fetch_assoc();
        unset($row['xdrowid']);
        return $row;
    }
}



class MysqlInserter
{
    public $db;
    public $table = '';
    private $structure = null;
    private $rows = array();
    private $stmt = null;
    private $fields = '';
    private $columns = null;

    public function init($service, $db, $table) // TODO: add parameter/return types
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
        $this->fields = '';

        $structure_indexed = [];
        foreach ($this->structure as $column)
            $structure_indexed[strtolower($column['name'])] = $column;

        $this->columns = [];
        foreach ($fields as $field)
        {
            if (!array_key_exists(strtolower($field), $structure_indexed))
            {
                // field not found
                return false;
            }

            $this->columns[] = $structure_indexed[strtolower($field)];

            if (strlen($this->fields) > 0)
                $this->fields .= ',';

            $this->fields .= \Flexio\Services\MySql::quoteIdentifier($field);
        }

        return true;
    }

    public function insertRow(array $row) : bool
    {
        $field_idx = 0;
        foreach ($row as &$f)
        {
            if (is_null($f))
            {
                $f = "NULL";
                ++$field_idx;
                continue;
            }

            switch ($this->columns[$field_idx]['type'])
            {
                case 'integer': // (handles boolean too)
                    if (is_bool($f))
                        $f = ($f ? '1':'0');
                    else
                        $f = (string)$f;
                    break;
                default:
                    if (is_null($f))
                        $f = 'NULL';
                         else
                        $f = "'" . $this->db->real_escape_string($f) . "'";
                    break;
            }

            ++$field_idx;
        }

        $this->rows[] = join(",", $row);

        if (count($this->rows) > 1000)
        {
            $this->flush();
        }

        return true;
    }

    public function flush()
    {
        if (count($this->rows) > 0)
        {
            $sql = "INSERT INTO " . \Flexio\Services\MySql::quoteIdentifier($this->table) . " VALUES ";

            $cnt = 0;
            foreach ($this->rows as $row)
            {
                if ($cnt > 0)
                    $sql .= ',(';
                     else
                    $sql .= '(';

                $sql .= $row;
                $sql .= ')';
                ++$cnt;
            }

            $this->db->query($sql);
        }

        $this->rows = [];
    }

    public function finishInsert()
    {
        $this->flush();
    }
}
