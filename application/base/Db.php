<?php
/**
 *
 * Copyright (c) 2014, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2014-04-03
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class DbExpr
{
    protected $expr = null;

    public function __construct($expr)
    {
        $this->expr = $expr;
    }

    public function __toString()
    {
        return $this->expr;
    }
}

abstract class DbBase
{
    public abstract function getConnection();
    public abstract function exec($sql);
    public abstract function query($sql, $params);
    public abstract function fetchOne($sql, $params);
    public abstract function fetchRow($sql, $params);
    public abstract function fetchAll($sql, $params);
    public abstract function beginTransaction();
    public abstract function commit();
    public abstract function rollback();
    public abstract function quote($value);
    public abstract function quoteIdentifier($value);
    public abstract function quoteInto($statement, $params);

    // TODO: different interface?
    public abstract function insert($table, $values);
    public abstract function update($table, $values, $where);
    public abstract function delete($table, $where);
}

class Db extends DbBase
{
    protected $connection_type = null;
    protected $db_host = null;
    protected $db_port = null;
    protected $db_dbname = null;
    protected $db_username = null;
    protected $db_password = null;
    private $db = null;
    private $log_callback = null;

    public static function factory($type, $params)
    {
        // note: for now, we only support PDO_MYSQL
        if ($type != 'PDO_MYSQL' && $type != 'PDO_POSTGRES')
            throw new \Exception;

        $modeldb = new Db;

        $modeldb->connection_type = $type;
        $modeldb->db_host = $params['host'] ?? '';
        $modeldb->db_port = $params['port'] ?? '';
        $modeldb->db_dbname = $params['dbname'] ?? '';
        $modeldb->db_username = $params['username'] ?? '';
        $modeldb->db_password = $params['password'] ?? '';

        return $modeldb;
    }

    public static function createDbResult($obj, $db)
    {
        return new DbResult($obj, $db);
    }

    public static function select()
    {
        return new DbSelect;
    }

    public function setLogger($callback)
    {
        $this->log_callback = $callback;
    }

    public function doLog()
    {
        if (isset($this->log_callback))
            return true;

        return false;
    }

    public function log($text)
    {
        if ($this->doLog() === false)
            return;

        call_user_func($this->log_callback,$text);
    }

    public function getConnection()
    {
        if (isset($this->db))
            return $this->db;

        $dsn = '';
        if ($this->connection_type == 'PDO_MYSQL')
            $dsn = 'mysql:host=' . $this->db_host . ';port=' . $this->db_port . ';dbname=' . $this->db_dbname;
        else if ($this->connection_type == 'PDO_POSTGRES')
            $dsn = 'pgsql:host=' . $this->db_host . ';port=' . $this->db_port . ';dbname=' . $this->db_dbname;

        if ($this->doLog())
            $this->log("CONNECTING TO: $dsn\n\n");

        $this->db = new \PDO($dsn, $this->db_username, $this->db_password);

        // use default database case handling; require exceptions
        $this->db->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        return $this->db;
    }

    public function getConnectionType()
    {
        return $this->connection_type;
    }


    public function prepare($sql)
    {
        return $this->getConnection()->prepare($sql);
    }

    public function query($sql, $params = array())
    {
        if ($sql instanceof DbSelect)
            $sql = $sql->assemble();

        if (!is_array($params))
            $params = array($params);

        if ($this->doLog())
        {
            $t1 = (int)microtime(true);
            $result = $this->getConnection()->prepare($sql);
            $result->execute($params);
            $t2 = (int)microtime(true);

            $t1_micropart = sprintf("%06d", ($t1 - floor($t1)) * 1000000);
            $date = new \DateTime(date('Y-m-d H:i:s.' . $t1_micropart, $t1));
            $timestamp = $date->format("Y-m-d H:i:s.u");

            $this->log("Timestamp: $timestamp; Query time: " . sprintf("%0.4f sec; Db::query()", ($t2-$t1)) . "\n$sql\n\n");

            return $result;
        }
         else
        {
            $result = $this->getConnection()->prepare($sql);
            $result->execute($params);
            return $result;
        }
    }

    public function exec($sql)
    {
        if ($sql instanceof DbSelect)
            $sql = $statement->assemble();

        if ($this->doLog())
        {
            $t1 = (int)microtime(true);
            $result = $this->getConnection()->exec($sql);
            $t2 = (int)microtime(true);

            $t1_micropart = sprintf("%06d", ($t1 - floor($t1)) * 1000000);
            $date = new \DateTime(date('Y-m-d H:i:s.' . $t1_micropart, $t1));
            $timestamp = $date->format("Y-m-d H:i:s.u");

            $this->log("Timestamp: $timestamp; Query time: " . sprintf("%0.4f sec; Db::exec()", ($t2-$t1)) . "\n$sql\n\n");

            return $result;
        }
         else
        {
            return $this->getConnection()->exec($sql);
        }
    }

    public function fetchOne($sql, $params = array())
    {
        $iter = $this->query($sql, $params);
        return $iter->fetchColumn(0);
    }

    public function fetchRow($sql, $params = array())
    {
        $iter = $this->query($sql, $params);
        return $iter->fetch();
    }

    public function fetchAll($sql, $params = array())
    {
        $iter = $this->query($sql, $params);
        return $iter->fetchAll();
    }

    public function insert($table, $set, $ignore = false)
    {
        // TODO: additional input type checking

        if (!is_array($set))
            return false;

        if (count($set) == 0)
            return false;

        $qtable = $this->quoteIdentifier($table);
        $fields = '';
        $values = '';

        $first = true;
        foreach ($set as $f => $v)
        {
            if (!$first)
            {
                $fields .= ',';
                $values .= ',';
            }

            $first = false;
            $fields .= $this->quoteIdentifier($f);

            if (!($v instanceof DbExpr))
            {
                if (is_null($v))
                    $v = 'null';
                     else
                    $v = $this->quote($v);
            }

            $values .= $v;
        }

        $result = false;
        if ($this->connection_type == 'PDO_MYSQL')
        {
            if ($ignore)
                $update = "insert ignore into $qtable ($fields) values ($values)";
                 else
                $update = "insert into $qtable ($fields) values ($values)";
            $result = $this->exec($update);
        }
         else
        {
            if ($ignore)
            {
                try
                {
                    $update = "insert into $qtable ($fields) values ($values)";
                    $result = $this->exec($update);
                }
                catch (\Exception $e)
                {
                }
            }
             else
            {
                $update = "insert into $qtable ($fields) values ($values)";
                $result = $this->exec($update);
            }
        }

        // if no results are inserted or exec returns false,
        // then return false
        if ($result === 0 || $result === false)
            return false;

        return true;
    }

    public function update($table, $set, $where)
    {
        // TODO: additional input type checking

        if (!is_array($set))
            return false;

        if (count($set) == 0)
            return false;

        $qtable = $this->quoteIdentifier($table);
        $values = '';

        $first = true;
        foreach ($set as $f => $v)
        {
            if (!$first)
                $values .= ',';

            $first = false;

            if (is_null($v))
                $v = 'null';
            else if (!($v instanceof DbExpr))
                $v = $this->quote($v);

            $values .= $this->quoteIdentifier($f) . '=' . $v;
        }

        $update = "update $qtable set $values where $where";
        $result = $this->exec($update);

        // if no rows are affected, return false
        if ($result === 0 || $result === false)
            return false;

        return true;
    }

    public function delete($table, $where)
    {
        // TODO: input type checking

        $qtable = $this->quoteIdentifier($table);
        $delete = "delete from $qtable where $where";
        $result = $this->exec($delete);

        // if no rows are affected, return false
        if ($result === 0 || $result === false)
            return false;

        return true;
    }

    public function beginTransaction()
    {
        return $this->getConnection()->beginTransaction();
    }

    public function commit()
    {
        return $this->getConnection()->commit();
    }

    public function rollback()
    {
        return $this->getConnection()->rollback();
    }

    public function quote($value)
    {
        if (is_int($value) || is_float($value))
            return $value;

        return $this->getConnection()->quote($value);
    }

    public function quoteIdentifier($value)
    {
        // default quote type
        $quote_type = '"';

        // mysql uses a special type of quote
        if ($this->connection_type == 'PDO_MYSQL')
            $quote_type = '`';

        $value = str_replace("$quote_type", "$quote_type$quote_type", $value);
        $value = $quote_type . $value . $quote_type;

        return $value;
    }

    public function quoteInto($statement, $params)
    {
        // if $params is a string, convert to single element array
        $arr = array();
        if (is_array($params))
            $arr = $params;
              else
            $arr[0] = $params;

        foreach ($arr as $value)
        {
            $qvalue = $this->quote($value);
            $statement = preg_replace('/\?/', $qvalue, $statement, 1);
        }

        return $statement;
    }

}


abstract class DbResultBase
{
    public abstract function init();
    public abstract function fetch();
    public abstract function fetchAll();
    public abstract function columnCount();
    public abstract function getColumnMeta($coln);
    public abstract function toDelete($tbl);
    public abstract function showDebugInfo();
    public abstract function setStructure($structure);
    public abstract function getTotalCount();
}


class DbResult extends DbResultBase
{
    protected $select;
    protected $db;
    protected $result = null;
    protected $init_called = false;
    protected $to_delete = array();
    protected $all = null;
    protected $structure = null;
    protected $total_count = false;

    public function __construct($select, $db)
    {
        $this->select = $select;
        $this->db = $db;
    }

    public function init()
    {
        $this->result = $this->db->query($this->select);
        $this->init_called = true;
    }

    public function fetch()
    {
        if (!$this->init_called) $this->init();
        if (!isset($this->result)) return false;
        return $this->result->fetch();
    }

    public function fetchAll()
    {
        if (!$this->init_called) $this->init();
        if (!$this->all)
            $this->all = $this->result->fetchAll();
        return $this->all;
    }

    public function columnCount()
    {
        if (!$this->init_called) $this->init();
        if (!isset($this->result)) return 0;
        if (!$this->structure) $this->translateStructure();
        return count($this->structure);
    }

    public function getColumnMeta($coln)
    {
        if (!$this->init_called) $this->init();
        if (!isset($this->result)) return false;
        if (!$this->structure) $this->translateStructure();
        return $this->structure[$coln];
    }

    public function translateStructure()
    {
        $this->structure = array();

        $cnt = $this->result->columnCount();
        for ($i = 0; $i < $cnt; ++$i)
        {
            $col = $this->result->getColumnMeta($i);

            // defaults
            $field_info = array('name'  => $col['name'],
                                'type'  => 'character',
                                'width' => 1024,
                                'scale' => 0);

            if (isset($colinfo['native_type']))
            {
                switch ($colinfo['native_type'])
                {
                    default:
                    case 'BLOB':
                    case 'VAR_STRING':
                    case 'STRING':     $field_info['type'] = 'character'; break;
                    case 'NEWDECIMAL': $field_info['type'] = 'numeric'; break;
                    case 'LONG':       $field_info['type'] = 'integer'; break;
                }
            }

            if (isset($col['len']))
                $field_info['width'] = $col['len'];
            if ($field_info['width'] < 0)
                $field_info['width'] = 1024;
            if ($field_info['width'] > 4096)
                $field_info['width'] = 4096;

            if (isset($col['precision']))
                $field_info['scale'] = $col['precision'];

            $this->structure[] = $field_info;
        }
    }

    public function setStructure($structure)
    {
        $this->structure = $structure;
    }

    public function toDelete($tbl)
    {
        if (is_array($tbl))
        {
            foreach ($tbl as $k => $v)
                $this->to_delete[] = $v;
        }
         else
        {
            $this->to_delete[] = $tbl;
        }
    }

    public function showDebugInfo()
    {
        return \Flexio\Base\DbUtil::formatSQL($this->select->assemble());
    }

    function __destruct()
    {
        foreach ($this->to_delete as $k => $tbl)
            $this->db->exec("DROP TABLE IF EXISTS $tbl");
    }

    function getTotalCount()
    {
        return $this->total_count;
    }

    function setTotalCount($cnt)
    {
        $this->total_count = $cnt;
    }
}


class DbResultArray extends DbResultBase
{
    protected $arr;
    protected $metadata;
    protected $structure;

    public function __construct($arr = array(), $metadata = array())
    {
        $this->arr = $arr;
        reset($this->arr);

        $this->metadata = $metadata;
        if (count($metadata) == 0)
        {
            if (count($arr) > 0)
            {
                foreach ($arr[0] as $k => $v)
                {
                    if (is_int($v))
                    {
                        $this->metadata[] = array('name' => $k, 'type' => 'integer', 'width' => 14, 'scale' => 0);
                    }
                     else
                    {
                        $this->metadata[] = array('name' => $k, 'type' => 'character', 'width' => 80, 'scale' => 0);
                    }
                }
            }
        }
    }

    public function init()
    {
    }

    public function fetch()
    {
        $result = current($this->arr);
        next($this->arr);
        return $result;
    }

    public function fetchAll()
    {
        return $this->arr;
    }

    public function columnCount()
    {
        return count($this->metadata);
    }

    public function getColumnMeta($coln)
    {
        return $this->metadata[$coln];
    }

    public function setStructure($structure)
    {
        $this->metadata = $structure;
    }

    public function toDelete($tbl)
    {
        // no implementation for DbResultArray
    }

    public function showDebugInfo()
    {
        return var_export($this->arr, true);
    }

    function getTotalCount()
    {
        return false;
    }
}


class DbSelect
{
    public static function create()
    {
        return new DbSelect;
    }

    public function from($table, $columns = '*')
    {
        if (is_string($table))
        {
            $this->from = array('table' => $table);
        }
         else
        {
            foreach ($table as $k => $v)
                $this->from = array('alias' => $k, 'table' => $v);
        }

        $this->columns($columns);

        return $this;
    }

    public function distinct($state = true)
    {
        $this->distinct = $state;
        return $this;
    }

    public function join($table, $on, $columns = '*')
    {
        return $this->joinInner($table, $on, $columns);
    }

    public function joinInner($table, $on, $columns = '*')
    {
        if (is_string($table))
        {
            $this->joins[] = array('table' => $table, 'type' => 'INNER', 'on' => $on);
        }
         else
        {
            foreach ($table as $k => $v)
                $this->joins[] = array('alias' => $k, 'table' => $v, 'type' => 'INNER', 'on' => $on);
        }

        return $this;
    }

    public function joinLeft($table, $on, $columns = '*')
    {
        if (is_string($table))
        {
            $this->joins[] = array('table' => $table, 'type' => 'LEFT', 'on' => $on);
        }
         else
        {
            foreach ($table as $k => $v)
                $this->joins[] = array('alias' => $k, 'table' => $v, 'type' => 'LEFT', 'on' => $on);
        }

        return $this;
    }

    public function columns($columns)
    {
        if (is_string($columns))
        {
            $this->addSingleColumn($columns);
        }
         else
        {
            foreach ($columns as $k => $v)
            {
                if (is_numeric($k))
                    $this->addSingleColumn($v);
                     else
                    $this->addSingleColumnAs($v, $k);
            }
        }

        return $this;
    }

    protected function addSingleColumn($column)
    {
        $matches = array();
        if (preg_match('/^(.+)\s+AS\s+(.+)$/i', $column, $matches))
        {
            return $this->addSingleColumnAs($matches[1], $matches[2]);
        }
         else
        {
            return $this->addSingleColumnAs($column, null);
        }
    }

    protected function addSingleColumnAs($column, $as)
    {
        $arr = Db::parseExprOrColumn($column);
        if (isset($as))
            $arr['as'] = $as;

        $this->columns[] = $arr;

        return $this;
    }

    protected function parseExprOrColumn($value)
    {
        // check for expressions/functions
        if ($value instanceof DbExpr)
        {
            $arr = array('expr' => (string)$value);
        }
         else if (preg_match('/\(.*\)/', $value))
        {
            $arr = array('expr' => $value);
        }
         else
        {
            $matches = array();
            if (preg_match('/(.+)\.(.+)/', $value, $matches))
            {
                $arr = array(
                    'table' => $matches[1],
                    'column' => $matches[2]
                );
            }
             else
            {
                $arr = array(
                    'column' => $value
                );
            }
        }

        return $arr;
    }

    public function where($condition, $value = null)
    {
        $this->where[] = array(
            'oper' => 'AND',
            'condition' => Db::quoteParam($condition, $value)
        );

        return $this;
    }

    public function orWhere($condition, $value = null)
    {
        $this->where[] = array(
            'oper' => 'OR',
            'condition' => Db::quoteParam($condition, $value)
        );

        return $this;
    }

    public function group($value)
    {
        if (!is_array($value))
            $value = array($value);

        foreach ($value as $v)
        {
            $arr = Db::parseExprOrColumn($v);
            $this->groups[] = $arr;
        }

        return $this;
    }

    public function order($value)
    {
        if (!is_array($value))
            $value = array($value);

        foreach ($value as $v)
        {
            $asc = true;

            $v = trim($v);
            $matches = array();
            if (preg_match('/^(.+)\s+(DESC|ASC)$/i', $v, $matches))
            {
                $v = $matches[1];
                if (strtoupper($matches[2]) == 'DESC')
                    $asc = false;
            }

            $arr = Db::parseExprOrColumn($v);
            if ($asc)
                $arr['direction'] = 'ASC';
                 else
                $arr['direction'] = 'DESC';

            $this->orders[] = $arr;
        }

        return $this;
    }

    public function limit($limit, $offset = null)
    {
        // if the limit or the offset are set and are anything other
        // a whole number, set them to null so the database statement
        // is constructed correctly
        if (isset($limit) && (!is_int($limit) || $limit < 0))
            $limit = null;
        if (isset($offset) && (!is_int($offset) || $offset < 0))
            $offset = null;

        // if we have an offset, we need a limit; set the limit to
        // the max value
        if (!isset($limit) && isset($offset))
            $limit = PHP_INT_MAX;

        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function assemble()
    {
        $sql = 'SELECT ';

        if ($this->distinct)
            $sql .= 'DISTINCT ';

        foreach ($this->columns as $k => $v)
        {
            if ($k > 0)
                $sql .= ', ';

            if (isset($v['expr']))
            {
                $sql .= $v['expr'];
            }
             else
            {
                if (isset($v['table']))
                    $sql .= Db::quoteIdentifier($v['table']) . '.';

                if ($v['column'] == '*')
                    $sql .= '*';
                     else
                    $sql .= Db::quoteIdentifier($v['column']);
            }

            if (isset($v['as']))
            {
                if (isset($v['as']) && isset($v['column']) && $v['as'] == $v['column'])
                {
                    // 'as' value is the same as the column name, so don't output 'AS ...'
                }
                 else
                {
                    $sql .= ' AS ' . Db::quoteIdentifier($v['as']);
                }
            }
        }


        $sql .= ' FROM ';
        if ($this->from['table'] instanceof DbSelect)
            $sql .= '(' . $this->from['table']->assemble() . ')';
             else
            $sql .= Db::quoteIdentifier($this->from['table']);
        if (isset($this->from['alias']))
        {
            $sql .= ' AS ' . Db::quoteIdentifier($this->from['alias']);
        }


        foreach ($this->joins as $v)
        {
            $sql .= (' ' . $v['type'] . ' JOIN ');

            if ($v['table'] instanceof DbSelect)
                $sql .= '(' . $v['table']->assemble() . ')';
            else if ($v['table'] instanceof DbExpr)
                $sql .= $v['table'];
            else
                $sql .= Db::quoteIdentifier($v['table']);

            if (isset($v['alias']))
                $sql .= ' AS ' . Db::quoteIdentifier($v['alias']);

            $sql .= ' ON '. $v['on'];
        }



        if (count($this->where) > 0)
        {
            $sql .= ' WHERE';
            foreach ($this->where as $k => $v)
            {
                if ($k > 0)
                    $sql .= ' ' . $v['oper'] . ' ';
                     else
                    $sql .= ' ';

                $sql .= '(' . $v['condition'] . ')';
            }
        }


        if (count($this->groups) > 0)
        {
            $sql .= ' GROUP BY';
            foreach ($this->groups as $k => $v)
            {
                if ($k > 0)
                    $sql .= ', ';
                     else
                    $sql .= ' ';

                if (isset($v['expr']))
                {
                    $sql .= $v['expr'];
                }
                 else
                {
                    if (isset($v['table']))
                        $sql .= Db::quoteIdentifier($v['table']) . '.';
                    $sql .= Db::quoteIdentifier($v['column']);
                }
            }
        }



        if (count($this->orders) > 0)
        {
            $sql .= ' ORDER BY';
            foreach ($this->orders as $k => $v)
            {
                if ($k > 0)
                    $sql .= ', ';
                     else
                    $sql .= ' ';

                if (isset($v['expr']))
                {
                    $sql .= $v['expr'];
                }
                 else
                {
                    if (isset($v['table']))
                        $sql .= Db::quoteIdentifier($v['table']) . '.';
                    $sql .= Db::quoteIdentifier($v['column']);
                }

                if (isset($v['direction']))
                {
                    $sql .= ' ' . $v['direction'];
                }
            }
        }


        if (!is_null($this->limit))
            $sql .= ' LIMIT '. intval($this->limit);

        if (!is_null($this->offset))
            $sql .= ' OFFSET '. intval($this->offset);

        return $sql;
    }

    public static function quote($value, $type = 'STRING')
    {
        if (is_array($value))
        {
            foreach ($value as &$v)
                $v = Db::quote($v, $type);
            return implode(', ', $value);
        }

        if (is_int($value))
        {
            return $value;
        }
         else if (is_float($value))
        {
            return $value;
        }
         else
        {
            return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
        }
    }

    public static function quoteIdentifier($value)
    {
        return '`' . str_replace('`', '``', $value) . '`';
    }

    public static function quoteParam($str, $value, $type = 'STRING')
    {
        $result = '';

        while (true)
        {
            $pos = strpos($str, '?');
            if ($pos === false)
            {
                $result .= $str;
                return $result;
            }

            $result .= substr($str, 0, $pos);
            $result .= Db::quote($value, $type);
            $str = substr($str, $pos+1);
        }
    }

    public function __toString()
    {
        return $this->assemble();
    }


    protected $from = null;
    protected $joins = array();
    protected $columns = array();
    protected $where = array();
    protected $orders = array();
    protected $groups = array();
    protected $limit = null;
    protected $offset = null;
    protected $distinct = false;
}
