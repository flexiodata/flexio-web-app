<?php
/**
 *
 * Copyright (c) 2010-2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2010-03-10
 *
 * @package flexio
 * @subpackage Model
 */


class Registry extends ModelBase
{
    /**
     * gets a variable; if the setting doesn't exist,
     * the function returns null
     *
     * @param {string} $name; The name of the variable for which
     *                  to get the value
     *
     * @return {string}; The value of the variable
     */
    public function getString($object_eid, $name, $default_value = null)
    {
        $res = $this->getVariable($object_eid, $name);
        if (is_null($res))
            return $default_value;
             else
            return $res;
    }

    public function getNumber($object_eid, $name, $default_value = null)
    {
        $res = $this->getVariable($object_eid, $name);
        if (is_null($res))
            return $default_value;
        return 0 + $res;
    }

    public function getBoolean($object_eid, $name, $default_value = null)
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

    public function getBinary($object_eid, $name, &$mime_type)
    {
        try
        {
            $db = $this->getDatabase();
            if (!$db)
                return false;

            $qobject_eid = $db->quote(($object_eid ?? ''));
            $qname = $db->quote($name);
            $sql = "select value, mime_type from tbl_registry where object_eid = $qobject_eid and name = $qname";
            $result = $db->fetchRow($sql);

            $mime_type = '';

            // if we don't have a variable, return null
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

    public function getUpdateTime($object_eid, $name)
    {
        try
        {
            $db = $this->getDatabase();
            if (!$db)
                return false;

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

    public function setString($object_eid, $name, $value, $expire = null)
    {
        return $this->setVariable($object_eid, $name, $value, 'STRING', $expire, '');
    }

    public function setNumber($object_eid, $name, $value, $expire = null)
    {
        return $this->setVariable($object_eid, $name, $value, 'NUMBER', $expire, '');
    }

    public function setBoolean($object_eid, $name, $value, $expire = null)
    {
        return $this->setVariable($object_eid, $name, $value, 'BOOLEAN', $expire, '');
    }

    public function setBinary($object_eid, $name, $value, $expire = null, $mime_type = '')
    {
        return $this->setVariable($object_eid, $name, $value, 'BINARY', $expire, $mime_type);
    }

    /**
     * returns true if a registry entry exists, and false otherwise
     *
     * @param {string} $name; The name of the registry entry for
     *                  which to check for existence
     *
     * @return {boolean}; True if the registry entry with the given
     *                     name exists, and false otherwise
     */
    public function entryExists($object_eid, $name)
    {
        if (!self::checkInput($object_eid, $name))
            return false;

        $db = $this->getDatabase();
        $result = $db->fetchOne('select name from tbl_registry where object_eid = ' . $db->quote($object_eid) . ' and name = '. $db->quote($name));
        return ($result === false) ? false : true;
    }

    public function deleteEntryByName($object_eid, $name)
    {
        if (!self::checkInput($object_eid, $name))
            return false;

        $db = $this->getDatabase();
        $result = $db->exec('delete from tbl_registry where object_eid = ' . $db->quote($object_eid) . ' and name = '. $db->quote($name));
        return ($result !== false && $result > 0) ? true : false;
    }

    public function expireKey($object_eid, $name, $seconds_from_now = 0)
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

    public function cleanupExpiredEntries()
    {
        $db = $this->getDatabase();
        $db->exec('delete from tbl_registry where expires < now()');
        return true;
    }

   /**
     * sets a variable; if the variable doesn't exist, it is
     * added; if it already exists, the value is set to the
     * new value
     *
     * @param {string} $name; The name of the variable
     * @param {string} $value; The value of the variable
     *
     * @return {void}
     */
    public function setVariable($object_eid, $name, $value, $type = 'STRING', $expires = null, $mime_type = '', $db = null)
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

    /**
     * gets a variable; if the variable doesn't exist,
     * the function returns null
     *
     * @param {string} $name; The name of the variable for which
     *                  to get the value
     *
     * @return {string}; The value of the variable
     */
    public function getVariable($object_eid, $name, $db = null, $for_update = false)
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

    private static function checkInput($object_eid, $name)
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

    private static function checkExpiresValue($expires)
    {
        // expiration value should be a non-negative integer
        if (!is_int($expires))
            return false;

        if ($expires < 0)
            return false;

        return true;
    }
}
