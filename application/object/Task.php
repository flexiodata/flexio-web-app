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

    public static function manifest() : array
    {
        // note: manifest() resturns a list of all available jobs and their related
        // information; enum() returns a list of publicly available functions in display
        // order

        // in the following, we have:
        //    type: the command type
        //    class: the command class implementation
        //    name: the display name
        //    verb: the verb to use when describing the command running
        //    description: a description of the command
        $manifest = array(
            array(
                'type' => \Flexio\Jobs\CalcField::MIME_TYPE,
                'class' => '\Flexio\Jobs\CalcField',
                'name' => 'Add Calculation',
                'verb' => 'Processing'
            ),
            array(
                'type' => \Flexio\Jobs\Comment::MIME_TYPE,
                'class' => '\Flexio\Jobs\Comment',
                'name' => '',
                'verb' => 'Processing'
            ),
            array(
                'type' => \Flexio\Jobs\Convert::MIME_TYPE,
                'class' => '\Flexio\Jobs\Convert',
                'name' => 'Convert File',
                'verb' => 'Converting'
            ),
            array(
                'type' => \Flexio\Jobs\Create::MIME_TYPE,
                'class' => '\Flexio\Jobs\Create',
                'name' => '',
                'verb' => 'Processing'
            ),
            array(
                'type' => \Flexio\Jobs\Distinct::MIME_TYPE,
                'class' => '\Flexio\Jobs\Distinct',
                'name' => 'Remove Duplicates',
                'verb' => 'Removing duplicates from'
            ),
            array(
                'type' => \Flexio\Jobs\Duplicate::MIME_TYPE,
                'class' => '\Flexio\Jobs\Duplicate',
                'name' => 'Identify Duplicates',
                'verb' => 'Identifying duplicates in'
            ),
            array(
                'type' => \Flexio\Jobs\EmailSend::MIME_TYPE,
                'class' => '\Flexio\Jobs\EmailSend',
                'name' => 'Email',
                'verb' => 'Sending email'
            ),
            array(
                'type' => \Flexio\Jobs\Request::MIME_TYPE,
                'class' => '\Flexio\Jobs\Request',
                'name' => 'Http Request',
                'verb' => 'Making Http Request'
            ),
            array(
                'type' => \Flexio\Jobs\Output::MIME_TYPE,
                'class' => '\Flexio\Jobs\Output',
                'name' => 'Output',
                'verb' => 'Outputting'
            ),
            array(
                'type' => \Flexio\Jobs\Filter::MIME_TYPE,
                'class' => '\Flexio\Jobs\Filter',
                'name' => 'Filter',
                'verb' => 'Processing'
            ),
            array(
                'type' => \Flexio\Jobs\Replace::MIME_TYPE,
                'class' => '\Flexio\Jobs\Replace',
                'name' => 'Find and Replace',
                'verb' => 'Replacing values in'
            ),
            array(
                'type' => \Flexio\Jobs\Grep::MIME_TYPE,
                'class' => '\Flexio\Jobs\Grep',
                'name' => '',
                'verb' => 'Processing'
            ),
            array(
                'type' => \Flexio\Jobs\Group::MIME_TYPE,
                'class' => '\Flexio\Jobs\Group',
                'name' => 'Summarize',
                'verb' => 'Summarizing'
            ),
            array(
                'type' => \Flexio\Jobs\Input::MIME_TYPE,
                'class' => '\Flexio\Jobs\Input',
                'name' => 'Input',
                'verb' => 'Inputting'
            ),
            array(
                'type' => \Flexio\Jobs\Limit::MIME_TYPE,
                'class' => '\Flexio\Jobs\Limit',
                'name' => 'Limit',
                'verb' => 'Limiting'
            ),
            array(
                'type' => \Flexio\Jobs\Merge::MIME_TYPE,
                'class' => '\Flexio\Jobs\Merge',
                'name' => 'Merge',
                'verb' => 'Merging'
            ),
            array(
                'type' => \Flexio\Jobs\Nop::MIME_TYPE,
                'class' => '\Flexio\Jobs\Nop',
                'name' => '',
                'verb' => 'Processing'
            ),
            array(
                'type' => \Flexio\Jobs\Fail::MIME_TYPE,
                'class' => '\Flexio\Jobs\Fail',
                'name' => 'Fail',
                'verb' => 'Failed'
            ),
            array(
                'type' => \Flexio\Jobs\Prompt::MIME_TYPE,
                'class' => '\Flexio\Jobs\Prompt',
                'name' => 'Ask For Information',
                'verb' => 'Waiting for information'
            ),
            array(
                'type' => \Flexio\Jobs\Execute::MIME_TYPE,
                'class' => '\Flexio\Jobs\Execute',
                'name' => '',
                'verb' => 'Running a program on'
            ),
            array(
                'type' => \Flexio\Jobs\Rename::MIME_TYPE,
                'class' => '\Flexio\Jobs\Rename',
                'name' => 'Rename Columns',
                'verb' => 'Renaming columns in'
            ),
            array(
                'type' => \Flexio\Jobs\Render::MIME_TYPE,
                'class' => '\Flexio\Jobs\Render',
                'name' => 'Render Document',
                'verb' => 'Rendering'
            ),
            array(
                'type' => \Flexio\Jobs\Search::MIME_TYPE,
                'class' => '\Flexio\Jobs\Search',
                'name' => 'Search',
                'verb' => 'Searching'
            ),
            array(
                'type' => \Flexio\Jobs\Select::MIME_TYPE,
                'class' => '\Flexio\Jobs\Select',
                'name' => 'Select Columns',
                'verb' => 'Selecting columns in'
            ),
            array(
                'type' => \Flexio\Jobs\SetType::MIME_TYPE,
                'class' => '\Flexio\Jobs\SetType',
                'name' => 'Change type',
                'verb' => 'Processing'
            ),
            array(
                'type' => \Flexio\Jobs\Sleep::MIME_TYPE,
                'class' => '\Flexio\Jobs\Sleep',
                'name' => 'Sleep',
                'verb' => 'Sleeping'
            ),
            array(
                'type' => \Flexio\Jobs\Sort::MIME_TYPE,
                'class' => '\Flexio\Jobs\Sort',
                'name' => 'Sort',
                'verb' => 'Sorting'
            ),
            array(
                'type' => \Flexio\Jobs\Transform::MIME_TYPE,
                'class' => '\Flexio\Jobs\Transform',
                'name' => 'Transform',
                'verb' => 'Transforming'
            ),
            array(
                'type' => \Flexio\Jobs\List1::MIME_TYPE,
                'class' => '\Flexio\Jobs\List1',
                'name' => 'List',
                'verb' => 'Listing'
            )
        );

        return $manifest;
    }

    public static function create(array $properties = null) : \Flexio\Object\Task
    {
        $object = new static();
        $object->task = array();

        if (isset($properties))
        {
            foreach ($properties as $p)
            {
                $object->push($p);
            }
        }

        return $object;
    }

    public function get() : array
    {
        // returns the list of commands
        return $this->task;
    }

    public function push(array $task_step) : \Flexio\Object\Task // TODO: add input parameter types
    {
        // note: adds a raw command to the end of the task array
        $this->addTaskStep($task_step, null);
        return $this;
    }

    public function pop() : \Flexio\Object\Task
    {
        // removes a command from the end of the task array
        array_pop($this->task);
        return $this;
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

    public function setParams(array $variables) : \Flexio\Object\Task
    {
        if (count($variables) === 0)
            return $this;

        $new_task = array();
        foreach ($this->task as $t)
        {
            $new_task[] = \Flexio\Object\Task::updateVariables($t, $variables);
        }

        $this->task = $new_task;
        return $this;
    }

    private static function updateVariables(array $task, array $variables) : array
    {
        $updated_task = $task;
        if (isset($updated_task['params']))
        {
            $original_params = $task['params'];
            $updated_params = null;

            self::updateTaskItemWithVariable($variables, $original_params, $updated_params);
            $updated_task['params'] = $updated_params;
        }

        return $updated_task;
    }

    private static function updateTaskItemWithVariable(array $variables, $original_item, &$updated_item) : bool
    {
        // set the initial output to the input
        $updated_item = $original_item;

        // iterate through the object until we have a string
        if (is_array($original_item) || is_object($original_item))
        {
            $updated = false;
            foreach ($original_item as $key => $value)
            {
                // replace any variables in the key
                self::replaceValueWithVariable($variables, $key, $key);

                // replace any variables in the value
                $new_value = $value;
                if (self::updateTaskItemWithVariable($variables, $value, $new_value))
                {
                    // if the new value is null, unset the original item; otherwise
                    // replace the value

                    if (!isset($new_value))
                    {
                        unset($updated_item[$key]);
                    }
                     else
                    {
                        $updated_item[$key] = $new_value;
                        $updated = true;
                    }
                }
            }

            return $updated;
        }

        if (is_string($original_item))
            return self::replaceValueWithVariable($variables, $original_item, $updated_item);

        // item is some other primitive, such as a number or a boolean;
        // don't do anything
        return false;
    }

    private static function replaceValueWithVariable(array $variables, $old_value, &$new_value) : bool
    {
        // returns true if value was updated; false otherwise

        // replace any variables in the string
        $updated = false;
        $new_value = $old_value;

        foreach ($variables as $variable_key => $variable_value)
        {
            // make sure variables are appropriately named
            if (!preg_match('/^[a-zA-Z_-][a-zA-Z0-9_-]*$/', (string)$variable_key))
                continue;

            $variable_match_name = '${'.$variable_key.'}';
            $variable_match_regex = '/\$\{'.$variable_key.'\}/';

            // see if the variable matches anything in the string
            if (!preg_match($variable_match_regex, (string)$old_value))
                continue;

            // if the variable matches the entire value, replace the
            // value with the variable value and type; if it matches
            // part of the value, do a string replacement
            if ($variable_match_name === $old_value)
            {
                $new_value = $variable_value;
            }
                else
            {
                // use true/false text for boolean value replacements in a string
                if ($variable_value === true)
                    $variable_value = 'true';
                if ($variable_value === false)
                    $variable_value = 'false';

                $new_value = preg_replace($variable_match_regex, $variable_value, $new_value); // replace on updated value to support multiple variable replacements
            }

            $updated = true;
        }

        return $updated;
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
