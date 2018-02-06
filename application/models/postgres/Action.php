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
    public function record(array $params) : bool
    {

/*
// TODO: update action model; possible table structure:

--
-- Table structure for table tbl_action
--

DROP TABLE IF EXISTS tbl_action;
CREATE TABLE tbl_action (
  id serial,
  eid varchar(12) NOT NULL default '',           // the eid of the action
  invoker_type varchar(3) NOT NULL default '',   // the type of agent used to invoke the action: api, email, scheduler, etc
  invoked_from text default '',                  // where the action was invoked from; e.g. api, email, the scheduler, etc
  invoked_by varchar(12) NOT NULL default '',    // user that invoked the action
  action_type text default '',                   // the type of action being peformed
  action_params json,                            // the parameters passed to the action
  action_object varchar(12) NOT NULL default '', // the eid of the object being acted on
  result_type text default '',                   // the type of result; success or failure
  result_info json,                              // extra info about the result in case of failure
  started timestamp NULL default NULL,           // when the action was invoked
  finished timestamp NULL default NULL,          // when the action finished
  created timestamp NULL default NULL,
  updated timestamp NULL default NULL,
  PRIMARY KEY (id),
  UNIQUE (eid)
);
*/


        $db = $this->getDatabase();
        try
        {
            $timestamp = \Flexio\System\System::getTimestamp();
            $process_arr = array(
                'user_eid' => $params['user_eid'],
                'subject_eid' => $params['subject_eid'],
                'object_eid' => $params['object_eid'],
                'action' => $params['action'],
                'params' => json_encode($params),
                'created' => $timestamp,
                'updated' => $timestamp,
            );

            // add the properties
            if ($db->insert('tbl_action', $process_arr) === false)
                throw new \Exception();

            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
}
