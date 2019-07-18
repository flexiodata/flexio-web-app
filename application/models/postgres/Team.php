<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-07-18
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Team extends ModelBase
{
    public function list(array $filter) : array
    {
        $db = $this->getDatabase();
        $allowed_items = array('owned_by', 'member_eid', 'created_min', 'created_max');
        $filter_expr = \Filter::build($db, $filter, $allowed_items);
        $limit_expr = \Limit::build($db, $filter);

        $rows = array();
        try
        {
            $query = "select * from tbl_teammember where $filter_expr order by id $limit_expr";
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
            $output[] = array('member_eid'    => $row['member_eid'],
                              'member_status' => $row['member_status'],
                              'owned_by'      => $row['owned_by'],
                              'created_by'    => $row['created_by'],
                              'created'       => \Flexio\Base\Util::formatDate($row['created']),
                              'updated'       => \Flexio\Base\Util::formatDate($row['updated']));
        }

        return $output;
    }
}
