<?php
/**
 *
 * Copyright (c) 2010-2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2010-03-10
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Registry extends ModelBase
{
    public function getString(string $object_eid, string $name, string $default_value = null) : ?string
    {
        $res = $this->getVariable($object_eid, $name);
        if (is_null($res))
            return $default_value;
             else
            return $res;
    }

    public function getNumber(string $object_eid, string $name, float $default_value = null) : ?float
    {
        $res = $this->getVariable($object_eid, $name);
        if (is_null($res))
            return $default_value;
        return 0.0 + $res;
    }

    public function getBoolean(string $object_eid, string $name, bool $default_value = null) : ?bool
    {
        $res = $this->getVariable($object_eid, $name);
        if (is_null($res))
            return $default_value;

        if (is_bool($res))
            return $res;

        if ($res == '1' || $res == 'true')
            return true;
             else
            return false;
    }

    public function getBinary(string $object_eid, string $name, &$mime_type) : ?string
    {
        try
        {
            $db = $this->getDatabase();
            if (!$db)
                return null;

            $qobject_eid = $db->quote(($object_eid ?? ''));
            $qname = $db->quote($name);
            $sql = "select value, mime_type from tbl_registry where object_eid = $qobject_eid and name = $qname";
            $result = $db->fetchRow($sql);

            $mime_type = '';

            if ($result)
            {
                $mime_type = $result['mime_type'];
                return base64_decode($result['value']);
            }
        }
        catch (\Exception $e)
        {
        }

        return null;
    }

    public function getUpdateTime(string $object_eid, string $name) : ?string
    {
        try
        {
            $db = $this->getDatabase();
            if (!$db)
                return null;

            $qobject_eid = $db->quote(($object_eid ?? ''));
            $qname = $db->quote($name);
            $sql = "select updated from tbl_registry where object_eid = $qobject_eid and name = $qname";
            $result = $db->fetchRow($sql);

            if (isset($result['updated']))
                return $result['updated'];
        }
        catch (\Exception $e)
        {
        }

        return null;
    }

    public function setString(string $object_eid, string $name, string $value, int $expire = null) : bool
    {
        return $this->setVariable($object_eid, $name, $value, 'STRING', $expire, '');
    }

    public function setNumber(string $object_eid, string $name, float $value, int $expire = null) : bool
    {
        return $this->setVariable($object_eid, $name, $value, 'NUMBER', $expire, '');
    }

    public function setBoolean(string $object_eid, string $name, bool $value, int $expire = null) : bool
    {
        return $this->setVariable($object_eid, $name, $value, 'BOOLEAN', $expire, '');
    }

    public function setBinary(string $object_eid, string $name, string $value, int $expire = null, string $mime_type = '') : bool
    {
        return $this->setVariable($object_eid, $name, $value, 'BINARY', $expire, $mime_type);
    }

    public function entryExists(string $object_eid, string $name) : bool
    {
        if (!self::checkInput($object_eid, $name))
            return false;

        $db = $this->getDatabase();
        $result = $db->fetchOne('select name from tbl_registry where object_eid = ' . $db->quote($object_eid) . ' and name = '. $db->quote($name));
        return ($result === false) ? false : true;
    }

    public function deleteEntryByName(string $object_eid, string $name) : bool
    {
        if (!self::checkInput($object_eid, $name))
            return false;

        $db = $this->getDatabase();
        $result = $db->exec('delete from tbl_registry where object_eid = ' . $db->quote($object_eid) . ' and name = '. $db->quote($name));
        return ($result !== false && $result > 0) ? true : false;
    }

    public function expireKey(string $object_eid, string $name, int $seconds_from_now = 0) : bool
    {
        if (!self::checkInput($object_eid, $name))
            return false;
        if (!self::checkExpiresValue($seconds_from_now))
            return false;

        $db = $this->getDatabase();
        $qobject_eid = $db->quote($object_eid);
        $qname = $db->quote($name);
        $qtimestamp = $db->quote(\Flexio\System\System::getTimestamp());
        $qseconds = $db->quote((int)$seconds_from_now);

        $db->exec("update tbl_registry set updated=$qtimestamp,expires=(now() + interval '$qseconds seconds') where object_eid=$qobject_eid and name=$qname");

        return true;
    }

    public function cleanupExpiredEntries() : bool
    {
        $db = $this->getDatabase();
        $db->exec('delete from tbl_registry where expires < now()');
        return true;
    }

    public function purge(string $object_eid) : bool
    {
        // this function deletes rows associated for a given object
        if (!\Flexio\Base\Eid::isValid($object_eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            $qobject_eid = $db->quote($object_eid);
            $sql = "delete from tbl_registry where object_eid = $qobject_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function setVariable(string $object_eid, string $name, /* variable */ $value, string $type = 'STRING', int $expires = null, string $mime_type = '', $db = null) : bool
    {
        if (!self::checkInput($object_eid, $name))
            return false;
        if (isset($expires) && !self::checkExpiresValue($expires))
            return false;

        // get the database
        if (!isset($db))
            $db = $this->getDatabase();

        switch ($type)
        {
            case 'STRING':
                $qvalue_type = $db->quote(\Model::REGISTRY_VALUE_STRING);
                $qvalue = $db->quote($value);
                break;

            case 'NUMBER':
                $qvalue_type = $db->quote(\Model::REGISTRY_VALUE_NUMBER);
                $qvalue = $db->quote((double)$value);
                break;

            case 'BOOLEAN':
                $qvalue_type = $db->quote(\Model::REGISTRY_VALUE_BOOLEAN);
                if (is_string($value))
                {
                    if ($value == 'true')
                        $value = true;
                         else
                        $value = false;
                }
                $qvalue = $db->quote($value ? 'true' : 'false');
                break;

            case 'DATE':
                $qvalue_type = $db->quote(\Model::REGISTRY_VALUE_DATE);
                $qvalue = $db->quote($value);
                break;

            case 'DATETIME':
                $qvalue_type = $db->quote(\Model::REGISTRY_VALUE_DATETIME);
                $qvalue = $db->quote($value);
                break;

            case 'BINARY':
                $qvalue_type = $db->quote(\Model::REGISTRY_VALUE_BINARY);
                $qvalue = "'" . base64_encode($value) . "'";
                break;
        }

        $qobject_eid = $db->quote($object_eid);
        $qname = $db->quote($name);
        $qmime_type = $db->quote($mime_type);

        $expiresset = 'null';
        if (isset($expires))
        {
            $expiresset = "(now() + interval '$expires seconds')";
        }


        $qtimestamp = $db->quote(\Flexio\System\System::getTimestamp());

        try
        {
            $rows_updated = $db->exec("update tbl_registry set
                           value=$qvalue,
                           value_type=$qvalue_type,
                           expires=$expiresset,
                           mime_type=$qmime_type,
                           updated=$qtimestamp where object_eid=$qobject_eid and name=$qname");

            if ($rows_updated > 0)
                return true;

            $db->exec("insert into tbl_registry
                           (object_eid, name, value, value_type, mime_type,
                            expires, created, updated)
                       values
                           ($qobject_eid, $qname, $qvalue, $qvalue_type, $qmime_type,
                            $expiresset, $qtimestamp, $qtimestamp)
                      ");
        }
        catch (\Exception $e)
        {
            return false;
        }

        return true;
    }

    public function getVariable(string $object_eid, string $name, $db = null, bool $for_update = false) : ?string
    {
        if (!self::checkInput($object_eid, $name))
            return null;

        try
        {
            if (!isset($db))
                $db = $this->getDatabase();

            $qobject_eid = $db->quote(($object_eid ?? ''));
            $qname = $db->quote($name);

            $sql = "select value, value_type from tbl_registry where object_eid = $qobject_eid and name = $qname";
            if ($for_update)
                $sql .= " for update";

            $result = $db->fetchRow($sql);

            // if we don't have a variable, return null
            if (!$result)
                return null;

            // return the variable
            if ($result['value_type'] == \Model::REGISTRY_VALUE_BINARY)
                return base64_decode($result['value']);
                 else
                return $result['value'];
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    private static function checkInput(string $object_eid, string $name) : bool
    {
        // registry names should always be non-empty strings;
        // registry objects may be specified; if they are, they should
        // be eids; if they are not specified, they should be empty
        // strings
        if (!is_string($name) || strlen($name) == 0)
            return false;

        if (!is_string($object_eid))
            return false;

        if (strlen($object_eid) > 0 && !\Flexio\Base\Eid::isValid($object_eid))
            return false;

        return true;
    }

    private static function checkExpiresValue(int $expires) : bool
    {
        if ($expires < 0)
            return false;

        return true;
    }
}
