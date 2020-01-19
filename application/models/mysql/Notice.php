<?php
/**
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-01-14
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Notice extends ModelBase
{
    public function create(array $params) : string
    {
        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'notice_type'         => array('type' => 'string', 'required' => false, 'default' => ''),
                'owned_by'            => array('type' => 'string', 'required' => false, 'default' => ''),
                'created_by'          => array('type' => 'string', 'required' => false, 'default' => '')
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $process_arr = $validator->getParams();

        $db = $this->getDatabase();
        try
        {
            $timestamp = \Flexio\System\System::getTimestamp();

            $process_arr['created'] = $timestamp;
            $process_arr['updated'] = $timestamp;

            if ($db->insert('tbl_notice', $process_arr) === false)
                throw new \Exception();

            return ''; // TODO: return something?
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);
        }
    }

    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('notice_type', 'owned_by', 'created_by', 'created_min', 'created_max');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_notice where $filter_expr order by id $limit_expr";
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
            $output[] = array('notice_type'         => $row['notice_type'],
                              'owned_by'            => $row['owned_by'],
                              'created_by'          => $row['created_by'],
                              'created'             => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'             => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }

    public function purge(string $owner_eid) : bool
    {
        // this function deletes rows for a given owner
        if (!\Flexio\Base\Eid::isValid($owner_eid))
            return false;

        $db = $this->getDatabase();
        try
        {
            $qowner_eid = $db->quote($owner_eid);
            $sql = "delete from tbl_notice where owned_by = $qowner_eid";
            $rows_affected = $db->exec($sql);

            return ($rows_affected > 0 ? true : false);
        }
        catch (\Exception $e)
        {
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::DELETE_FAILED);
        }
    }
}
