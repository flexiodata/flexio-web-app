<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-05-05
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Task
{
    public static function create(array $tasks) : array
    {
        // utility function for converting an array of task info into
        // a properly formatting task sequence

        $sequence = array();
        foreach ($tasks as $t)
        {
            // put the operation in the "op" parameter and everything else
            // in the "params" parameter
            $op = $t['op'] ?? false;
            unset($t['op']);

            $item = array();

            if ($op !== false)
                $item['op'] = $op;

            $item['params'] = $t;

            $sequence[] = $item;
        }

        // wrap all the tasks in a sequence operation
        $result = array();
        $result['op'] = 'sequence';
        $result['params']['items'] = $sequence;

        return $result;
    }
}
