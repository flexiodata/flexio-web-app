<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie LLC. All rights reserved.
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


class Filter
{
    public static function build($db, array $filter_items, array $allowed_items) : string
    {
        $allowed_item_keys = array_flip($allowed_items);

        // initial condition
        $filter_expr = 'true';

        // add on allowed items from the list of filter items
        foreach ($filter_items as $key => $value)
        {
            // handle date range separately
            if ($key === 'created_min' || $key === 'created_max')
                continue;

            // all other keys, build up a equality comparison using the column name
            if (isset($value) && array_key_exists($key, $allowed_item_keys))
                $filter_expr .= (" and ($key = " . $db->quote($value) . ")");
        }

        // handle the date range
        if (isset($filter_items['created_min']) && array_key_exists('created_min', $allowed_item_keys))
        {
            $date = $filter_items['created_min'];
            $date = strtotime($date);
            if ($date !== false)
            {
                $date_clean = date('Y-m-d', $date);
                $filter_expr .= (' and (created >= ' . $db->quote($date_clean) . ')');
            }
        }

        if (isset($filter_items['created_max']) && array_key_exists('created_max', $allowed_item_keys))
        {
            $date = $filter_items['created_max'];
            $date = strtotime($date . ' + 1 days');
            if ($date !== false)
            {
                $date_clean = date('Y-m-d', $date);
                $filter_expr .= (' and (created < ' . $db->quote($date_clean) . ')'); // created is a timestamp, so use < on the next day
            }
        }

        if (strlen($filter_expr) > 0)
            $filter_expr = '(' . $filter_expr . ')';

        return $filter_expr;
    }
}

class Limit
{
    public static function build($db, array $limit_items) : string
    {
        $limit_expr = '';

        if (isset($limit_items['limit']))
        {
            $limit = (int)$limit_items['limit'];
            if ($limit < 0)
                $limit = 0;
            $limit_expr .= ' limit ' . $db->quote($limit);
        }

        if (isset($limit_items['start']))
        {
            $start = (int)$limit_items['start'];
            if ($start < 0)
                $start = 0;
            $limit_expr .= ' offset ' . $db->quote($start);
        }

        return $limit_expr;
    }
}

class Model
{
    public const TYPE_UNDEFINED      = '';
    public const TYPE_USER           = 'USR';
    public const TYPE_PIPE           = 'PIP';
    public const TYPE_STREAM         = 'STR';
    public const TYPE_CONNECTION     = 'CTN';
    public const TYPE_COMMENT        = 'CMT';
    public const TYPE_PROCESS        = 'PRC';
    public const TYPE_TOKEN          = 'TKN';
    public const TYPE_RIGHT          = 'RGH';
    public const TYPE_ACTION         = 'ACT';

    public const EDGE_UNDEFINED     = '';     // undefind edge
    public const EDGE_INVITED       = 'INV';  // user A invited user B
    public const EDGE_INVITED_BY    = 'INB';  // user A was invited by user B
    public const EDGE_SHARED_WITH   = 'SHW';  // user A shared with user B
    public const EDGE_SHARED_FROM   = 'SHF';  // user A was shared something from user B
    public const EDGE_FOLLOWING     = 'FLW';  // user A is following object B
    public const EDGE_FOLLOWED_BY   = 'FLB';  // object A is followed by user B
    public const EDGE_LINKED_TO     = 'LKT';  // object A links to object B
    public const EDGE_LINKED_FROM   = 'LKF';  // object A links from object B
    public const EDGE_COPIED_TO     = 'CPT';  // object A copied to object B
    public const EDGE_COPIED_FROM   = 'CPF';  // object A copied from object B
    public const EDGE_COMMENT_ON    = 'CMO';  // comment A is a comment on object B
    public const EDGE_HAS_COMMENT   = 'HCM';  // object A has comment B

    public const STATUS_UNDEFINED = '';
    public const STATUS_PENDING   = 'P';
    public const STATUS_AVAILABLE = 'A';
    public const STATUS_DELETED   = 'D';

    public const REGISTRY_VALUE_UNDEFINED = '';
    public const REGISTRY_VALUE_STRING    = 'S';
    public const REGISTRY_VALUE_NUMBER    = 'N';
    public const REGISTRY_VALUE_BOOLEAN   = 'B';
    public const REGISTRY_VALUE_DATE      = 'D'; // (YYYY-MM-DD)
    public const REGISTRY_VALUE_DATETIME  = 'T'; // (YYYY-MM-DD HH:MM:SS)
    public const REGISTRY_VALUE_BINARY    = 'X';

    public const PIPE_DEPLOY_MODE_UNDEFINED  = '';
    public const PIPE_DEPLOY_MODE_BUILD      = 'B';
    public const PIPE_DEPLOY_MODE_RUN        = 'R';

    public const PIPE_DEPLOY_STATUS_UNDEFINED  = '';
    public const PIPE_DEPLOY_STATUS_ACTIVE     = 'A';
    public const PIPE_DEPLOY_STATUS_INACTIVE   = 'I';

    public const PROCESS_TRIGGERED_UNDEFINED = '';
    public const PROCESS_TRIGGERED_API       = 'A';
    public const PROCESS_TRIGGERED_EMAIL     = 'E';
    public const PROCESS_TRIGGERED_SCHEDULER = 'S';
    public const PROCESS_TRIGGERED_INTERFACE = 'I';

    public const TEAM_MEMBER_STATUS_UNDEFINED = '';
    public const TEAM_MEMBER_STATUS_PENDING = 'P';
    public const TEAM_MEMBER_STATUS_INACTIVE = 'I';
    public const TEAM_MEMBER_STATUS_ACTIVE = 'A';

    public const SYSTEM_ROLE_UNDEFINED     = '';
    public const SYSTEM_ROLE_ADMINISTRATOR = 'A';

    public const TEAM_ROLE_UNDEFINED       = '';
    public const TEAM_ROLE_USER            = 'U';
    public const TEAM_ROLE_CONTRIBUTOR     = 'C';
    public const TEAM_ROLE_ADMINISTRATOR   = 'A';
    public const TEAM_ROLE_OWNER           = 'O';

    public const CONNECTION_MODE_UNDEFINED   = '';
    public const CONNECTION_MODE_RESOURCE      = 'R';
    public const CONNECTION_MODE_FUNCTION      = 'F';

    public const CONNECTION_STATUS_UNDEFINED   = '';
    public const CONNECTION_STATUS_AVAILABLE   = 'A';
    public const CONNECTION_STATUS_UNAVAILABLE = 'U';
    public const CONNECTION_STATUS_ERROR       = 'E';

    public const CONNECTION_TYPE_UNDEFINED          = '';
    public const CONNECTION_TYPE_FTP                = 'ftp';
    public const CONNECTION_TYPE_SFTP               = 'sftp';
    public const CONNECTION_TYPE_MYSQL              = 'mysql';
    public const CONNECTION_TYPE_POSTGRES           = 'postgres';
    public const CONNECTION_TYPE_ELASTICSEARCH      = 'elasticsearch';
    public const CONNECTION_TYPE_DROPBOX            = 'dropbox';
    public const CONNECTION_TYPE_BOX                = 'box';
    public const CONNECTION_TYPE_GMAIL              = 'gmail';
    public const CONNECTION_TYPE_GOOGLEDRIVE        = 'googledrive';
    public const CONNECTION_TYPE_GOOGLESHEETS       = 'googlesheets';
    public const CONNECTION_TYPE_GOOGLECLOUDSTORAGE = 'googlecloudstorage';
    public const CONNECTION_TYPE_GITHUB             = 'github';
    public const CONNECTION_TYPE_AMAZONS3           = 'amazons3';
    public const CONNECTION_TYPE_EMAIL              = 'email';
    public const CONNECTION_TYPE_SMTP               = 'smtp';
    public const CONNECTION_TYPE_HTTP               = 'http';
    public const CONNECTION_TYPE_SOCRATA            = 'socrata';
    public const CONNECTION_TYPE_LINKEDIN           = 'linkedin';
    public const CONNECTION_TYPE_TWITTER            = 'twitter';
    public const CONNECTION_TYPE_CRUNCHBASE         = 'crunchbase';
    public const CONNECTION_TYPE_KEYRING            = 'keyring';
    public const CONNECTION_TYPE_INTERCOM           = 'intercom';
    public const CONNECTION_TYPE_PRODUCTHUNT        = 'producthunt';

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

    public static function isValidType(string $type) : bool
    {
        switch ($type)
        {
            default:
                return false;

            case \Model::TYPE_UNDEFINED:
                return false;

            case \Model::TYPE_USER:
            case \Model::TYPE_PIPE:
            case \Model::TYPE_STREAM:
            case \Model::TYPE_CONNECTION:
            case \Model::TYPE_COMMENT:
            case \Model::TYPE_PROCESS:
            case \Model::TYPE_TOKEN:
            case \Model::TYPE_RIGHT:
            case \Model::TYPE_ACTION:
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

            case \Model::EDGE_INVITED:
            case \Model::EDGE_INVITED_BY:
            case \Model::EDGE_SHARED_WITH:
            case \Model::EDGE_SHARED_FROM:
            case \Model::EDGE_FOLLOWING:
            case \Model::EDGE_FOLLOWED_BY:
            case \Model::EDGE_LINKED_TO:
            case \Model::EDGE_LINKED_FROM:
            case \Model::EDGE_COPIED_TO:
            case \Model::EDGE_COPIED_FROM:
            case \Model::EDGE_COMMENT_ON:
            case \Model::EDGE_HAS_COMMENT:
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
            case \Model::STATUS_DELETED:
                return true;
        }
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

    public function createObjectBase(string $type) : string
    {
        // make sure we have a valid type
        if (!\Model::isValidType($type))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // $eid = $this->generateUniqueEid();
        $eid = \Flexio\Base\Eid::generate(); // simply generate an eid; if it isn't unique, the insert will fail because of the unique condition
        $process_arr = array(
            'eid'           => $eid,
            'eid_type'      => $type
        );

        try
        {
            $result = $this->getDatabase()->insert('tbl_object', $process_arr);
            if ($result === false)
                throw new \Exception;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }

        return $eid;
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // invalid eids can't be associated with each other
        if (!\Flexio\Base\Eid::isValid($source_eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        if (!\Flexio\Base\Eid::isValid($target_eid))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        if (!\Model::isValidEdge($newtype))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

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
                   "        tobtar.eid_type as eid_type ".
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
            }

            $sql = "select ".
                   "        target_eid as eid, ".
                   "        tobtar.eid_type as eid_type ".
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

    public function setDbVersionNumber(string $version) : bool
    {
        if (strlen($version) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

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

    public function getTableCounts() : array
    {
        $db = $this->getDatabase();

        $stat_queries = array(
            'object' => 'select count(*) as count from tbl_object',
            'association' => 'select count(*) as count from tbl_association',
            'user' => 'select count(*) as count from tbl_user',
            'token' => 'select count(*) as count from tbl_token',
            'pipe' => 'select count(*) as count from tbl_pipe',
            'connection' => 'select count(*) as count from tbl_connection',
            'process' => 'select count(*) as count from tbl_process',
            'stream' => 'select count(*) as count from tbl_stream',
            'comment' => 'select count(*) as count from tbl_comment',
            'action' => 'select count(*) as count from tbl_action',
            'registry' => 'select count(*) as count from tbl_registry',
            'system' => 'select count(*) as count from tbl_system'
        );

        try
        {
            $result = array();
            foreach ($stat_queries as $name => $query)
            {
                $count = $db->fetchOne($query);
                $result[$name] = $count;
            }
            return $result;
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
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

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

