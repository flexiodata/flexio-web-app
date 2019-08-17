<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-03-28
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Object;


class Process extends \Flexio\Object\Base implements \Flexio\IFace\IObject
{
    public function __construct()
    {
    }

    public function __toString()
    {
        $object = array(
            'eid' => $this->getEid(),
            'eid_type' => $this->getType()
        );
        return json_encode($object);
    }

    public static function accumulateStats(array $filter, string $object_column) : array
    {
        // construct a date range from the filter
        $date_start = $filter['created_min'] ?? false;
        $date_end = $filter['created_max'] ?? false;

        if ($date_start === false || $date_end === false)
            return array();

        try
        {
            $date_start = (new \DateTime($date_start ))->format('Y-m-d');
            $date_end = (new \DateTime($date_end))->format('Y-m-d');
        }
        catch (\Exception $e)
        {
            return array();
        }

        $eid_type = false;
        if ($object_column == 'pipe_eid')
            $eid_type = 'PIP';
        if ($object_column == 'user_eid')
            $eid_type = 'USR';
        if ($eid_type === false)
            return array();

        $total_count = 0;
        $daily_count = \Flexio\Base\Util::createDateRangeArray($date_start, $date_end);

        $object_totals = array();
        $stats = \Flexio\System\System::getModel()->process->summary($filter);

        foreach ($stats as $s)
        {
            $object_eid = $s[$object_column];
            $process_created = $s['created'];
            $process_count = $s['total_count'];

            // initialize the object totals if we haven't yet started accumulating them
            if (!isset($object_totals[$object_eid]))
            {
                $object_totals[$object_eid]['total_count'] = 0;
                $object_totals[$object_eid]['daily_count'] = \Flexio\Base\Util::createDateRangeArray($date_start, $date_end);
            }

            // overall totals
            $total_count += $process_count;
            $daily_count[$process_created] += $process_count;

            // object totals
            $object_totals[$object_eid]['total_count'] += $process_count;
            $object_totals[$object_eid]['daily_count'][$process_created] += $process_count;
        }

        $object_totals_reformatted = array();
        foreach ($object_totals as $key => $value)
        {
            $object_totals_reformatted[] = array(
                'user' => array('eid' => $key,'eid_type' => $eid_type),
                'total_count' => $value['total_count'],
                'daily_count' => array_values($value['daily_count'])
            );
        }

        $result = array(
            'header' => array(
                'start_date' => $date_start,
                'end_date' => $date_end,
                'days' => array_keys(\Flexio\Base\Util::createDateRangeArray($date_start, $date_end)),
                'total_count' => $total_count,
                'daily_count' => array_values($daily_count)
            ),
            'detail' => $object_totals_reformatted
        );

        return $result;
    }

    public static function list(array $filter) : array
    {
        $object = new static();
        $process_model = $object->getModel()->process;
        $items = $process_model->list($filter);

        $objects = array();
        foreach ($items as $i)
        {
            $o = new static();
            $o->properties =self::formatProperties($i);
            $o->setEid($o->properties['eid']);
            $objects[] = $o;
        }

        return $objects;
    }

    public static function load(string $eid) : \Flexio\Object\Process
    {
        $object = new static();
        $process_model = $object->getModel()->process;
        $properties = $process_model->get($eid);

        $object->setEid($eid);
        $object->clearCache();
        $object->properties = self::formatProperties($properties);
        return $object;
    }

    public static function create(array $properties = null) : \Flexio\Object\Process
    {
        if (!isset($properties))
            $properties = array();

        // if the pipe info is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['pipe_info']))
        {
            if (!is_array($properties['pipe_info']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

            $properties['pipe_info'] = json_encode($properties['pipe_info']);
        }

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::flattenParams($properties['task']);
            $properties['task'] = json_encode($properties['task']);
        }

        // if no process mode is specified, run everything
        if (!isset($properties['process_mode']))
            $properties['process_mode'] = \Flexio\Jobs\Process::MODE_RUN;

        // if no process status is set, set a default
        if (!isset($properties['process_status']))
            $properties['process_status'] = \Flexio\Jobs\Process::STATUS_PENDING;

        $object = new static();
        $process_model = $object->getModel()->process;
        $local_eid = $process_model->create($properties);

        $object->setEid($local_eid);
        $object->clearCache();

        return $object;
    }

    public function delete() : \Flexio\Object\Process
    {
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_model->delete($this->getEid());
        return $this;
    }

    public function set(array $properties) : \Flexio\Object\Process
    {
        // TODO: add properties check

        // if the pipe info is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['pipe_info']))
        {
            if (!is_array($properties['pipe_info']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['pipe_info'] = json_encode($properties['pipe_info']);
        }

        // if the input or output info are set, make sure they're an object and then encode it as
        // JSON for storage
        if (isset($properties) && isset($properties['input']))
        {
            if (!is_array($properties['input']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['input'] = json_encode($properties['input']);
        }
        if (isset($properties) && isset($properties['output']))
        {
            if (!is_array($properties['output']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['output'] = json_encode($properties['output']);
        }

        // if the task is set, make sure it's an object and then encode it as JSON for storage
        if (isset($properties) && isset($properties['task']))
        {
            if (!is_array($properties['task']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

            $properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($properties['task']);
            $properties['task'] = \Flexio\Jobs\Base::flattenParams($properties['task']);
            $properties['task'] = json_encode($properties['task']);
        }

        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_model->set($this->getEid(), $properties);
        return $this;
    }

    public function get() : array
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties;
    }

    public function getType() : string
    {
        return \Model::TYPE_PROCESS;
    }

    public function setOwner(string $user_eid) : \Flexio\Object\Process
    {
        $properties = array('owned_by' => $user_eid);
        $this->set($properties);
        return $this;
    }

    public function getOwner() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['owned_by']['eid'];
    }

    public function setStatus(string $status) : \Flexio\Object\Process
    {
        if ($status === \Model::STATUS_DELETED)
            return $this->delete();

        $this->clearCache();
        $process_model = $this->getModel()->process;
        $result = $process_model->set($this->getEid(), array('eid_status' => $status));
        return $this;
    }

    public function getStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['eid_status'];
    }

    public function cancel() : \Flexio\Object\Process
    {
        $this->clearCache();
        $process_model = $this->getModel()->process;
        $process_status = $process_model->getProcessStatus($this->getEid());

        switch ($process_status)
        {
            // if a job is already completed, don't allow it to be cancelled
            case \Flexio\Jobs\Process::STATUS_CANCELLED:
            case \Flexio\Jobs\Process::STATUS_FAILED:
            case \Flexio\Jobs\Process::STATUS_COMPLETED:
                return $this;
        }

        $process_model->setProcessStatus($this->getEid(), \Flexio\Jobs\Process::STATUS_CANCELLED);
        return $this;
    }

    public function getProcessStatus() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_status'];
    }

    public function setTask(array $task) : \Flexio\Object\Process
    {
        $properties = array();
        $properties['task'] = $task;
        return $this->set($properties);
    }

    public function getTask() : array
    {
        $properties = $this->get();
        return $properties['task'];
    }

    public function getMode() : string
    {
        if ($this->isCached() === false)
            $this->populateCache();

        return $this->properties['process_mode'];
    }

    private function isCached() : bool
    {
        // a process may be run in the background and update values
        // in the model; never cache process data so an object always
        // represents the latest state of a process
        return false;

        // note: following is normal cache behavior
        // if (!$this->properties)
        //    return false;
        //
        // return true;
    }

    private function clearCache() : void
    {
        $this->properties = null;
    }

    private function populateCache() : void
    {
        $process_model = $this->getModel()->process;
        $properties = $process_model->get($this->getEid());
        $this->properties = self::formatProperties($properties);
    }

    private static function formatProperties(array $properties) : array
    {
        $mapped_properties = \Flexio\Base\Util::mapArray(
            [
                "eid" => null,
                "eid_type" => null,
                "eid_status" =>  null,
                "parent" => null,
                "process_mode" => null,
                "task" => null,
                "output" => null,
                "triggered_by" => null,
                "started_by" => null,
                "started" => null,
                "finished" => null,
                "duration" => null,
                "process_info" => null,
                "process_status" => null,
                "owned_by" => null,
                "created_by" => null,
                "created" => null,
                "updated" => null
            ],
        $properties);

        // sanity check: if the data record is missing, then eid will be null
        if (!isset($mapped_properties['eid']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        // get the pipe info
        $pipe_info = null;
        if (isset($properties['pipe_info']))
            $pipe_info = @json_decode($properties['pipe_info'],true);
            if ($pipe_info === false)
                $pipe_info = null;

        // expand the parent pipe info
        $mapped_properties['parent'] = array(
            'eid' => $properties['parent_eid'],
            'eid_type' => \Model::TYPE_PIPE,
            'eid_status' => $pipe_info['eid_status'] ?? "",
            'name' => $pipe_info['name'] ?? "",
            'title' => $pipe_info['title'] ?? "",
            'description' => $pipe_info['description'] ?? "",
            'deploy_mode' => $pipe_info['deploy_mode'] ?? "",
            'created' => $pipe_info['created'] ?? "",
            'updated' => $pipe_info['updated'] ?? ""
        );

        // get the output if it exists
        if (isset($properties['output']))
            $pipe_output = @json_decode($properties['output'],true);
            if ($pipe_output !== false)
            {
                if (isset($pipe_output['stream']))
                    $mapped_properties['output'] = array(
                        "eid" => $pipe_output['stream'],
                        "eid_type" => \Model::TYPE_STREAM
                    );
                 else
                    $mapped_properties['output'] = null;
            }

        // unpack the primary process task json
        if (isset($mapped_properties['task']))
        {
            $task = @json_decode($mapped_properties['task'],true);
            if ($task !== false)
            {
                $mapped_properties['task'] = $task;
                $mapped_properties['task'] = \Flexio\Jobs\Base::fixEmptyParams($mapped_properties['task']);
            }
        }

        // unpack the primary process process info json
        if (isset($mapped_properties['process_info']))
        {
            $process_info = @json_decode($mapped_properties['process_info'],true);
            if ($process_info !== false)
                $mapped_properties['process_info'] = $process_info;
        }

        // expand the user info
        $mapped_properties['owned_by'] = array(
            'eid' => $properties['owned_by'],
            'eid_type' => \Model::TYPE_USER
        );
        $mapped_properties['created_by'] = array(
            'eid' => $properties['created_by'],
            'eid_type' => \Model::TYPE_USER
        );

        return $mapped_properties;
    }
}

