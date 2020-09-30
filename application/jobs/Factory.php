<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-03-25
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Factory
{
    private static $manifest = array(
        'convert'   => '\Flexio\Jobs\Convert',
        'echo'      => '\Flexio\Jobs\Echo1',
        'email'     => '\Flexio\Jobs\Email',
        'execute'   => '\Flexio\Jobs\Execute',
        'extract'   => '\Flexio\Jobs\Extract',
        'exit'      => '\Flexio\Jobs\Exit1',
        'grep'      => '\Flexio\Jobs\Grep',
        'limit'     => '\Flexio\Jobs\Limit',
        'lookup'    => '\Flexio\Jobs\Lookup',
        'read'      => '\Flexio\Jobs\Read',
        'render'    => '\Flexio\Jobs\Render',
        'request'   => '\Flexio\Jobs\Request',
        'search'    => '\Flexio\Jobs\Search',
        'transform' => '\Flexio\Jobs\Transform',
        'write'     => '\Flexio\Jobs\Write'
    );

    public static function getJobClass(array $task) : string
    {
        if (!isset($task['op']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _('Missing operation \'op\' task parameter'));

        $operation = $task['op'];

        if (!is_string($operation))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _('Invalid operation \'op\' task parameter'));

        // make sure the job is registered; note: this isn't strictly necessary,
        // but gives us a convenient way of limiting what jobs are available for
        // processing
        $job_class_name = self::$manifest[$operation] ?? false;
        if ($job_class_name === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, _('Invalid operation \'op\' task parameter'));

        // try to find the job file
        $class_name_parts = explode("\\", $job_class_name);
        if (!isset($class_name_parts[3]))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $job_class_file = \Flexio\System\System::getApplicationDirectory() . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $class_name_parts[3] . '.php';
        if (!@file_exists($job_class_file))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // load the job's php file and instantiate the job object
        include_once $job_class_file;
        return $job_class_name;
    }
}
