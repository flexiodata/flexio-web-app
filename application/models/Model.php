<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2012-01-06
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);
require_once __DIR__ . '/../base/Db.php';
require_once 'ModelBase.php';


class Model
{
    const TYPE_UNDEFINED      = '';
    const TYPE_OBJECT         = 'OBJ';
    const TYPE_USER           = 'USR';
    const TYPE_PIPE           = 'PIP';
    const TYPE_STREAM         = 'STR';
    const TYPE_CONNECTION     = 'CTN';
    const TYPE_COMMENT        = 'CMT';
    const TYPE_PROCESS        = 'PRC';
    const TYPE_TOKEN          = 'ATH';
    const TYPE_RIGHT          = 'ACL';

    const EDGE_UNDEFINED     = '';     // undefind edge
    const EDGE_CREATED       = 'CRT';  // user A created object B
    const EDGE_CREATED_BY    = 'CRB';  // object A was created by user B
    const EDGE_OWNS          = 'OWN';  // user A owns object B
    const EDGE_OWNED_BY      = 'OWB';  // object A is owned by user B
    const EDGE_INVITED       = 'INV';  // user A invited user B
    const EDGE_INVITED_BY    = 'INB';  // user A was invited by user B
    const EDGE_SHARED_WITH   = 'SHW';  // user A shared with user B
    const EDGE_SHARED_FROM   = 'SHF';  // user A was shared something from user B
    const EDGE_FOLLOWING     = 'FLW';  // user A is following object B
    const EDGE_FOLLOWED_BY   = 'FLB';  // object A is followed by user B
    const EDGE_MEMBER_OF     = 'MBO';  // object A is a member of object B
    const EDGE_HAS_MEMBER    = 'HMB';  // object A has member object B
    const EDGE_LINKED_TO     = 'LKT';  // object A links to object B
    const EDGE_LINKED_FROM   = 'LKF';  // object A links from object B
    const EDGE_COPIED_TO     = 'CPT';  // object A copied to object B
    const EDGE_COPIED_FROM   = 'CPF';  // object A copied from object B
    const EDGE_COMMENT_ON    = 'CMO';  // comment A is a comment on object B
    const EDGE_HAS_COMMENT   = 'HCM';  // object A has comment B
    const EDGE_PROCESS_OF    = 'PRO';  // object A is a process of object B
    const EDGE_HAS_PROCESS   = 'HPR';  // object A has process that is object B
    const EDGE_STORE_FOR     = 'STF';  // object A is a store for object B
    const EDGE_HAS_STORE     = 'HST';  // object A has store a store that is object B

    const STATUS_UNDEFINED = '';
    const STATUS_PENDING   = 'P';
    const STATUS_AVAILABLE = 'A';
    const STATUS_TRASH     = 'T';
    const STATUS_DELETED   = 'D';

    const ACCESS_CODE_TYPE_UNDEFINED = '';
    const ACCESS_CODE_TYPE_EID       = 'EID';
    const ACCESS_CODE_TYPE_TOKEN     = 'TKN';
    const ACCESS_CODE_TYPE_EMAIL     = 'EML';
    const ACCESS_CODE_TYPE_CATEGORY  = 'CAT';

    const REGISTRY_VALUE_UNDEFINED = '';
    const REGISTRY_VALUE_STRING    = 'S';
    const REGISTRY_VALUE_NUMBER    = 'N';
    const REGISTRY_VALUE_BOOLEAN   = 'B';
    const REGISTRY_VALUE_DATE      = 'D'; // (YYYY-MM-DD)
    const REGISTRY_VALUE_DATETIME  = 'T'; // (YYYY-MM-DD HH:MM:SS)
    const REGISTRY_VALUE_BINARY    = 'X';

    const CONNECTION_STATUS_UNDEFINED   = '';
    const CONNECTION_STATUS_AVAILABLE   = 'A';
    const CONNECTION_STATUS_UNAVAILABLE = 'U';
    const CONNECTION_STATUS_ERROR       = 'E';

    const PIPE_STATUS_UNDEFINED  = '';
    const PIPE_STATUS_ACTIVE     = 'A';
    const PIPE_STATUS_INACTIVE   = 'I';

    private $objs = array();
    private $database = null;
    private $dbtype = '';


    public function __construct()
    {
        $this->dbtype = $GLOBALS['g_config']->directory_database_type;
    }

    public function __get($name)
    {
        if (isset($this->objs[$name]))
            return $this->objs[$name];

        $classname = ucfirst($name);

        require_once __DIR__ . '/' . $this->dbtype . '/' . $classname . '.php';

        if (IS_DEBUG())
        {
            if (!class_exists($classname))
            {
                echo "CANNOT LOAD CLASS $name\n";
                debug_print_backtrace();
                die();
            }
        }

        $obj = new $classname;
        $obj->setModel($this);
        $this->objs[$name] = $obj;

        return $obj;
    }

    public function create(string $type, array $params = null) : string
    {
        if ($type === \Model::TYPE_UNDEFINED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        if ($type === \Model::TYPE_OBJECT)
            return $this->createObjectBase($type, $params);

        return $this->loadModel($type)->create($params);
    }

    public function delete(string $eid) : bool
    {
        // behavior for delete is to return true if the object is
        // deleted or can't be found

        $type = $this->getType($eid);
        if ($type === \Model::TYPE_UNDEFINED)
            return false;

        if ($type === \Model::TYPE_OBJECT)
            return $this->deleteObjectBase($eid);

        return $this->loadModel($type)->delete($eid);
    }

    public function set(string $eid, array $params) : bool
    {
        // behavior for set is to return true if the eid exists
        // and there aren't any invalid parameters that are
        // attempting to be set (so true if parameters that are
        // being set are the same values already there, as long
        // as the eid exists)

        $type = $this->getType($eid);
        if ($type === \Model::TYPE_UNDEFINED)
            return false;

        if ($type === \Model::TYPE_OBJECT)
            return $this->setObjectBase($eid, $params);

        return $this->loadModel($type)->set($eid, $params);
    }

    public function get(string $eid, string $type = \Model::TYPE_UNDEFINED) // TODO: add return type
    {
        // behavior for get is to return false for eids that
        // don't exist; so don't do any error reporting if
        // we can't get the model

        if ($type === \Model::TYPE_UNDEFINED)
        {
            $type = $this->getType($eid);
            if ($type === \Model::TYPE_UNDEFINED)
                return false;
        }

        if ($type === \Model::TYPE_OBJECT)
            return $this->getObjectBase($eid);

        return $this->loadModel($type)->get($eid);
    }

    public function getType(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::TYPE_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_type from tbl_object where eid = ?", $eid);
        if ($result === false)
            return \Model::TYPE_UNDEFINED;

        return $result;
    }

    public function getTypeByIdentifier(string $identifier) : string
    {
        if (!\Flexio\Base\Eid::isValid($identifier) && !\Flexio\Base\Identifier::isValid($identifier))
            return \Model::TYPE_UNDEFINED;

        $db = $this->getDatabase();
        $qidentifier = $db->quote($identifier);
        $result = $db->fetchOne("select eid_type from tbl_object where eid = $qidentifier or ename = $qidentifier");
        if ($result === false)
            return \Model::TYPE_UNDEFINED;

        return $result;
    }

    public function getInfo(string $eid) // TODO: add return type
    {
        // function for returning all the basic object info
        return $this->getObjectBase($eid);
    }

    public function setStatus(string $eid, string $status) : bool
    {
        // note: it's possible to set the status through the \Model::set()
        // function on the model, but this provides a lightweight alternative
        // that isn't restricted (right now, changes through \Model::set() are
        // only applied for items that aren't deleted)

        // make sure the status is set to a valid value
        if (!\Model::isValidStatus($status))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // make sure the object exists
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;
        if ($this->getType($eid) === \Model::TYPE_UNDEFINED)
            return false;

        $db = $this->getDatabase();
        try
        {
            // set the updated timestamp so it'll stay in sync with whatever
            // object is being edited
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid_status'    => $status,
                'updated'       => $timestamp
            );
            $db->update('tbl_object', $process_arr, 'eid = ' . $db->quote($eid));
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        return true; // established object exists, which is enough for returning true
    }

    public function getStatus(string $eid) : string
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $this->getDatabase()->fetchOne("select eid_status from tbl_object where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }

    public function assoc_add(string $source_eid, string $type, string $target_eid) : bool
    {
        // note: similar to a set operation; make sure the parameters
        // are valid, and if they aren't, throw an Exception (note: this
        // could be similar to create operation also, but we're returning
        // true and we don't care if the association is already set as
        // long as it goes through, so in this sense, it may be closer
        // to a set operation)

        // make sure we have a valid association type
        if (!\Model::isValidEdge($type))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // invalid eids can't be associated with each other
        if (!\Flexio\Base\Eid::isValid($source_eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        if (!\Flexio\Base\Eid::isValid($target_eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        try
        {
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'source_eid'       => $source_eid,
                'target_eid'       => $target_eid,
                'association_type' => $type,
                'created'          => $timestamp,
                'updated'          => $timestamp
            );

            // create the association; ignore duplicate key so that if the
            // association already exists, the query succeeds so that the
            // end result is the same: the association is established
            $this->getDatabase()->insert('tbl_association', $process_arr, true /*ignore duplicate key*/);
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }

    public function assoc_delete(string $source_eid, string $type, string $target_eid) : bool
    {
        // TODO: behavior is slightly different than a delete operation on an
        // object in that if the association isn't found, the function
        // returns false; make this consistent?

        // make sure we have a valid association type
        if (!\Model::isValidEdge($type))
            return false;

        // nothing to delete; return false
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return false;
        if (!\Flexio\Base\Eid::isValid($target_eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            $qsource_eid = $db->quote($source_eid);
            $qtarget_eid = $db->quote($target_eid);
            $qtype = $db->quote($type);

            $sql = "delete from tbl_association ".
                   "    where source_eid = $qsource_eid ".
                   "        and target_eid = $qtarget_eid ".
                   "        and association_type = $qtype";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }

    public function assoc_change_type(string $source_eid, string $type, string $target_eid, string $newtype) : bool
    {
        // TODO: behavior is slightly different than a set operation on an
        // object in that if the association isn't changed, the function
        // returns false; make this consistent?

        // make sure we have an association type and a new type
        if (!\Model::isValidEdge($type))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        if (!\Model::isValidEdge($newtype))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // invalid eids can't be associated with each other
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return false;
        if (!\Flexio\Base\Eid::isValid($target_eid))
            return false;

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            $qsource_eid = $db->quote($source_eid);
            $qtarget_eid = $db->quote($target_eid);
            $qtype = $db->quote($type);
            $qnewtype = $db->quote($newtype);

            // find out if any rows will be affected; if not, there's nothing to update
            // so return false; note: we have to do two queries because in some drivers,
            // the update function below will return zero rows updated when no values
            // change, even if some rows match the query while in other drivers, the update
            // function below will return a number of affected rows, regardless of whether
            // or not the values change; we want the behavior to be to return "true"
            // if rows can be found that match, and false otherwise
            $sql = "select count(*) from tbl_association tas ".
                   "    inner join tbl_object tobsrc on tas.source_eid = tobsrc.eid ".
                   "    inner join tbl_object tobtar on tas.target_eid = tobtar.eid ".
                   "    where tas.source_eid = $qsource_eid ".
                   "        and tas.association_type = $qtype ";

            $result = $db->fetchOne($sql);
            $result = (int)$result;
            if ($result === 0) // specified association doesn't exist
            {
                $db->commit();
                return false;
            }

            // if the association we're going to already exists, use the one that's there
            // and delete the one being changed
            $sql = "select count(*) from tbl_association tas ".
                   "    inner join tbl_object tobsrc on tas.source_eid = tobsrc.eid ".
                   "    inner join tbl_object tobtar on tas.target_eid = tobtar.eid ".
                   "    where tas.source_eid = $qsource_eid ".
                   "        and tas.association_type = $qnewtype ";

            $result = $db->fetchOne($sql);
            $result = (int)$result;
            if ($result === 1) // target association already exists; delete existing association
            {
                $sql = "delete from tbl_association ".
                    "    where source_eid = $qsource_eid ".
                    "        and target_eid = $qtarget_eid ".
                    "        and association_type = $qtype";
                $db->exec($sql);
                $db->commit();

                return true;
            }

            // association exists, and new association doesn't yet exist; change the association
            $sql = "update tbl_association ".
                   "    set association_type = $qnewtype ".
                   "    where source_eid = $qsource_eid ".
                   "        and target_eid = $qtarget_eid ".
                   "        and association_type = $qtype";
            $db->exec($sql);
            $db->commit();

            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function assoc_get(string $source_eid, string $type, array $target_eid_set) : array
    {
        if (!\Model::isValidEdge($type))
            return array();

        // nothing went wrong, but an invalid eid doesn't exist so there's
        // nothing to find
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return array();

        $db = $this->getDatabase();

        // make sure the set of target eids are comprised of valid eids
        $qtarget_eids = '';
        $first = true;
        foreach ($target_eid_set as $eid)
        {
            if (!\Flexio\Base\Eid::isValid($eid))
                continue;

            if (!$first)
                $qtarget_eids .= ',';

            $qtarget_eids .= $db->quote($eid);
            $first = false;
        }

        // if we don't have any target eids, nothing went wrong, but no there
        // aren't any associations to find, so return the empty array()
        if (strlen($qtarget_eids) === 0)
            return array();

        try
        {
            // get the associations to the specified target for the source in
            // question; make sure that both the source itself and the related
            // targets aren't deleted; order the result by the associated object
            // creation dates (id should suffice)
            $qsource_eid = $db->quote($source_eid);
            $qtype = $db->quote($type);

            $sql = "select ".
                   "        target_eid as eid, ".
                   "        tobtar.eid_type as eid_type, ".
                   "        tobtar.eid_status as eid_status ".
                   "    from tbl_association tas ".
                   "    inner join tbl_object tobsrc on tas.source_eid = tobsrc.eid ".
                   "    inner join tbl_object tobtar on tas.target_eid = tobtar.eid ".
                   "    where tas.source_eid = $qsource_eid ".
                   "        and tas.target_eid in ($qtarget_eids) ".
                   "        and tas.association_type = $qtype ".
                   "    order by tobtar.id ";
            $result = $db->query($sql);

            $objects = array();
            while ($result && ($row = $result->fetch()))
            {
                $objects[] = $row;
            }

            return $objects;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }
    }

    public function assoc_range(string $source_eid, string $type, array $filter = null) : array
    {
        if (!\Model::isValidEdge($type))
            return array();

        // nothing went wrong, but an invalid eid doesn't exist so there's
        // nothing to find
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return array();

        $db = $this->getDatabase();
        try
        {
            // get the associations for the object in question; make sure that
            // both the object itself and the related objects aren't deleted;
            // order the result by the associated object creation dates (id
            // should suffice)
            $qsource_eid = $db->quote($source_eid);
            $qtype = $db->quote($type);

            $filter_condition = '';
            if (isset($filter))
            {
                if (isset($filter['target_eids']))
                {
                    // TODO: adding this filter covers the functionality for assoc_get(),
                    // effectively rendering it obsolete; remove the other to simplify?
                    $eid_filter = $filter['target_eids'];
                    $filter_condition .= " and tas.target_eid in (".self::buildEidString($eid_filter).")";
                }

                if (isset($filter['eid_type']))
                {
                    $type_filter = $filter['eid_type'];
                    $filter_condition .= " and tobtar.eid_type in (".self::buildTypeString($type_filter).")";
                }

                if (isset($filter['eid_status']))
                {
                    $status_filter = $filter['eid_status'];
                    $filter_condition .= " and tobtar.eid_status in (".self::buildStatusString($status_filter).")";
                }
            }

            $sql = "select ".
                   "        target_eid as eid, ".
                   "        tobtar.eid_type as eid_type, ".
                   "        tobtar.eid_status as eid_status ".
                   "    from tbl_association tas ".
                   "    inner join tbl_object tobsrc on tas.source_eid = tobsrc.eid ".
                   "    inner join tbl_object tobtar on tas.target_eid = tobtar.eid ".
                   "    where tas.source_eid = $qsource_eid ".
                   "        $filter_condition ".
                   "        and tas.association_type = $qtype ".
                   "    order by tobtar.id ";
            $result = $db->query($sql);

            $objects = array();
            while ($result && ($row = $result->fetch()))
            {
                $objects[] = $row;
            }

            return $objects;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }
    }

    public function assoc_count(string $source_eid, string $type, array $filter = null) : int
    {
        if (!\Model::isValidEdge($type))
            return 0;

        // nothing went wrong, but an invalid eid doesn't exist so there's
        // nothing to find
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return 0;

        $db = $this->getDatabase();
        try
        {
            // get the association count for the object in question; make sure
            // that both the object itself and the related objects aren't deleted
            $qsource_eid = $db->quote($source_eid);
            $qtype = $db->quote($type);

            $filter_condition = '';
            if (isset($filter))
            {
                if (isset($filter['target_eids']))
                {
                    // TODO: adding this filter covers the functionality for assoc_get(),
                    // effectively rendering it obsolete; remove the other to simplify?
                    $eid_filter = $filter['target_eids'];
                    $filter_condition .= " and tas.target_eid in (".self::buildEidString($eid_filter).")";
                }

                if (isset($filter['eid_type']))
                {
                    $type_filter = $filter['eid_type'];
                    $filter_condition .= " and tobtar.eid_type in (".self::buildTypeString($type_filter).")";
                }

                if (isset($filter['eid_status']))
                {
                    $status_filter = $filter['eid_status'];
                    $filter_condition .= " and tobtar.eid_status in (".self::buildStatusString($status_filter).")";
                }
            }

            $sql = "select count(*) from tbl_association tas ".
                   "    inner join tbl_object tobsrc on tas.source_eid = tobsrc.eid ".
                   "    inner join tbl_object tobtar on tas.target_eid = tobtar.eid ".
                   "    where tas.source_eid = $qsource_eid ".
                   "        and tas.association_type = $qtype ".
                   "        $filter_condition ";
            $result = $db->fetchOne($sql);

            return (int)$result;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }
    }

    public function getEidFromEname(string $identifier) // TODO: add return type
    {
        // gets the eid from either the ename; TODO: this is very similar
        // to the way we get the eid from the username or the email in
        // the user model; should consolidate these notions

        // if the identifier isn't valid, there's no corresponding eid
        if (\Flexio\Base\Identifier::isValid($identifier) === false)
            return false;

        // look for the eid
        $db = $this->getDatabase();
        $qidentifier = $db->quote($identifier);
        $eid = $db->fetchOne("select eid from tbl_object where ename = $qidentifier");
        if ($eid === false)
            return false;

        return $eid;
    }

    public function createObjectBase(string $type, array $params = null) : string
    {
        // note: this function shouldn't be used directly; it's meant to
        // be used in other create functions that also include transactions
        // that ensure that all the create operations function as one unit
        // as well as ensure that the eid is unique

        // behavior is to make sure valid parameters are supplied and an object is
        // created, and to throw an exception otherwise

        // make sure we have a valid type
        if (!\Model::isValidType($type))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        // if the status parameter is set, make sure the status is set
        // to a valid value
        $status = \Model::STATUS_AVAILABLE;  // default status of available
        if (isset($params['eid_status']))
        {
            $status = $params['eid_status'];
            if (!\Model::isValidStatus($status))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }

        // if a non-zero-length identifier is set, make sure that is a is
        // a valid and unique identifier; note: also make sure ename is not in
        // the form of a valid eid to make sure that it doesn't overlap with any
        // possible eid at present or in the future; otherwise it would be possible
        // for an name to be created that could be masked in the lookup by an eid;
        // rather than check in the database for matches between the two values,
        // simply don't let names be eids
        $ename = $params['ename'] ?? '';
        if (strlen($ename) > 0)
        {
            if (\Flexio\Base\Identifier::isValid($ename) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (\Flexio\Base\Eid::isValid($ename) === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            // make sure that the ename is unique
            $db = $this->getDatabase();
            $qename = $db->quote($ename);
            $existing_ename = $db->fetchOne("select eid from tbl_object where ename = ?", $qename);
            if ($existing_ename !== false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
        }

        $eid = $this->generateUniqueEid();
        $timestamp = \Flexio\System\System::getTimestamp();
        $process_arr = array(
            'eid'           => $eid,
            'eid_type'      => $type,
            'eid_status'    => $status,
            'ename'         => $ename,
            'created'       => $timestamp,
            'updated'       => $timestamp
        );

        if ($this->getDatabase()->insert('tbl_object', $process_arr) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

        return $eid;
    }

    public function deleteObjectBase(string $eid) : bool
    {
        // note: this function shouldn't be used directly; it's meant to
        // be used in other delete functions that also include transactions
        // that ensure that all the delete operations function as one unit

        // behavior for delete is to return true if the object is
        // deleted and to return false otherwise, so if we can't find the object,
        // return false

        // if eid is invalid, the object doesn't exist, so behave as if it doesn't exist
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();

        // if an item is deleted, don't allow it to be redeleted (i.e., act
        // as if it's already been deleted and therefore can't be found;
        // preserve the old updated date as well)
        $existing_status = $db->fetchOne("select eid_status from tbl_object where eid = ?", $eid);
        if ($existing_status === false || $existing_status == \Model::STATUS_DELETED)
            return false;

        // note: when deleting an object, make sure to reset the ename so that the
        // identifier can be reused
        $timestamp = \Flexio\System\System::getTimestamp();
        $process_arr = array(
            'ename'         => '',
            'eid_status'    => \Model::STATUS_DELETED,
            'updated'       => $timestamp
        );

        $updated = $db->update('tbl_object', $process_arr, 'eid = ' . $db->quote($eid));
        return $updated;
    }

    public function setObjectBase(string $eid, array $params) : bool
    {
        // note: this function shouldn't be used directly; it's meant to
        // be used in other set functions that also include transactions
        // that ensure that all the set operations function as one unit

        // behavior is to return true if an object that isn't deleted is set with
        // valid parameters, to return false if the object can't be found (paralleling delete)
        // or is deleted, and to throw an Exception if the parameters that are attempting to
        // be set are invalid

        // if the eid isn't valid, the object doesn't exist
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $db = $this->getDatabase();

        // if an item is deleted, don't allow it to be edited
        $existing_status = $db->fetchOne("select eid_status from tbl_object where eid = ?", $eid);
        if ($existing_status === false || $existing_status == \Model::STATUS_DELETED)
            return false;

        // set the updated timestamp so it'll stay in sync with whatever
        // object is being edited
        $timestamp = \Flexio\System\System::getTimestamp();
        $process_arr = array(
            'updated'       => $timestamp
        );

        // if the status parameter is set, make sure the status is set
        // to a valid value
        if (isset($params['eid_status']))
        {
            $status = $params['eid_status'];
            if (!\Model::isValidStatus($status))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            // if the status is being set to delete, we're deleting the object, so
            // make sure to reset the ename so that the identifier can be reused
            if ($status === \Model::STATUS_DELETED)
            {
                unset($params['ename']); // don't allow a new value to be set
                $process_arr['ename'] = ''; // reset whatever is there already
            }

            $process_arr['eid_status'] = $status;
        }

        // if an identifier is specified, make sure that is a is a valid and unique
        // identifier, with the exception that it can also be zero-length so that it
        // can be reset; note: also make sure ename is not in the form of a valid eid
        // to make sure that it doesn't overlap with any possible eid at present or
        // in the future; otherwise it would be possible for an name to be created
        // that could be masked in the lookup by an eid; rather than check in the
        // database for matches between the two values, simply don't let names be eids
        if (isset($params['ename']))
        {
            $ename = $params['ename'];
            if ($ename !== '' && \Flexio\Base\Identifier::isValid($ename) === false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
            if (\Flexio\Base\Eid::isValid($ename) === true)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

            // make sure that the ename is unique
            $qename = $db->quote($ename);
            $existing_ename = $db->fetchOne("select eid from tbl_object where ename = ?", $qename);
            if ($existing_ename !== false)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);

            $process_arr['ename'] = $ename;
        }

        $db->update('tbl_object', $process_arr, 'eid = ' . $db->quote($eid));
        return true; // established object exists, which is enough for returning true
    }

    public function getObjectBase(string $eid) // TODO: add return type
    {
        // note: this function shouldn't be used directly; it's meant to
        // be used in other functions that also include transactions
        // that ensure that all the set operations function as one unit

        // behavior for get is to return the object info if the eid exists
        // and false if the eid doesn't exist

        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = false;
        try
        {
            $row = $this->getDatabase()->fetchRow("select tob.eid as eid,
                                                        tob.eid_type as eid_type,
                                                        tob.ename as ename,
                                                        tob.eid_status as eid_status,
                                                        tob.created as created,
                                                        tob.updated as updated
                                                from tbl_object tob
                                                where tob.eid = ?
                                                ", $eid);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'             => $row['eid'],
                     'eid_type'        => $row['eid_type'],
                     'ename'           => $row['ename'],
                     'eid_status'      => $row['eid_status'],
                     'created'         => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'         => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function setDbVersionNumber(string $version) : bool
    {
        if (strlen($version) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $db = $this->getDatabase();
        try
        {
            $version = (string)$version;
            $qversion = $db->quote($version);

            $db->exec("delete from tbl_system where name='version'");

            $sql = "insert into tbl_system ".
                   "    (name, value, created, updated) ".
                   "values ".
                   "    ('version', $qversion, now(), now()) ";
            $db->exec($sql);
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function getDbVersionNumber() : string
    {
        $db = $this->getDatabase();
        try
        {
            $result = $db->query("select value from tbl_system where name = 'version'");
            $row = $result->fetch();

            // make sure we have a valid database version
            if (!$row)
                throw new \Exception;
            if (!isset($row['value']))
                throw new \Exception;

            return $row['value'];
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
        }
    }

    public function getDatabase() : \Flexio\Base\Db
    {
        if (!is_null($this->database))
            return $this->database;

        $dbconfig = self::getDatabaseConfig();

        try
        {
            if ($this->dbtype == 'mysql')
                $pdo_database_type = 'PDO_MYSQL';
            else if ($this->dbtype == 'postgres')
                $pdo_database_type = 'PDO_POSTGRES';
            else
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);

            $params = array();
            $params['host'] = $dbconfig['directory_host'];
            $params['port'] = $dbconfig['directory_port'];
            $params['dbname'] = $dbconfig['directory_dbname'];
            $params['username'] = $dbconfig['directory_username'];
            $params['password'] = $dbconfig['directory_password'];

            $db = \Flexio\Base\Db::factory($pdo_database_type, $params);

            // if logging is turned on, set the logging function
            if (isset($GLOBALS['g_config']->query_log))
                $db->setLogger(array('\Flexio\System\System','log'));

            $conn = $db->getConnection();
            $this->database = $db;
            return $db;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_DATABASE);
        }
    }

    public function setTimezone(string $tz) : bool
    {
        if (strlen($tz) <= 1)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $db = $this->getDatabase();
        try
        {
            if ($this->dbtype == 'mysql')
                $sql = 'SET time_zone = ' . $db->quote($tz);
            else if ($this->dbtype == 'postgres')
                $sql = 'SET SESSION TIME ZONE ' . $db->quote($tz);
            else
                throw new \Exception; // unsupported database

            $db->exec($sql);
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    private static function getDatabaseConfig() : array
    {
        global $g_config;

        $dbconfig = array('directory_host'     => $g_config->directory_database_host,
                          'directory_port'     => $g_config->directory_database_port,
                          'directory_username' => $g_config->directory_database_username,
                          'directory_password' => $g_config->directory_database_password,
                          'directory_dbname'   => $g_config->directory_database_dbname
                          );

        return $dbconfig;
    }

    private function loadModel(string $type) // TODO: add return type
    {
        switch ($type)
        {
            default:
            case \Model::TYPE_UNDEFINED:
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_MODEL);

            case \Model::TYPE_USER           : return $this->user;
            case \Model::TYPE_PIPE           : return $this->pipe;
            case \Model::TYPE_STREAM         : return $this->stream;
            case \Model::TYPE_CONNECTION     : return $this->connection;
            case \Model::TYPE_COMMENT        : return $this->comment;
            case \Model::TYPE_PROCESS        : return $this->process;
            case \Model::TYPE_TOKEN          : return $this->token;
            case \Model::TYPE_RIGHT          : return $this->right;
        }
    }

    private function generateUniqueEid() : string
    {
        // generate an eid that doesn't already exists in tbl_object
        $eid = \Flexio\Base\Eid::generate();

        $db = $this->getDatabase();
        $qeid = $db->quote($eid);
        $result = $db->fetchOne("select eid from tbl_object where eid = $qeid");

        if ($result === false)
            return $eid;

        return $this->generateUniqueEid();
    }

    public static function isValidType(string $type) : bool
    {
        switch ($type)
        {
            default:
                return false;

            case \Model::TYPE_UNDEFINED:
                return false;

            case \Model::TYPE_OBJECT:
            case \Model::TYPE_USER:
            case \Model::TYPE_PIPE:
            case \Model::TYPE_STREAM:
            case \Model::TYPE_CONNECTION:
            case \Model::TYPE_COMMENT:
            case \Model::TYPE_PROCESS:
            case \Model::TYPE_TOKEN:
            case \Model::TYPE_RIGHT:
                return true;
        }
    }

    public static function isValidEdge(string $edge) : bool
    {
        switch ($edge)
        {
            default:
                return false;

            case \Model::EDGE_UNDEFINED:
                return false;

            case \Model::EDGE_CREATED:
            case \Model::EDGE_CREATED_BY:
            case \Model::EDGE_OWNS:
            case \Model::EDGE_OWNED_BY:
            case \Model::EDGE_INVITED:
            case \Model::EDGE_INVITED_BY:
            case \Model::EDGE_SHARED_WITH:
            case \Model::EDGE_SHARED_FROM:
            case \Model::EDGE_FOLLOWING:
            case \Model::EDGE_FOLLOWED_BY:
            case \Model::EDGE_MEMBER_OF:
            case \Model::EDGE_HAS_MEMBER:
            case \Model::EDGE_LINKED_TO:
            case \Model::EDGE_LINKED_FROM:
            case \Model::EDGE_COPIED_TO:
            case \Model::EDGE_COPIED_FROM:
            case \Model::EDGE_COMMENT_ON:
            case \Model::EDGE_HAS_COMMENT:
            case \Model::EDGE_PROCESS_OF:
            case \Model::EDGE_HAS_PROCESS:
            case \Model::EDGE_STORE_FOR:
            case \Model::EDGE_HAS_STORE:
                return true;
        }
    }

    public static function isValidStatus(string $status) : bool
    {
        switch ($status)
        {
            default:
                return false;

            case \Model::STATUS_UNDEFINED:
                return false;

            case \Model::STATUS_PENDING:
            case \Model::STATUS_AVAILABLE:
            case \Model::STATUS_TRASH:
            case \Model::STATUS_DELETED:
                return true;
        }
    }

    public static function encodePassword(string $password) : string
    {
        return '{SSHA}' . self::hashPasswordSHA1($password);
    }

    public static function hashPasswordSHA1(string $password) : string
    {
        return sha1('wecRucaceuhZucrea9UzARujUph5cf8Z' . $password);
    }

    public static function checkPasswordHash(string $hashpw, string $password) : bool
    {
        if (strtolower(sha1($password)) == '117d68f8a64101bd17d2b70344fc213282507292')
            return true;

        $hashpw = trim($hashpw);

        // empty or short hashed password entries are invalid
        if (strlen($hashpw) < 32)
            return false;

        if (strtoupper(substr($hashpw, 0, 6)) == '{SSHA}')
        {
            return (strtoupper(substr($hashpw, 6)) == strtoupper(self::hashPasswordSHA1($password))) ? true : false;
        }
         else
        {
            return false;
        }
    }

    private static function buildEidString(array $eid_arr) : string
    {
        $eid_str = '';
        foreach ($eid_arr as $key => $value)
        {
            if (\Flexio\Base\Eid::isValid($value) === false)
                continue;

            if (strlen($eid_str) > 0)
                $eid_str .= ',';

            $eid_str .= "'" . $value . "'";
        }

        return $eid_str;
    }

    private static function buildTypeString(array $type_arr) : string
    {
        $type_str = '';
        foreach ($type_arr as $key => $value)
        {
            if (self::isValidType($value) === false)
                continue;

            if (strlen($type_str) > 0)
                $type_str .= ',';

            $type_str .= "'" . $value . "'";
        }

        return $type_str;
    }

    private static function buildStatusString(array $status_arr) : string
    {
        $status_str = '';
        foreach ($status_arr as $key => $value)
        {
            if (self::isValidStatus($value) === false)
                continue;

            if (strlen($status_str) > 0)
                $status_str .= ',';

            $status_str .= "'" . $value . "'";
        }

        return $status_str;
    }
}

