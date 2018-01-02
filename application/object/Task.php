<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-05-03
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Task
{
    private $task;

    public static function create(array $properties = null) : \Flexio\Object\Task
    {
        $object = new static();
        $object->task = array();

        if (isset($properties))
        {
            foreach ($properties as $p)
            {
                $object->task->addTaskStep($task_step);
            }
        }

        return $object;
    }

    public function get() : array
    {
        // returns the list of commands
        return $this->task;
    }

    public function addTaskStep(array $task_step, int $index = null) : string
    {
        // TODO: any supplied ids shouldn't exist in the application as
        // a registered object, since tasks eids aren't currently registered;
        // so if an eid is supplied, make sure it's valid and that it isn't
        // an eid for another item in the application; if either of these
        // is true, unset the eid so that a new task eid is generated
        if (isset($task_step['eid']))
        {
            $object = \Flexio\Object\Store::load($task_step['eid']);
            if ($object !== false || !\Flexio\Base\Eid::isValid($task_step['eid']))
                unset($task_step['eid']);
        }

        $task_step = self::addEidToTaskStep($task_step);
        $task_count = count($this->task);

        // if the index isn't set, set the index to the number of elements
        // in the task list (e.g. insert the task at the end of the task list)
        if (!isset($index))
            $index = count($this->task);

        if ($index <= 0 )
            $index = 0;
        if ($index >= $task_count)
            $index = $task_count;

        // if the index is the same as the task count, insert the element
        // at the end of the task list
        if ($index == $task_count)
        {
            $this->task[] = $task_step;
            return $task_step['eid'];
        }

        // if the index is somewhere in the range of the task item offsets,
        // insert the task in the appropriate position based on the specified
        // index
        $new_task_list = array();
        $i = 0;

        foreach ($this->task as $t)
        {
            if ($i === $index)
                $new_task_list[] = $task_step;

            $new_task_list[] = $t;
            ++$i;
        }

        $this->task = $new_task_list;
        return $task_step['eid'];
    }

    public function deleteTaskStep(string $task_eid) : \Flexio\Object\Task
    {
        if (!\Flexio\Base\Eid::isValid($task_eid))
            return $this;

        // iterate through the tasks; if the eid of the task step
        // matches the existing task, replace the task with the
        // specified task step
        $updated_task = array();
        foreach ($this->task as $t)
        {
            $updated_task_item = $t;
            if (isset($t['eid']) && $t['eid'] === $task_eid)
                continue;

            $updated_task[] = $updated_task_item;
        }

        $this->task = $updated_task;
        return $this;
    }

    public function setTaskStep(string $task_eid, array $task_step) : \Flexio\Object\Task // TODO: add input parameter types
    {
        if (!\Flexio\Base\Eid::isValid($task_eid))
            return $this;

        // iterate through the tasks; if the eid of the task step
        // matches the existing task, replace the task with the
        // specified task step
        $updated_task = array();
        foreach ($this->task as $t)
        {
            $updated_task_item = $t;
            if (isset($t['eid']) && $t['eid'] === $task_eid)
            {
                $task_step['eid'] = $task_eid;
                $updated_task_item = $task_step;
            }

            $updated_task[] = $updated_task_item;
        }

        $this->task = $updated_task;
        return $this;
    }

    public function getTaskStep(string $task_eid) // TODO: add function return type
    {
        // iterate through the tasks; if the eid of the task step
        // matches the existing task, return the task
        foreach ($this->task as $t)
        {
            if (isset($t['eid']) && $t['eid'] === $task_eid)
                return $t;
        }

        return false;
    }

    private static function addEidToTaskStep(array $step) : array
    {
        // if a task eid isn't set, then add one

        // TODO: eventually, we may want to check if eid is unique and store it in the
        // database; however, for now, since individual task steps are always interfaced
        // with in connection with a pipe, we don't have to worry about global uniqueness
        // since we have a unique pipe eid
        if (!isset($step['eid']))
        {
            $item['eid'] = \Flexio\Base\Eid::generate();
            $step = array_merge($item, $step);
        }

        return $step;
    }
}
