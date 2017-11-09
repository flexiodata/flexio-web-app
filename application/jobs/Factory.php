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
        \Flexio\Jobs\CalcField::MIME_TYPE => '\Flexio\Jobs\CalcField',
        \Flexio\Jobs\Comment::MIME_TYPE   => '\Flexio\Jobs\Comment',
        \Flexio\Jobs\Convert::MIME_TYPE   => '\Flexio\Jobs\Convert',
        \Flexio\Jobs\Create::MIME_TYPE    => '\Flexio\Jobs\Create',
        \Flexio\Jobs\Distinct::MIME_TYPE  => '\Flexio\Jobs\Distinct',
        \Flexio\Jobs\Duplicate::MIME_TYPE => '\Flexio\Jobs\Duplicate',
        \Flexio\Jobs\Each::MIME_TYPE      => '\Flexio\Jobs\Each',
        \Flexio\Jobs\Echo1::MIME_TYPE     => '\Flexio\Jobs\Echo1',
        \Flexio\Jobs\EmailSend::MIME_TYPE => '\Flexio\Jobs\EmailSend',
        \Flexio\Jobs\Request::MIME_TYPE   => '\Flexio\Jobs\Request',
        \Flexio\Jobs\Output::MIME_TYPE    => '\Flexio\Jobs\Output',
        \Flexio\Jobs\Filter::MIME_TYPE    => '\Flexio\Jobs\Filter',
        \Flexio\Jobs\Replace::MIME_TYPE   => '\Flexio\Jobs\Replace',
        \Flexio\Jobs\Grep::MIME_TYPE      => '\Flexio\Jobs\Grep',
        \Flexio\Jobs\Group::MIME_TYPE     => '\Flexio\Jobs\Group',
        \Flexio\Jobs\Input::MIME_TYPE     => '\Flexio\Jobs\Input',
        \Flexio\Jobs\Limit::MIME_TYPE     => '\Flexio\Jobs\Limit',
        \Flexio\Jobs\Merge::MIME_TYPE     => '\Flexio\Jobs\Merge',
        \Flexio\Jobs\Nop::MIME_TYPE       => '\Flexio\Jobs\Nop',
        \Flexio\Jobs\Fail::MIME_TYPE      => '\Flexio\Jobs\Fail',
        \Flexio\Jobs\Execute::MIME_TYPE   => '\Flexio\Jobs\Execute',
        \Flexio\Jobs\Exit1::MIME_TYPE     => '\Flexio\Jobs\Exit1',
        \Flexio\Jobs\Rename::MIME_TYPE    => '\Flexio\Jobs\Rename',
        \Flexio\Jobs\Render::MIME_TYPE    => '\Flexio\Jobs\Render',
        \Flexio\Jobs\Search::MIME_TYPE    => '\Flexio\Jobs\Search',
        \Flexio\Jobs\Select::MIME_TYPE    => '\Flexio\Jobs\Select',
        \Flexio\Jobs\SetType::MIME_TYPE   => '\Flexio\Jobs\SetType',
        \Flexio\Jobs\Sleep::MIME_TYPE     => '\Flexio\Jobs\Sleep',
        \Flexio\Jobs\Sort::MIME_TYPE      => '\Flexio\Jobs\Sort',
        \Flexio\Jobs\Transform::MIME_TYPE => '\Flexio\Jobs\Transform',
        \Flexio\Jobs\List1::MIME_TYPE     => '\Flexio\Jobs\List1'
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
