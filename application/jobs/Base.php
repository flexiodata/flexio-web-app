<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-06-10
 *
 * @package flexio
 * @subpackage Jobs
 */


namespace Flexio\Jobs;


require_once __DIR__ . DIRECTORY_SEPARATOR . 'Abstract.php';

class Base implements \Flexio\Jobs\IJob
{
    // basic job template
    const MIME_TYPE = 'flexio';
    const TEMPLATE = <<<EOD
    {
        "type": "flexio",
        "params": {
        }
    }
EOD;

    // job properties
    private $process;
    private $type;
    private $input;
    private $output;

    // properties for derived classes; these the job parameters
    protected $properties;

    public function __construct()
    {
    }

    public static function create($process = null, $properties = null)
    {
        $object = new static();

        // if a process variable is specified, make sure it's an instance
        // of the Process class
        if (isset($process) && !($process instanceof \Flexio\Object\Process))
            return false;

        // set the type and the process
        $object->type = static::MIME_TYPE;
        $object->process = $process;

        // create the empty input and output collection
        $object->input = \Flexio\Object\Collection::create();
        $object->output = \Flexio\Object\Collection::create();

        // set the default properties
        $object->properties = json_decode($object::TEMPLATE,true);

        // if the input is a string, treat as json and decode it
        if (is_string($properties))
            $properties = json_decode($properties, true);

        // if properties are specified, set them
        if (isset($properties) && is_array($properties))
        {
            // if the properties are set, make sure the input type matches
            if (!isset($properties['type']))
                return false;

            if ($properties['type'] !== $object->getType())
                return false;

            // TODO: temporarily disable

            // make sure the properties are valid
            //$validator = \Flexio\Base\ValidatorSchema::check($object->properties, $object::SCHEMA);
            //if ($validator->hasErrors())
            //    return false;

            $object->properties = $properties;
        }

        return $object;
    }

    public function getProcess()
    {
        if (!isset($this->process))
            return false;

        return $this->process;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getProperties()
    {
        if (!isset($this->properties))
            return false;

        return $this->properties;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function run()
    {
        return;
    }

    protected function isRunMode()
    {
        $process = $this->getProcess();
        if ($process === false)
            return false;

        return $process->isRunMode();
    }

    protected function setProgress($pct)
    {
        $process = $this->getProcess();
        if ($process === false)
            return false;

        $process->setProgress($pct);
    }

    protected function fail($error, $message, $file, $line)
    {
        $process = $this->getProcess();
        if ($process === false)
            return false;

        return $process->fail($error, $message, $file, $line);
    }

    protected function failValidation($validator, $file, $line)
    {
        $process = $this->getProcess();
        if ($process === false)
            return false;

        return $this->failValidation($validator, $file, $line);
    }
}
