<?php
/**
 *
 * Copyright (c) 2015, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2017-12-12
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "insert",      // string, required
    "path": "",          // string, required
    "values": []         // array, required
    // TODO: fill out
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['insert'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
*/

class Insert implements \Flexio\IFace\IJob
{
    private $properties = array();

    public static function validate(array $task) : array
    {
        $errors = array();
        return $errors;
    }

    public static function run(\Flexio\IFace\IProcess $process, array $task) : void
    {
        unset($task['op']);
        \Flexio\Jobs\Util::replaceParameterTokens($process, $task);

        $object = new static();
        $object->properties = $task;
        $object->run_internal($process);
    }

    private function run_internal(\Flexio\IFace\IProcess $process) : void
    {
        // create job adds new streams; don't clear existing streams
        $params = $this->getJobParameters();
        $path = $params['path'] ?? '';
        $values = $params['values'] ?? [];

        if (!is_string($path) || strlen($path) == 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED, "Path parameter must specify an insert target");

        $vfs = new \Flexio\Services\Vfs($process->getOwner());
        $vfs->setProcess($process);


        if (is_array($values))
        {
            $all_elements_are_arrays = true;
            foreach ($values as $v)
            {
                if (!is_array($v))
                {
                    $all_elements_are_arrays = false;
                    break;
                }
            }

            if (!$all_elements_are_arrays)
            {
                $values = [ $values ];
            }
        }

        if (!$vfs->insert($path, $values))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::WRITE_FAILED);
    }

    private function getJobParameters() : array
    {
        return $this->properties;
    }
}
