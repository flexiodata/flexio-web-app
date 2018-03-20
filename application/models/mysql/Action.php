<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-07-21
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Action extends ModelBase
{
    public function create(array $params = null) : string
    {
        $db = $this->getDatabase();
        $db->beginTransaction(); // needed to make sure eid generation is safe
        try
        {
            $eid = $this->generateActionEid();
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'eid'            => $eid,
                'created'        => $timestamp,
                'updated'        => $timestamp
            );

            if ($db->insert('tbl_action', $process_arr) === false)
                throw new \Exception();

            $db->commit();
            return $eid;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }

    public function set(string $eid, array $params) : bool
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false;

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db = $this->getDatabase();
        try
        {
            // see if the action exists; return false otherwise; this check is to
            // achieve the same behavior as other model set functions
            $row = $db->fetchRow("select tac.eid as eid
                                  from tbl_action tac
                                  where tac.eid = ?
                                 ", $eid);
            if (!$row)
                return false;

            // set the properties
            $db->update('tbl_action', $process_arr, 'eid = ' . $db->quote($eid));
            return true;
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('eid', 'eid_status', 'created_min', 'created_max');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);

        $rows = array();
        try
        {
            $query = "select * from tbl_action where $filter_expr";
            $rows = $db->fetchAll($query);
         }
         catch (\Exception $e)
         {
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
         }

        if (!$rows)
            return array();

        $output = array();
        foreach ($rows as $row)
        {
            $output[] = array('eid'           => $row['eid'],
                              'invoked_from'  => $row['invoked_from'],
                              'invoked_by'    => $row['invoked_by'],
                              'action_type'   => $row['action_type'],
                              'action_info'   => $row['action_info'],
                              'action_target' => $row['action_target'],
                              'result_type'   => $row['result_type'],
                              'result_info'   => $row['result_info'],
                              'started'       => $row['started'],
                              'finished'      => $row['finished'],
                              'created'       => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'       => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }

    public function get(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $filter = array('eid' => $eid);
        $rows = $this->list($filter);
        if (count($rows) === 0)
            return false;

        return $rows[0];
    }

    private function generateActionEid() : string
    {
        // note: this function generates a unique action eid; this function
        // is nearly identical to \Model::generateUniqueEid() except that
        // this function checks the tbl_action table for the eid since the
        // action eids aren't added to the object table; to avoid future
        // potential clashes, the object table is also checked; however,
        // the object able doesn't check the action table, but this reduces
        // some possibility of a clash

        $eid = \Flexio\Base\Eid::generate();
        $result1 = $this->getDatabase()->fetchOne("select eid from tbl_action where eid = ?", $eid);
        $result2 = $this->getDatabase()->fetchOne("select eid from tbl_object where eid = ?", $eid);

        if ($result1 === false && $result2 === false)
            return $eid;

        return $this->generateActionEid();
    }
}
