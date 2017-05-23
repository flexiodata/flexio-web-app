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


declare(strict_types=1);
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

    public static function create(\Flexio\Object\Process $process = null, array $properties = null) : \Flexio\Jobs\Base
    {
        $object = new static();

        // set the type and the process
        $object->type = static::MIME_TYPE;
        $object->process = $process;

        // create the empty input and output context
        $object->input = \Flexio\Object\Context::create();
        $object->output = \Flexio\Object\Context::create();

        // set the default properties
        $object->properties = json_decode($object::TEMPLATE,true);

        // if properties are specified, set them
        if (isset($properties))
        {
            // if the properties are set, make sure the input type matches
            if (!isset($properties['type']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

            if ($properties['type'] !== $object->getType())
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::MISSING_PARAMETER);

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

    public function getType() : string
    {
        return $this->type;
    }

    public function getProperties() : array
    {
        return $this->properties;
    }

    public function getInput() : \Flexio\Object\Context
    {
        return $this->input;
    }

    public function getOutput() : \Flexio\Object\Context
    {
        return $this->output;
    }

    public function run()
    {
    }

    protected function isRunMode() : bool
    {
        $process = $this->getProcess();
        if ($process === false)
            return false;

        return $process->isRunMode();
    }
}
