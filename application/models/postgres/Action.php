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
                'invoked_from'   => $params['invoked_from'] ?? '',
                'invoked_by'     => $params['invoked_by'] ?? '',
                'action_type'    => $params['action_type'] ?? '',
                'action_info'    => $params['action_info'] ?? '{}',
                'action_target'  => $params['action_target'] ?? '',
                'result_type'    => $params['result_type'] ?? '',
                'result_info'    => $params['result_info'] ?? '{}',
                'started'        => $params['started'] ?? null,
                'finished'       => $params['finished'] ?? null,
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
                'invoked_from'   => array('type' => 'string',  'required' => false),
                'invoked_by'     => array('type' => 'string',  'required' => false),
                'action_type'    => array('type' => 'string',  'required' => false),
                'action_info'    => array('type' => 'string',  'required' => false),
                'action_target'  => array('type' => 'string',  'required' => false),
                'result_type'    => array('type' => 'string',  'required' => false),
                'result_info'    => array('type' => 'string',  'required' => false),
                'started'        => array('type' => 'string',  'required' => false),
                'finished'      => array('type' => 'string',  'required' => false),
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $process_arr = $validator->getParams();
        $process_arr['updated'] = \Flexio\System\System::getTimestamp();

        $db = $this->getDatabase();
        $db->beginTransaction();
        try
        {
            // see if the action exists; return false otherwise; this check is to
            // achieve the same behavior as other model set functions
            $row = $db->fetchRow("select tac.eid as eid
                                  from tbl_action tac
                                  where tac.eid = ?
                                 ", $eid);
            if (!$row)
            {
                $db->commit();
                return false;
            }

            // set the properties
            $db->update('tbl_action', $process_arr, 'eid = ' . $db->quote($eid));
            $db->commit();
            return true;
        }
        catch (\Exception $e)
        {
            $db->rollback();
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
        }
    }

    public function get(string $eid) // TODO: add return type
    {
        if (!\Flexio\Base\Eid::isValid($eid))
            return false; // don't flag an error, but acknowledge that object doesn't exist

        $row = false;
        $db = $this->getDatabase();
        try
        {
            $row = $db->fetchRow("select tac.eid as eid,
                                         tac.invoked_from as invoked_from,
                                         tac.invoked_by as invoked_by,
                                         tac.action_type as action_type,
                                         tac.action_info as action_info,
                                         tac.action_target as action_target,
                                         tac.result_type as result_type,
                                         tac.result_info as result_info,
                                         tac.started as started,
                                         tac.finished as finished,
                                         tac.created as created,
                                         tac.updated as updated
                                  from tbl_action tac
                                  where tac.eid = ?
                                 ", $eid);
         }
         catch (\Exception $e)
         {
             throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
         }

        if (!$row)
            return false; // don't flag an error, but acknowledge that object doesn't exist

        return array('eid'           => $row['eid'],
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
