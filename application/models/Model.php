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


require_once __DIR__ . '/../system/ModelDb.php';
require_once 'ModelBase.php';


class Model
{
    const ERROR_NONE                   =  'none';
    const ERROR_UNDEFINED              =  'undefined';
    const ERROR_GENERAL                =  'general';
    const ERROR_UNIMPLEMENTED          =  'unimplemented';
    const ERROR_NO_DATABASE            =  'no_database';
    const ERROR_NO_MODEL               =  'no_model';
    const ERROR_NO_SERVICE             =  'no_service';
    const ERROR_NO_OBJECT              =  'no_object';
    const ERROR_INVALID_SYNTAX         =  'invalid_syntax';
    const ERROR_MISSING_PARAMETER      =  'missing_parameter';
    const ERROR_INVALID_PARAMETER      =  'invalid_parameter';
    const ERROR_CREATE_FAILED          =  'create_failed';
    const ERROR_DELETE_FAILED          =  'delete_failed';
    const ERROR_WRITE_FAILED           =  'write_failed';
    const ERROR_READ_FAILED            =  'read_failed';
    const ERROR_INSUFFICIENT_RIGHTS    =  'insufficient_rights';
    const ERROR_SIZE_LIMIT_EXCEEDED    =  'size_limit_exceeded';

    const TYPE_UNDEFINED      = '';
    const TYPE_OBJECT         = 'OBJ';
    const TYPE_USER           = 'USR';
    const TYPE_PROJECT        = 'PRJ';
    const TYPE_PIPE           = 'PIP';
    const TYPE_STREAM         = 'STR';
    const TYPE_CONNECTION     = 'CTN';
    const TYPE_COMMENT        = 'CMT';
    const TYPE_PROCESS        = 'PRC';
    const TYPE_TOKEN          = 'ATH';

    const EDGE_UNDEFINED     = '';     // undefind edge
    const EDGE_CREATED       = 'CRT';  // user A created object B
    const EDGE_CREATED_BY    = 'CRB';  // object B was created by user A
    const EDGE_OWNS          = 'OWN';  // user A owns object B
    const EDGE_OWNED_BY      = 'OWB';  // object B is owned by user A
    const EDGE_INVITED       = 'INV';  // user A invited user B
    const EDGE_INVITED_BY    = 'INB';  // user B was invited by user A
    const EDGE_SHARED_WITH   = 'SHW';  // user A shared with user B
    const EDGE_SHARED_FROM   = 'SHF';  // user B was shared something from user A
    const EDGE_FOLLOWING     = 'FLW';  // user B is following object A
    const EDGE_FOLLOWED_BY   = 'FLB';  // object A is followed by user B
    const EDGE_MEMBER_OF     = 'MBO';  // object A is a member of object B
    const EDGE_HAS_MEMBER    = 'HMB';  // object B has member object A
    const EDGE_LINKED_TO     = 'LKT';  // object A links to object B
    const EDGE_LINKED_FROM   = 'LKF';  // object A links from object A
    const EDGE_COPIED_TO     = 'CPT';  // object A copied to object B
    const EDGE_COPIED_FROM   = 'CPF';  // object B copied from object A
    const EDGE_COMMENT_ON    = 'CMO';  // comment A is a comment on object B
    const EDGE_HAS_COMMENT   = 'HCM';  // object B has comment A
    const EDGE_PROCESS_OF    = 'PRO';  // object A is a process of object B
    const EDGE_HAS_PROCESS   = 'HPR';  // object B has process of object A

    const STATUS_UNDEFINED = '';
    const STATUS_PENDING   = 'P';
    const STATUS_AVAILABLE = 'A';
    const STATUS_TRASH     = 'T';
    const STATUS_DELETED   = 'D';

    const REGISTRY_VALUE_UNDEFINED = '';
    const REGISTRY_VALUE_STRING    = 'S';
    const REGISTRY_VALUE_NUMBER    = 'N';
    const REGISTRY_VALUE_BOOLEAN   = 'B';
    const REGISTRY_VALUE_DATE      = 'D'; // (YYYY-MM-DD)
    const REGISTRY_VALUE_DATETIME  = 'T'; // (YYYY-MM-DD HH:MM:SS)
    const REGISTRY_VALUE_BINARY    = 'X';

    const CONNECTION_STATUS_INVALID     = 'I';
    const CONNECTION_STATUS_AVAILABLE   = 'A';
    const CONNECTION_STATUS_UNAVAILABLE = 'U';
    const CONNECTION_STATUS_ERROR       = 'E';

    const CONNECTION_CONFIG_TYPE_DATABASE = 'database';
    const CONNECTION_CONFIG_TYPE_OAUTH2   = 'oauth2';

    const CONNECTION_TYPE_FLEXIO        = 'flexio.api';
    const CONNECTION_TYPE_FLEXIO_LOCAL  = 'flexio.local';
    const CONNECTION_TYPE_FTP           = 'ftp.api';
    const CONNECTION_TYPE_SFTP          = 'sftp.api';
    const CONNECTION_TYPE_MYSQL         = 'mysql.api';
    const CONNECTION_TYPE_POSTGRES      = 'postgres.api';
    const CONNECTION_TYPE_DROPBOX       = 'dropbox.oauth2';
    const CONNECTION_TYPE_GOOGLEDRIVE   = 'googledrive.oauth2';
    const CONNECTION_TYPE_GOOGLESHEETS  = 'googlesheets.oauth2';
    const CONNECTION_TYPE_AMAZONS3      = 'amazons3.api';
    const CONNECTION_TYPE_UPLOAD        = 'upload.api';
    const CONNECTION_TYPE_DOWNLOAD      = 'download.api';
    const CONNECTION_TYPE_HTTP          = 'http.api';
    const CONNECTION_TYPE_RSS           = 'rss.api';
    const CONNECTION_TYPE_SOCRATA       = 'socrata.api';
    const CONNECTION_TYPE_PIPELINEDEALS = 'pipelinedeals.api';
    const CONNECTION_TYPE_MAILJET       = 'mailjet.api';
    const CONNECTION_TYPE_TWILIO        = 'twilio.api';

    const PIPE_STATUS_ACTIVE   = 'A';
    const PIPE_STATUS_INACTIVE = 'I';

    const PROCESS_MODE_BUILD    = 'B';
    const PROCESS_MODE_RUN      = 'R';

    const PROCESS_STATUS_UNDEFINED = '';
    const PROCESS_STATUS_PENDING   = 'S'; // 'S' for 'Starting'
    const PROCESS_STATUS_WAITING   = 'W';
    const PROCESS_STATUS_RUNNING   = 'R';
    const PROCESS_STATUS_CANCELLED = 'X';
    const PROCESS_STATUS_PAUSED    = 'P';
    const PROCESS_STATUS_FAILED    = 'F';
    const PROCESS_STATUS_COMPLETED = 'C';

    private $objs = array();
    private $database = null;
    private $errors = array();  // use array to allow tracing stack of errors
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

    public function create($type, $params)
    {
        if ($type === \Model::TYPE_OBJECT)
            return $this->createObjectBase($type, $params);

        $model = $this->loadModel($type);
        if ($model === false)
            return $this->fail(Model::ERROR_INVALID_PARAMETER);

        return $model->create($params);
    }

    public function delete($eid)
    {
        // behavior for delete is to return true if the object is
        // deleted and to return otherwise, so if we can't return
        // the object return false

        $type = $this->getType($eid);
        if ($type === \Model::TYPE_OBJECT)
            return $this->deleteObjectBase($eid);

        $model = $this->loadModel($type);
        if ($model === false)
            return false;

        return $model->delete($eid);
    }

    public function set($eid, $params)
    {
        // behavior for set is to return true if the eid exists
        // and there aren't any invalid parameters that are
        // attempting to be set (so true if parameters that are
        // being set are the same values already there, as long
        // as the eid exists)

        $type = $this->getType($eid);
        if ($type === \Model::TYPE_OBJECT)
            return $this->setObjectBase($eid, $params);

        $model = $this->loadModel($type);
        if ($model === false)
            return false;

        return $model->set($eid, $params);
    }

    public function get($eid, $type = \Model::TYPE_UNDEFINED)
    {
        // behavior for get is to return false for eids that
        // don't exist; so don't do any error reporting if
        // we can't get the model

        if ($type === \Model::TYPE_UNDEFINED)
            $type = $this->getType($eid);

        if ($type === \Model::TYPE_OBJECT)
            return $this->getObjectBase($eid);

        $model = $this->loadModel($type);
        if ($model === false)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return $model->get($eid);
    }

    public function getType($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Model::ERROR_NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::TYPE_UNDEFINED;

        $result = $db->fetchOne("select eid_type from tbl_object where eid = ?", $eid);
        if ($result === false)
            return \Model::TYPE_UNDEFINED;

        return $result;
    }

    public function getTypeByIdentifier($identifier)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Model::ERROR_NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($identifier) && !\Flexio\Base\Identifier::isValid($identifier))
            return \Model::TYPE_UNDEFINED;

        $qidentifier = $db->quote($identifier);
        $result = $db->fetchOne("select eid_type from tbl_object where eid = $qidentifier or ename = $qidentifier");
        if ($result === false)
            return \Model::TYPE_UNDEFINED;

        return $result;
    }

    public function getInfo($eid)
    {
        // function for returning all the basic object info
        return $this->getObjectBase($eid);
    }

    public function setStatus($eid, $status)
    {
        // note: it's possible to set the status through the \Model::set()
        // function on the model, but this provides a lightweight alternative
        // that isn't restricted (right now, changes through \Model::set() are
        // only applied for items that aren't deleted)

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Model::ERROR_NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        // make sure the status is set to a valid value
        if (!\Model::isValidStatus($status))
            return $this->fail(\Model::ERROR_INVALID_PARAMETER);

        // make sure the object exists
        if ($this->getType($eid) === \Model::TYPE_UNDEFINED)
            return false;

        // set the updated timestamp so it'll stay in sync with whatever
        // object is being edited
        $timestamp = \Flexio\System\System::getTimestamp();
        $process_arr = array(
            'eid_status'    => $status,
            'updated'       => $timestamp
        );

        $db->update('tbl_object', $process_arr, 'eid = ' . $db->quote($eid));
        return true; // established object exists, which is enough for returning true
    }

    public function getStatus($eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(\Model::ERROR_NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return \Model::STATUS_UNDEFINED;

        $result = $db->fetchOne("select eid_status from tbl_object where eid = ?", $eid);
        if ($result === false)
            return \Model::STATUS_UNDEFINED;

        return $result;
    }

    public function search($path)
    {
        return $this->search->exec($path);
    }

    public function assoc_add($source_eid, $type, $target_eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        // make sure we have a valid association type; flag an error
        // for an invalid edge
        if (!Model::isValidEdge($type))
            return $this->fail(Model::ERROR_INVALID_PARAMETER);

        // invalid eids can't be associated with each other
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return false;
        if (!\Flexio\Base\Eid::isValid($target_eid))
            return false;

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
            if ($db->insert('tbl_association', $process_arr, true /*ignore duplicate key*/) === false)
                throw new \Exception();

            return true;
        }
        catch (\Exception $e)
        {
            return $this->fail(Model::ERROR_CREATE_FAILED, _('Could not associate objects'));
        }
    }

    public function assoc_delete($source_eid, $type, $target_eid)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        // make sure we have a valid association type
        if (!Model::isValidEdge($type))
            return $this->fail(Model::ERROR_INVALID_PARAMETER);

        // nothing to delete; return false
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return false;
        if (!\Flexio\Base\Eid::isValid($target_eid))
            return false;

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
            return $this->fail(Model::ERROR_DELETE_FAILED, _('Could not delete association'));
        }
    }

    public function assoc_change_type($source_eid, $type, $target_eid, $newtype)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        // make sure we have an association type and a new type
        if (!Model::isValidEdge($type))
            return $this->fail(Model::ERROR_INVALID_PARAMETER);
        if (!Model::isValidEdge($newtype))
            return $this->fail(Model::ERROR_INVALID_PARAMETER);

        // invalid eids can't be associated with each other
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return false;
        if (!\Flexio\Base\Eid::isValid($target_eid))
            return false;

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
            if ($result === 0)
            {
                $db->commit();
                return false;
            }

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
            return $this->fail(Model::ERROR_WRITE_FAILED, _('Could not change association type'));
        }
    }

    public function assoc_get($source_eid, $type, $target_eid_set)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!Model::isValidEdge($type))
            return $this->fail(Model::ERROR_INVALID_PARAMETER);

        // nothing went wrong, but an invalid eid doesn't exist so there's
        // nothing to find
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return array();

        // if the target_eid_set is something besides an array or a string,
        // it's an invalid input; return an empty list
        if (!is_array($target_eid_set) && !is_string($target_eid_set))
            return array();

        // if the target_eid_set is a string, convert it over to an array
        // of one element
        $set = array();
        if (is_string($target_eid_set))
        {
            $set = explode(',',$target_eid_set);
            $target_eid_set = $set;
        }

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
            return $this->fail(Model::ERROR_READ_FAILED, _('Could not get associations'));
        }
    }

    public function assoc_range($source_eid, $type, $status_filter = false)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!Model::isValidEdge($type))
            return $this->fail(Model::ERROR_INVALID_PARAMETER);

        // nothing went wrong, but an invalid eid doesn't exist so there's
        // nothing to find
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return array();

        try
        {
            // get the associations for the object in question; make sure that
            // both the object itself and the related objects aren't deleted;
            // order the result by the associated object creation dates (id
            // should suffice)
            $qsource_eid = $db->quote($source_eid);
            $qtype = $db->quote($type);

            $status_condition = '';
            if ($status_filter !== false)
                $status_condition = " and tobtar.eid_status in (".Model::buildStatusString($status_filter).")";

            $sql = "select ".
                   "        target_eid as eid, ".
                   "        tobtar.eid_type as eid_type, ".
                   "        tobtar.eid_status as eid_status ".
                   "    from tbl_association tas ".
                   "    inner join tbl_object tobsrc on tas.source_eid = tobsrc.eid ".
                   "    inner join tbl_object tobtar on tas.target_eid = tobtar.eid ".
                   "    where tas.source_eid = $qsource_eid ".
                   "        $status_condition ".
                   "        and tas.association_type = $qtype ";
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
            return $this->fail(Model::ERROR_READ_FAILED, _('Could not get associations'));
        }
    }

    public function assoc_count($source_eid, $type, $status_filter = false)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!Model::isValidEdge($type))
            return $this->fail(Model::ERROR_INVALID_PARAMETER);

        // nothing went wrong, but an invalid eid doesn't exist so there's
        // nothing to find
        if (!\Flexio\Base\Eid::isValid($source_eid))
            return 0;

        try
        {
            // get the association count for the object in question; make sure
            // that both the object itself and the related objects aren't deleted
            $qsource_eid = $db->quote($source_eid);
            $qtype = $db->quote($type);

            $status_condition = '';
            if ($status_filter !== false)
                $status_condition = " and tobtar.eid_status in (".Model::buildStatusString($status_filter).")";

            $sql = "select count(*) from tbl_association tas ".
                   "    inner join tbl_object tobsrc on tas.source_eid = tobsrc.eid ".
                   "    inner join tbl_object tobtar on tas.target_eid = tobtar.eid ".
                   "    where tas.source_eid = $qsource_eid ".
                   "        and tas.association_type = $qtype ".
                   "        $status_condition ";
            $result = $db->fetchOne($sql);

            return (int)$result;
        }
        catch (\Exception $e)
        {
            return $this->fail(Model::ERROR_READ_FAILED, _('Could not get association count'));
        }
    }

    public function getEidFromEname($identifier)
    {
        // gets the eid from either the ename; TODO: this is very similar
        // to the way we get the eid from the username or the email in
        // the user model; should consolidate these notions

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        // if the identifier isn't valid, there's no corresponding eid
        if (\Flexio\Base\Identifier::isValid($identifier) === false)
            return false;

        // look for the eid
        $qidentifier = $db->quote($identifier);
        $eid = $db->fetchOne("select eid from tbl_object where ename = $qidentifier");
        if ($eid === false)
            return false;

        return $eid;
    }

    public function createObjectBase($type, $params)
    {
        // note: this function shouldn't be used directly; it's meant to
        // be used in other create functions that also include transactions
        // that ensure that all the create operations function as one unit
        // as well as ensure that the eid is unique

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        // make sure we have a valid type
        if (!Model::isValidType($type))
            return $this->fail(Model::ERROR_INVALID_PARAMETER);

        $eid = $this->generateUniqueEid();
        if (!$eid)
            return $this->fail(Model::ERROR_CREATE_FAILED);

        // if the status parameter is set, make sure the status is set
        // to a valid value
        $status = \Model::STATUS_AVAILABLE;  // default status of available
        if (isset($params['eid_status']))
        {
            $status = $params['eid_status'];
            if (!\Model::isValidStatus($status))
                return $this->fail(Model::ERROR_INVALID_PARAMETER);
        }


        // if a non-zero-length identifier is set, make sure that is a is
        // a valid and unique identifier; note: also make sure ename is not in
        // the form of a valid eid to make sure that it doesn't overlap with any
        // possible eid at present or in the future; otherwise it would be possible
        // for an name to be created that could be masked in the lookup by an eid;
        // rather than check in the database for matches between the two values,
        // simply don't let names be eids
        $ename = isset_or($params['ename'], '');
        if (strlen($ename) > 0)
        {
            if (\Flexio\Base\Identifier::isValid($ename) === false)
                return $this->fail(Model::ERROR_CREATE_FAILED);
            if (\Flexio\Base\Eid::isValid($ename) === true)
                return $this->fail(Model::ERROR_CREATE_FAILED);

            // make sure that the ename is unique
            $qename = $db->quote($ename);
            $existing_ename = $db->fetchOne("select eid from tbl_object where ename = ?", $qename);
            if ($existing_ename !== false)
                return $this->fail(Model::ERROR_CREATE_FAILED);
        }

        $timestamp = \Flexio\System\System::getTimestamp();
        $process_arr = array(
            'eid'           => $eid,
            'eid_type'      => $type,
            'eid_status'    => $status,
            'ename'         => $ename,
            'created'       => $timestamp,
            'updated'       => $timestamp
        );

        if ($db->insert('tbl_object', $process_arr) === false)
            return $this->fail(Model::ERROR_CREATE_FAILED);

        return $eid;
    }

    public function deleteObjectBase($eid)
    {
        // note: this function shouldn't be used directly; it's meant to
        // be used in other delete functions that also include transactions
        // that ensure that all the delete operations function as one unit

        // behavior for delete is to return true if the object is
        // deleted and to return otherwise, so if we can't return
        // the object return false

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        // eid isn't valid; return false
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        // if an item is deleted, don't allow it to be redeleted (i.e., act
        // as if it's already been deleted and therefore can't be found;
        // preserve the old updated date as well)
        $existing_status = $db->fetchOne("select eid_status from tbl_object where eid = ?", $eid);
        if ($existing_status === false || $existing_status == \Model::STATUS_DELETED)
            return false;

        $timestamp = \Flexio\System\System::getTimestamp();
        $process_arr = array(
            'eid_status'    => \Model::STATUS_DELETED,
            'updated'       => $timestamp
        );

        $updated = $db->update('tbl_object', $process_arr, 'eid = ' . $db->quote($eid));
        return $updated;
    }

    public function setObjectBase($eid, $params)
    {
        // note: this function shouldn't be used directly; it's meant to
        // be used in other set functions that also include transactions
        // that ensure that all the set operations function as one unit

        // behavior for set is to return true if the eid exists and there
        // aren't any invalid parameters that are attempting to be set
        // (so true if parameters that are being set are the same values
        // already there, as long as the eid exists)

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        // eid isn't valid
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

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
            if (!Model::isValidStatus($status))
                return $this->fail(Model::ERROR_INVALID_PARAMETER);

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
                return $this->fail(Model::ERROR_CREATE_FAILED);
            if (\Flexio\Base\Eid::isValid($ename) === true)
                return $this->fail(\Model::ERROR_CREATE_FAILED);

            // make sure that the ename is unique
            $qename = $db->quote($ename);
            $existing_ename = $db->fetchOne("select eid from tbl_object where ename = ?", $qename);
            if ($existing_ename !== false)
                return $this->fail(\Model::ERROR_CREATE_FAILED);

            $process_arr['ename'] = $ename;
        }

        $db->update('tbl_object', $process_arr, 'eid = ' . $db->quote($eid));
        return true; // established object exists, which is enough for returning true
    }

    public function getObjectBase($eid)
    {
        // note: this function shouldn't be used directly; it's meant to
        // be used in other functions that also include transactions
        // that ensure that all the set operations function as one unit

        // behavior for get is to return the object info if the eid exists
        // and false if the eid doesn't exist

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = $db->fetchRow("select tob.eid as eid,
                                     tob.eid_type as eid_type,
                                     tob.ename as ename,
                                     tob.eid_status as eid_status,
                                     tob.created as created,
                                     tob.updated as updated
                              from tbl_object tob
                              where tob.eid = ?
                             ", $eid);

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'             => $row['eid'],
                     'eid_type'        => $row['eid_type'],
                     'ename'           => $row['ename'],
                     'eid_status'      => $row['eid_status'],
                     'created'         => \Flexio\Base\Util::formatDate($row['created']),
                     'updated'         => \Flexio\Base\Util::formatDate($row['updated']));
    }

    public function setDbVersionNumber($version)
    {
        // TODO: use model to abstract out custom SQL

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

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
        }
        catch (\Exception $e)
        {
            syslog(LOG_ERR, 'Exception in setDbVersionNumber(): ' . $e->getMessage());
            return false;
        }

        /*
        // mysql implementation alternative:

        try
        {
            $qversion = $db->quote($version);

            $sql = "insert into tbl_system ".
                   "    (name, value, created, updated) ".
                   "values ".
                   "    ('version', $qversion, now(), now()) ".
                   "on duplicate key update ".
                   "    value = $qversion, updated = now()";

            $db->exec($sql);
        }
        catch (\Exception $e)
        {
            return false;
        }

        */
    }

    public function getDbVersionNumber()
    {
        // TODO: use model to abstract out custom SQL

        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        try
        {
            $result = $db->query("select value from tbl_system where name = 'version'");

            $row = $result->fetch();
            if (!$row)
                return false;

            return $row['value'];
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    public function getDatabase()
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
                return null; // unsupported database

            $params = array();
            $params['host'] = $dbconfig['directory_host'];
            $params['port'] = $dbconfig['directory_port'];
            $params['dbname'] = $dbconfig['directory_dbname'];
            $params['username'] = $dbconfig['directory_username'];
            $params['password'] = $dbconfig['directory_password'];

            $db = \Flexio\System\ModelDb::factory($pdo_database_type, $params);
            $conn = $db->getConnection();
            $this->database = $db;
            return $db;
        }
        catch (\Exception $e)
        {
            if (isset($GLOBALS['g_config']->debug_error_log))
            {
                $data = $e->getMessage();
                file_put_contents($GLOBALS['g_config']->debug_error_log, "Connection error $data\n\n", FILE_APPEND);
            }

            return false; // can't connect
        }
    }

    public static function getDatabaseConfig()
    {
        global $g_config;

        $dbconfig = array('directory_host'     => $g_config->directory_database_host,
                          'directory_port'     => $g_config->directory_database_port,
                          'directory_username' => $g_config->directory_database_username,
                          'directory_password' => $g_config->directory_database_password,
                          'directory_dbname'   => $g_config->directory_database_dbname,
                          'datastore_host'     => $g_config->datastore_host,
                          'datastore_port'     => $g_config->datastore_port,
                          'datastore_username' => $g_config->datastore_username,
                          'datastore_password' => $g_config->datastore_password,
                          'datastore_dbname'   => $g_config->datastore_dbname
                          );

        return $dbconfig;
    }

    public function setTimezone($tz)
    {
        $db = $this->getDatabase();
        if ($db === false)
            return $this->fail(Model::ERROR_NO_DATABASE);

        if (!is_string($tz) || strlen($tz) == 0)
            return $this->fail(Model::ERROR_INVALID_PARAMETER);

        try
        {
            if ($this->dbtype == 'mysql')
                $sql = 'SET time_zone = ' . $db->quote($tz);
            else if ($this->dbtype == 'postgres')
                $sql = 'SET SESSION TIME ZONE ' . $db->quote($tz);
            else
                return $this->fail(Model::ERROR_INVALID_PARAMETER); // unsupported database

            $this->database->exec($sql);
            return true;
        }
        catch (\Exception $e)
        {
            return $this->fail(Model::ERROR_READ_FAILED, 'Unable to update database settings');
        }
    }

    public function setError($code, $message = null, $exception_message = null)
    {
        if (is_null($message))
        {
            switch ($code)
            {
                default:                                  $message = _('Operation failed');            break;
                case \Model::ERROR_UNDEFINED:              $message = _('Operation failed');            break;
                case \Model::ERROR_GENERAL:                $message = _('General error');               break;
                case \Model::ERROR_UNIMPLEMENTED:          $message = _('Unimplemented');               break;
                case \Model::ERROR_NO_DATABASE:            $message = _('Database not available');      break;
                case \Model::ERROR_NO_MODEL:               $message = _('Model not available');         break;
                case \Model::ERROR_NO_SERVICE:             $message = _('Service not available');       break;
                case \Model::ERROR_NO_OBJECT:              $message = _('Object not available');        break;
                case \Model::ERROR_MISSING_PARAMETER:      $message = _('Missing parameter');           break;
                case \Model::ERROR_INVALID_PARAMETER:      $message = _('Invalid parameter');           break;
                case \Model::ERROR_INVALID_SYNTAX:         $message = _('Invalid syntax');              break;
                case \Model::ERROR_CREATE_FAILED:          $message = _('Could not create object');     break;
                case \Model::ERROR_DELETE_FAILED:          $message = _('Could not delete object');     break;
                case \Model::ERROR_WRITE_FAILED:           $message = _('Could not write to object');   break;
                case \Model::ERROR_READ_FAILED:            $message = _('Could not read from object');  break;
                case \Model::ERROR_INSUFFICIENT_RIGHTS:    $message = _('Insufficient rights');         break;
                case \Model::ERROR_SIZE_LIMIT_EXCEEDED:    $message = _('Size limit exceeded');         break;
            }
        }

        if (isset($GLOBALS['g_config']->debug_error_log))
        {
            ob_start();
            debug_print_backtrace();
            $data = ob_get_clean();

            $excp = '';
            if ($exception_message)
                $excp = ' Exception message: ' . $exception_message;

            file_put_contents($GLOBALS['g_config']->debug_error_log, "Model error '$message' set!$excp\n$data\n\n", FILE_APPEND);
        }

        $this->errors[] = array('code' => $code, 'message' => $message);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        if (empty($this->errors))
            return false;

        return true;
    }

    public function clearErrors()
    {
        $this->errors = array();
    }

    public function fail($code, $message = null, $exception_message = null)
    {
        $this->setError($code, $message, $exception_message);
        return false;
    }

    private function loadModel($type)
    {
        switch ($type)
        {
            default:
                return false;

            // note: undefined type isn't a valid model, so it's not included
            case \Model::TYPE_USER           : return $this->user;
            case \Model::TYPE_PROJECT        : return $this->project;
            case \Model::TYPE_PIPE           : return $this->pipe;
            case \Model::TYPE_STREAM         : return $this->stream;
            case \Model::TYPE_CONNECTION     : return $this->connection;
            case \Model::TYPE_COMMENT        : return $this->comment;
            case \Model::TYPE_PROCESS        : return $this->process;
            case \Model::TYPE_TOKEN          : return $this->token;
        }
    }

    private function generateUniqueEid()
    {
        // generate an eid that doesn't already exists in tbl_object
        $db = $this->getDatabase();
        if ($db === false)
            return false; // internal function, so don't flag an error

        $eid = \Flexio\Base\Eid::generate();
        $qeid = $db->quote($eid);

        $result = $db->fetchOne("select eid from tbl_object where eid = $qeid");

        if ($result === false)
            return $eid;

        return $this->generateUniqueEid();
    }

    public static function check($params, $checks)
    {
        // validate the parameters; if they check out, then return
        // the cleaned parameters
        if (($params = \Flexio\Base\Validator::getInstance()->check($params, $checks)) !== false)
            return $params;

        // if the parameters don't check out, return false
        return false;
    }

    public static function isValidType($type)
    {
        if (!is_string($type))
            return false;

        switch ($type)
        {
            default:
                return false;

            case \Model::TYPE_UNDEFINED:
                return false;

            case \Model::TYPE_OBJECT:
            case \Model::TYPE_USER:
            case \Model::TYPE_PROJECT:
            case \Model::TYPE_PIPE:
            case \Model::TYPE_STREAM:
            case \Model::TYPE_CONNECTION:
            case \Model::TYPE_COMMENT:
            case \Model::TYPE_PROCESS:
            case \Model::TYPE_TOKEN:
                return true;
        }
    }

    public static function isValidEdge($edge)
    {
        if (!is_string($edge))
            return false;

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
                return true;
        }
    }

    public static function isValidStatus($status)
    {
        if (!is_string($status))
            return false;

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

    public static function encodePassword($password)
    {
        return '{SSHA}' . self::hashPasswordSHA1($password);
    }

    public static function hashPasswordSHA1($password)
    {
        return sha1('wecRucaceuhZucrea9UzARujUph5cf8Z' . $password);
    }

    public static function checkPasswordHash($hashpw, $password)
    {
        // input should be strings
        if (!is_string($hashpw))
            return false;
        if (!is_string($password))
            return false;

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

    private static function buildStatusString($status_arr)
    {
        if (!is_array($status_arr))
            return '';

        $status_str = '';
        foreach ($status_arr as $key => $value)
        {
            if (Model::isValidStatus($value) === false)
                continue;

            if (strlen($status_str) > 0)
                $status_str .= ',';

            $status_str .= "'" . $value . "'";
        }

        return $status_str;
    }
}
