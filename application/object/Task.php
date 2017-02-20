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
 * @subpackage Controller
 */


namespace Flexio\Object;


class Task
{
    private $task;

    public static function manifest()
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
                'type' => \CalcFieldJob::MIME_TYPE,
                'class' => 'CalcFieldJob',
                'name' => 'Add Calculation',
                'verb' => 'Processing'
            ),
            array(
                'type' => \ConvertJob::MIME_TYPE,
                'class' => 'ConvertJob',
                'name' => 'Convert File',
                'verb' => 'Converting'
            ),
            array(
                'type' => \CopyJob::MIME_TYPE,
                'class' => 'CopyJob',
                'name' => '',
                'verb' => 'Processing'
            ),
            array(
                'type' => \CreateJob::MIME_TYPE,
                'class' => 'CreateJob',
                'name' => '',
                'verb' => 'Processing'
            ),
            array(
                'type' => \DistinctJob::MIME_TYPE,
                'class' => 'DistinctJob',
                'name' => 'Remove Duplicates',
                'verb' => 'Removing duplicates from'
            ),
            array(
                'type' => \DuplicateJob::MIME_TYPE,
                'class' => 'DuplicateJob',
                'name' => 'Identify Duplicates',
                'verb' => 'Identifying duplicates in'
            ),
            array(
                'type' => \EmailSendJob::MIME_TYPE,
                'class' => 'EmailSendJob',
                'name' => 'Email',
                'verb' => 'Sending email'
            ),
            array(
                'type' => \OutputJob::MIME_TYPE,
                'class' => 'OutputJob',
                'name' => 'Output',
                'verb' => 'Outputting'
            ),
            array(
                'type' => \FilterJob::MIME_TYPE,
                'class' => 'FilterJob',
                'name' => 'Filter',
                'verb' => 'Processing'
            ),
            array(
                'type' => \FindReplaceJob::MIME_TYPE,
                'class' => 'FindReplaceJob',
                'name' => 'Find and Replace',
                'verb' => 'Replacing values in'
            ),
            array(
                'type' => \GrepJob::MIME_TYPE,
                'class' => 'GrepJob',
                'name' => '',
                'verb' => 'Processing'
            ),
            array(
                'type' => \GroupJob::MIME_TYPE,
                'class' => 'GroupJob',
                'name' => 'Summarize',
                'verb' => 'Summarizing'
            ),
            array(
                'type' => \InputJob::MIME_TYPE,
                'class' => 'InputJob',
                'name' => 'Input',
                'verb' => 'Inputting'
            ),
            array(
                'type' => \LimitJob::MIME_TYPE,
                'class' => 'LimitJob',
                'name' => 'Limit',
                'verb' => 'Limiting'
            ),
            array(
                'type' => \MergeJob::MIME_TYPE,
                'class' => 'MergeJob',
                'name' => 'Merge',
                'verb' => 'Merging'
            ),
            array(
                'type' => \NopJob::MIME_TYPE,
                'class' => 'NopJob',
                'name' => '',
                'verb' => 'Processing'
            ),
            array(
                'type' => \PromptJob::MIME_TYPE,
                'class' => 'PromptJob',
                'name' => 'Ask For Information',
                'verb' => 'Waiting for information'
            ),
            array(
                'type' => \ExecuteJob::MIME_TYPE,
                'class' => 'ExecuteJob',
                'name' => '',
                'verb' => 'Running a program on'
            ),
            array(
                'type' => \RenameColumnJob::MIME_TYPE,
                'class' => 'RenameColumnJob',
                'name' => 'Rename Columns',
                'verb' => 'Renaming columns in'
            ),
            array(
                'type' => \RenameFileJob::MIME_TYPE,
                'class' => 'RenameFileJob',
                'name' => 'Rename Files',
                'verb' => 'Renaming file'
            ),
            array(
                'type' => \SearchJob::MIME_TYPE,
                'class' => 'SearchJob',
                'name' => 'Search',
                'verb' => 'Searching'
            ),
            array(
                'type' => \SelectColumnJob::MIME_TYPE,
                'class' => 'SelectColumnJob',
                'name' => 'Select Columns',
                'verb' => 'Selecting columns in'
            ),
            array(
                'type' => \SleepJob::MIME_TYPE,
                'class' => 'SleepJob',
                'name' => 'Sleep',
                'verb' => 'Sleeping'
            ),
            array(
                'type' => \SortJob::MIME_TYPE,
                'class' => 'SortJob',
                'name' => 'Sort',
                'verb' => 'Sorting'
            ),
            array(
                'type' => \TransformJob::MIME_TYPE,
                'class' => 'TransformJob',
                'name' => 'Transform',
                'verb' => 'Transforming'
            )
        );

        return $manifest;
    }

    public static function create($properties = null)
    {
        $object = new static();
        $object->task = array();

        // if the input is a string, treat as json and decode it
        if (is_string($properties))
            $properties = json_decode($properties, true);

        // if the input is a task array, add on an id to each step
        if (is_array($properties))
        {
            foreach ($properties as $p)
            {
                $object->push($p);
            }
        }

        return $object;
    }

    public static function load($task)
    {
        // similar to create (we don't have eids for tasks yet),
        // but assumes a valid task array has already been created

        $object = new static();
        $object->task = array();

        if ($task instanceof \Flexio\Object\Task)
        {
            $object->task = $task->get();
            return $object;
        }

        if (is_array($task))
        {
            $object->task = $task;
            return $object;
        }

        return false;
    }

    public function get()
    {
        // returns the list of commands
        return $this->task;
    }

    public function push($command)
    {
        // note: adds a raw command to the end of the task array
        $this->addTaskStep($command, null);
        return $this;
    }

    public function pop()
    {
        // removes a command from the end of the task array
        array_pop($this->task);
        return $this;
    }

    public function addTaskStep($command, $index = null)
    {
        // make sure the command is in the proper format
        $task_step = self::convertCommand($command);
        if ($task_step === false)
            return false;

        // TODO: any supplied ids shouldn't exist in the application as
        // a registered object, since tasks eids aren't currently registered;
        // so if an eid is supplied, make sure it's valid and that it isn't
        // an eid for another item in the application; if either of these
        // is true, unset the eid so that a new task eid is generated
        if (isset($task_step['eid']))
        {
            $object = \Flexio\Object\Store::load($task_step['eid']);
            if ($object !== false || !\Eid::isValid($task_step['eid']))
                unset($task_step['eid']);
        }

        $task_step = self::addEidToTaskStep($task_step);
        $task_count = count($this->task);

        // if the index is something besides an integer, set the index
        // to the number of elements in the task list (e.g. insert the
        // task at the end of the task list)
        if (!is_integer($index))
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

    public function deleteTaskStep($task_eid)
    {
        if (!\Eid::isValid($task_eid))
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

    public function setTaskStep($task_eid, $command)
    {
        if (!\Eid::isValid($task_eid))
            return $this;

        $task_step = self::convertCommand($command);
        if ($task_step === false)
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

    public function getTaskStep($task_eid)
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

    public function setParams($variables)
    {
        if (!is_array($variables) || count($variables) === 0)
            return $this;

        $new_task = array();
        foreach ($this->task as $t)
        {
            $new_task[] = \Flexio\Object\Task::updateVariables($t, $variables);
        }

        $this->task = $new_task;
        return $this;
    }

    private static function updateVariables($task, $variables)
    {
        $updated_task = (array)$task;
        if (isset($updated_task['params']))
        {
            $original_params = $task['params'];
            $updated_params = null;

            self::updateTaskItemWithVariable($variables, $original_params, $updated_params);
            $updated_task['params'] = $updated_params;
        }

        return $updated_task;
    }

    private static function updateTaskItemWithVariable($variables, $original_item, &$updated_item)
    {
        // set the initial output to the input
        $updated_item = $original_item;

        // iterate through the object until we have a string
        if (is_array($original_item) || is_object($original_item))
        {
            $updated = false;
            foreach ($original_item as $key => $value)
            {
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
        {
            // note: for now, variables are alphanumeric-plus-underscore-plus-hyphen string values
            // that are enclosed in brackets that begin with a $: (e.g. ${variable_name})

            // replace any variables in the string
            $updated = false;
            $updated_item = $original_item;

            foreach ($variables as $variable_key => $variable_value)
            {
                // make sure variables are appropriately named
                if (!preg_match('/^[a-zA-Z_-][a-zA-Z0-9_-]*$/', $variable_key))
                    continue;

                $variable_match_name = '${'.$variable_key.'}';
                $variable_match_regex = '/\$\{'.$variable_key.'\}/';

                // see if the variable matches anything in the string
                if (!preg_match($variable_match_regex, $original_item))
                    continue;

                // if the variable matches the entire value, replace the
                // value with the variable value and type; if it matches
                // part of the value, do a string replacement
                if ($variable_match_name === $original_item)
                {
                    $updated_item = $variable_value;
                }
                 else
                {
                    // use true/false text for boolean value replacements in a string
                    if ($variable_value === true)
                        $variable_value = 'true';
                    if ($variable_value === false)
                        $variable_value = 'false';

                    $updated_item = preg_replace($variable_match_regex, $variable_value, $updated_item);
                }

                $updated = true;
            }

            return $updated;
        }

        // item is some other primitive, such as a number or a boolean;
        // don't do anything
        return false;
    }

    private static function addEidToTaskStep($step)
    {
        // if a task eid isn't set, then add one

        // TODO: eventually, we may want to check if eid is unique and store it in the
        // database; however, for now, since individual task steps are always interfaced
        // with in connection with a pipe, we don't have to worry about global uniqueness
        // since we have a unique pipe eid
        if (!isset($step['eid']))
        {
            $item['eid'] = \Eid::generate();
            $step = array_merge($item, $step);
        }

        return $step;
    }

    private static function convertCommand($command)
    {
        $task_step = false;

        if ($command instanceof \IJob)
            $task_step = $command->getProperties();

        if (is_array($command))
            $task_step = $command;

        if (is_string($command))
        {
            $command_decoded = json_decode($command, true);
            if (isset($command_decoded))
                $task_step = $command_decoded;
        }

        return $task_step;
    }
}
