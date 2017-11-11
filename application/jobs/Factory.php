<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-11-08
 *
 * @package flexio
 * @subpackage Object
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Factory
{
    private static $manifest = array(
        'flexio.calc'      => '\Flexio\Jobs\CalcField',
        'flexio.comment'   => '\Flexio\Jobs\Comment',
        'flexio.convert'   => '\Flexio\Jobs\Convert',
        'flexio.create'    => '\Flexio\Jobs\Create',
        'flexio.distinct'  => '\Flexio\Jobs\Distinct',
        'flexio.duplicate' => '\Flexio\Jobs\Duplicate',
        'flexio.each'      => '\Flexio\Jobs\Each',
        'flexio.echo'      => '\Flexio\Jobs\Echo1',
        'flexio.email'     => '\Flexio\Jobs\Email',
        'flexio.request'   => '\Flexio\Jobs\Request',
        'flexio.output'    => '\Flexio\Jobs\Output',
        'flexio.filter'    => '\Flexio\Jobs\Filter',
        'flexio.replace'   => '\Flexio\Jobs\Replace',
        'flexio.grep'      => '\Flexio\Jobs\Grep',
        'flexio.group'     => '\Flexio\Jobs\Group',
        'flexio.input'     => '\Flexio\Jobs\Input',
        'flexio.limit'     => '\Flexio\Jobs\Limit',
        'flexio.merge'     => '\Flexio\Jobs\Merge',
        'flexio.nop'       => '\Flexio\Jobs\Nop',
        'flexio.fail'      => '\Flexio\Jobs\Fail',
        'flexio.execute'   => '\Flexio\Jobs\Execute',
        'flexio.exit'      => '\Flexio\Jobs\Exit1',
        'flexio.rename'    => '\Flexio\Jobs\Rename',
        'flexio.render'    => '\Flexio\Jobs\Render',
        'flexio.search'    => '\Flexio\Jobs\Search',
        'flexio.select'    => '\Flexio\Jobs\Select',
        'flexio.settype'   => '\Flexio\Jobs\SetType',
        'flexio.sleep'     => '\Flexio\Jobs\Sleep',
        'flexio.sort'      => '\Flexio\Jobs\Sort',
        'flexio.transform' => '\Flexio\Jobs\Transform',
        'flexio.list'      => '\Flexio\Jobs\List1'
    );

    public static function create(array $task) : \Flexio\Jobs\IJob
    {
        if (!isset($task['type']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $job_type = $task['type'];

        // make sure the job is registered; note: this isn't strictly necessary,
        // but gives us a convenient way of limiting what jobs are available for
        // processing
        $job_class_name = self::$manifest[$job_type] ?? false;
        if ($job_class_name === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // try to find the job file
        $class_name_parts = explode("\\", $job_class_name);
        if (!isset($class_name_parts[3]))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        $job_class_file = \Flexio\System\System::getApplicationDirectory() . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $class_name_parts[3] . '.php';
        if (!@file_exists($job_class_file))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        // load the job's php file and instantiate the job object
        include_once $job_class_file;
        $job = $job_class_name::create($task);

        if ($job === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::CREATE_FAILED);

        return $job;
    }
}
