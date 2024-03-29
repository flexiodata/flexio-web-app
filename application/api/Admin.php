<?php
/**
 *
 * Copyright (c) 2017, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams, Aaron L. Williams
 * Created:  2017-11-13
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Admin
{
    public static function system(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $package_info = \Flexio\System\System::getPackageInfo();
        $git_version = \Flexio\System\System::getGitRevision();

        $elasticsearch = \Flexio\System\System::getSearchCache();
        $search_cache_version = $elasticsearch->info();

        $result = array(
            "application" => array(
                "name" => $package_info['name'] ?? '',
                "version" =>  $package_info['version'] ?? '',
                "sha" => $git_version
            ),
            "database" =>  array(
                "version" => \Flexio\System\System::getModel()->getDbVersionNumber()
            ),
            "index" => array(
                "version" => $search_cache_version
            )
        );

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function settings(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = self::checkServerSettings();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function users(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'username'     => array('type' => 'string',  'required' => false),
                'email'        => array('type' => 'string',  'required' => false),
                'owned_by'     => array('type' => 'string',  'required' => false),
                'created_by'   => array('type' => 'string',  'required' => false),
                'start'        => array('type' => 'integer', 'required' => false),
                'tail'         => array('type' => 'integer', 'required' => false),
                'limit'        => array('type' => 'integer', 'required' => false),
                'created_min'  => array('type' => 'date',    'required' => false),
                'created_max'  => array('type' => 'date',    'required' => false),
                'trialend_min' => array('type' => 'date',    'required' => false),
                'trialend_max' => array('type' => 'date',    'required' => false),
                'is_customer'  => array('type' => 'string',  'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // convert owned_by/created_by identifiers into eids
        $validated_query_params = self::convertUserIdentifierQueryParams($validated_query_params);

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => [\Model::STATUS_PENDING, \Model::STATUS_AVAILABLE]);
        $users = \Flexio\System\System::getModel()->user->list($filter);

        $result = array();
        foreach ($users as $u)
        {
            $user_info = array();
            $user_info['eid'] = $u['eid'] ?? '';
            $user_info['eid_status'] = $u['eid_status'] ?? '';
            $user_info['username'] = $u['username'] ?? '';
            $user_info['email'] = $u['email'] ?? '';
            $user_info['first_name'] = $u['first_name'] ?? '';
            $user_info['last_name'] = $u['last_name'] ?? '';
            $user_info['stripe_customer_id'] = $u['stripe_customer_id'] ?? '';
            $user_info['stripe_subscription_id'] = $u['stripe_subscription_id'] ?? '';
            $user_info['trial_end_date'] = $u['trial_end_date'] ?? '';
            $user_info['created'] = $u['created'] ?? '';
            $user_info['updated'] = $u['updated'] ?? '';
            $result[] = $user_info;
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function actions(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'string',  'required' => false),
                'created_by'  => array('type' => 'string',  'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // convert owned_by/created_by identifiers into eids
        $validated_query_params = self::convertUserIdentifierQueryParams($validated_query_params);

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $actions = \Flexio\Object\Action::list($filter);

        $result = array();
        foreach ($actions as $a)
        {
            $action_info = $a->get();

            try
            {
                $user_eid = $action_info['request_created_by'] ?? false;
                if ($user_eid !== false)
                {
                    if (!isset($user_cache[$user_eid]))
                    {
                        $user = \Flexio\Object\User::load($user_eid);
                        $u = $user->get();

                        $user_info = array();
                        $user_info['eid'] = $u['eid'] ?? '';
                        $user_info['username'] = $u['username'] ?? '';
                        $user_info['email'] = $u['email'] ?? '';
                        $user_info['first_name'] = $u['first_name'] ?? '';
                        $user_info['last_name'] = $u['last_name'] ?? '';
                        $user_info['created'] = $u['created'] ?? '';

                        $user_cache[$user_eid] = $user_info;
                    }

                    $action_info['request_created_by'] = $user_cache[$user_eid];
                }
            }
            catch (\Flexio\Base\Exception $e)
            {
            }

            $result[] = $action_info;
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function connections(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'string',  'required' => false),
                'created_by'  => array('type' => 'string',  'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // convert owned_by/created_by identifiers into eids
        $validated_query_params = self::convertUserIdentifierQueryParams($validated_query_params);

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $connections = \Flexio\Object\Connection::list($filter);

        $result = array();
        foreach ($connections as $c)
        {
            $result[] = $c->get();
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function pipes(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'string',  'required' => false),
                'created_by'  => array('type' => 'string',  'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // convert owned_by/created_by identifiers into eids
        $validated_query_params = self::convertUserIdentifierQueryParams($validated_query_params);

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $pipes = \Flexio\Object\Pipe::list($filter);

        $result = array();
        foreach ($pipes as $p)
        {
            $result[] = $p->get();
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function processes(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'string',  'required' => false),
                'created_by'  => array('type' => 'string',  'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // convert owned_by/created_by identifiers into eids
        $validated_query_params = self::convertUserIdentifierQueryParams($validated_query_params);

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $processes = \Flexio\Object\Process::list($filter);

        $result = array();
        foreach ($processes as $p)
        {
            $process_info = $p->get();

            $process_info_subset = array();
            $process_info_subset['eid'] = $process_info['eid'];
            $process_info_subset['eid_type'] = $process_info['eid_type'];
            $process_info_subset['eid_status'] = $process_info['eid_status'];
            $process_info_subset['parent'] = $process_info['parent'] ?? null;
            $process_info_subset['triggered_by'] = $process_info['triggered_by'];
            $process_info_subset['started_by'] = $process_info['started_by'];
            $process_info_subset['started'] = $process_info['started'];
            $process_info_subset['finished'] = $process_info['finished'];
            $process_info_subset['duration'] = $process_info['duration'];
            $process_info_subset['process_status'] = $process_info['process_status'];
            $process_info_subset['owned_by'] = $process_info['owned_by'];
            $process_info_subset['created'] = $process_info['created'];
            $process_info_subset['updated'] = $process_info['updated'];

            $result[] = $process_info_subset;
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function process_summary_byuser(\Flexio\Api\Request $request) : void
    {
        $query_params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();
        $owner_user_eid = $request->getOwnerFromUrl();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($query_params, array(
                'owned_by'    => array('type' => 'string',  'required' => false),
                'created_by'  => array('type' => 'string',  'required' => false),
                'start'       => array('type' => 'integer', 'required' => false),
                'tail'        => array('type' => 'integer', 'required' => false),
                'limit'       => array('type' => 'integer', 'required' => false),
                'created_min' => array('type' => 'date',    'required' => false),
                'created_max' => array('type' => 'date',    'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        $validated_query_params = $validator->getParams();

        // convert owned_by/created_by identifiers into eids
        $validated_query_params = self::convertUserIdentifierQueryParams($validated_query_params);

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $filter = array('eid_status' => \Model::STATUS_AVAILABLE);
        $filter = array_merge($validated_query_params, $filter); // give precedence to fixed status
        $stats = self::accumulateProcessStats($filter, 'user_eid');

        // populate the user info if possible
        if (isset($stats['detail']))
        {
            foreach ($stats['detail'] as &$s)
            {
                try
                {
                    $user = \Flexio\Object\User::load($s['user']['eid']);
                    $user_info = $user->get();
                    $info['eid'] = $user_info['eid'];
                    $info['username'] = $user_info['username'];
                    $info['email'] = $user_info['email'];
                    $info['first_name'] = $user_info['first_name'];
                    $info['last_name'] = $user_info['last_name'];
                    $info['created'] = $user_info['created'] ?? '';
                    $s['user'] = $info;
                }
                catch (\Flexio\Base\Exception $e)
                {
                }
            }
        }

        $results = $stats;
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($results);
    }

    public static function statsDatabase(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $result = \Flexio\System\System::getModel()->getTableCounts();
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function statsCluster(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $elasticsearch = \Flexio\System\System::getSearchCache();
        $result = $elasticsearch->getClusterStats();

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function statsIndices(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $elasticsearch = \Flexio\System\System::getSearchCache();
        $result = $elasticsearch->getIndicesStats();

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function statsIndicesBasic(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $elasticsearch = \Flexio\System\System::getSearchCache();
        $stats = $elasticsearch->getIndicesStats();
        $indices = $stats['indices'];

        $result = [];
        foreach ($indices as $index_name => $index_info)
        {
            $pipe_info = array();
            try
            {
                $pipe = \Flexio\Object\Pipe::load($index_name);
                $pipe_info = $pipe->get();
            }
            catch (\Flexio\Base\Exception $e)
            {

            }


            $uuid = $index_info['uuid'] ?? '';
            $doc_count = $index_info['primaries']['docs'] ?? null;
            $size = $index_info['primaries']['store']['size_in_bytes'] ?? null;

            // TODO: include other information from the stats
            $result[] = array(
                            'name' => $index_name,
                            'uuid' => $uuid,
                            'size' => $size,
                            'doc_count' => $doc_count,
                            'pipe_eid' => $pipe_info['eid'] ?? '',
                            'pipe_eid_status' => $pipe_info['eid_status'] ?? '',
                            'pipe_owner' => $pipe_info['owned_by'] ?? ''
                        );
        }

        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function email(\Flexio\Api\Request $request) : void
    {
        $f = \Flexio\System\System::openPhpInputStream();
        if ($f === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        \Flexio\Api\Trigger::handleEmail($f);

        $result = array('success' => true);
        \Flexio\Api\Response::sendContent($result);
    }

    public static function cron(\Flexio\Api\Request $request) : void
    {
        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators to do this
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // the scheduler script uses UTC as its timezone
        //date_default_timezone_set('UTC');
        //$dt = \Flexio\Base\Util::getDateTimeParts();
        //printf("Scheduler time is: %02d:%02d\n", $dt['hours'], $dt['minutes']);
        $result = array('success' => true);

        $cron = new \Flexio\Api\Cron;
        $cron->run();
    }

    public static function userPurge(\Flexio\Api\Request $request) : void
    {
        // purges a list of users; note: this deletes users and their associated
        // records from the database; it can't be undone

        $post_params = $request->getPostParams();
        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators to do this
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // for bulk purge, incoming form is an array of users to purge in
        // the following format where the value of the eid key is the user
        // eid:
        // [{"eid": ""}, {"eid": ""}, ...]

        // make sure we have an array of at least one item to delete
        $user_eids_to_purge = $post_params;
        if (!is_array($user_eids_to_purge))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);
        if (count($user_eids_to_purge) === 0)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX);

        // purge the users
        $result = array();
        foreach ($user_eids_to_purge as $u)
        {
            try
            {
                // load the user
                $user_eid = $u['eid'] ?? '';
                $user = \Flexio\Object\User::load($user_eid);
                $user_info = $user->get();

                // purge the user
                $purge_result = $user->purge();
                if ($purge_result === true)
                {
                    $result[] = array(
                        'eid' => $user_info['eid'],
                        'eid_status' => $user_info['eid_status'],
                        'username' => $user_info['username'],
                        'first_name' => $user_info['first_name'],
                        'last_name' => $user_info['last_name'],
                        'email' => $user_info['email']
                    );
                }
            }
            catch (\Flexio\Base\Exception $e)
            {
                // fall through; don't error out
            }
        }

        // report the users that have been purged
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function indexCleanup(\Flexio\Api\Request $request) : void
    {
        // deletes any indices having with an associated pipe that's been deleted

        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators to do this
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $elasticsearch = \Flexio\System\System::getSearchCache();
        $stats = $elasticsearch->getIndicesStats();
        $indices = $stats['indices'];

        $result = [];
        foreach ($indices as $index_name => $index_info)
        {
            $pipe_info = array();
            try
            {
                // for each index, see if we have a pipe
                $pipe = \Flexio\Object\Pipe::load($index_name);
                if ($pipe->getStatus() !== \Model::STATUS_DELETED)
                    continue;

                // if the pipe has been deleted, then delete the corresponding index
                $elasticsearch->deleteIndex($pipe->getEid());

                // report the deleted indices
                $uuid = $index_info['uuid'] ?? '';
                $doc_count = $index_info['primaries']['docs'] ?? null;
                $size = $index_info['primaries']['store']['size_in_bytes'] ?? null;

                // TODO: include other information from the stats
                $result[] = array(
                                'name' => $index_name,
                                'uuid' => $uuid,
                                'size' => $size,
                                'doc_count' => $doc_count,
                                'pipe_eid' => $pipe_info['eid'] ?? '',
                                'pipe_eid_status' => $pipe_info['eid_status'] ?? '',
                                'pipe_owner' => $pipe_info['owned_by'] ?? ''
                            );
            }
            catch (\Flexio\Base\Exception $e)
            {
                // pipe didn't load; fall through
            }
        }

        // report the indices that have been deleted
        $request->setResponseCreated(\Flexio\Base\Util::getCurrentTimestamp());
        \Flexio\Api\Response::sendContent($result);
    }

    public static function indexJson(\Flexio\Api\Request $request) : void
    {
        // TEST: used for issuing raw JSON queries to an index

        $request_url = $request->getUrl();
        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators to do this
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the index path
        $path = $request_url;

        $pos = strpos($path, '/admin/index/json/');
        if ($pos === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST);

        $path = parse_url($request_url, PHP_URL_PATH);
        $path = substr($path, $pos+18);

        // get the index eid from the path
        $pipe_name_parts = explode('/', $path);
        $owner_eid = \Flexio\Object\User::getEidFromIdentifier($pipe_name_parts[0]);
        $pipe_eid = \Flexio\Object\Pipe::getEidFromName($owner_eid, $pipe_name_parts[1]);

        if (!$pipe_eid)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_REQUEST, "this pipe doesn't exist");

        // get the query from the input
        $php_stream_handle = \Flexio\System\System::openPhpInputStream();
        $post_content = fread($php_stream_handle, 4096);
        fclose($php_stream_handle);

        $post_content = json_decode($post_content, true);
        if ($post_content === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, 'input needs to be in JSON format');

        // run the query and return the results
        $elasticsearch = \Flexio\System\System::getSearchCache();
        $result = $elasticsearch->search($pipe_eid, $post_content);
        $result = json_encode($result);

        echo($result);
        exit(0);
    }

    public static function indexSql(\Flexio\Api\Request $request) : void
    {
        // EXPERIMENTAL

        $requesting_user_eid = $request->getRequestingUser();

        // only allow administrators to do this
        $requesting_user = \Flexio\Object\User::load($requesting_user_eid);
        if ($requesting_user->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNAVAILABLE);
        if ($requesting_user->isAdministrator() !== true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        // get the query from the input
        $php_stream_handle = \Flexio\System\System::openPhpInputStream();
        $post_content = fread($php_stream_handle, 4096);
        fclose($php_stream_handle);

        $post_content = json_decode($post_content, true);
        if ($post_content === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, 'input needs to be in JSON format');

        if (!isset($post_content['query']))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_SYNTAX, 'missing query parameter');

        $search_query = $post_content['query'];

        // TODO: smarter parsing of tablename with error handling

        // replace the table path with the index eid
        $pipe_name_start_offset= strpos($search_query, 'from ') + 5;
        $pipe_name_start = trim(substr($search_query, $pipe_name_start_offset));
        $arr = explode(' ', $pipe_name_start);
        $pipe_name = $arr[0];
        $pipe_name_parts = explode('/', $pipe_name);
        $owner_eid = \Flexio\Object\User::getEidFromIdentifier($pipe_name_parts[0]);
        $pipe_eid = \Flexio\Object\Pipe::getEidFromName($owner_eid, $pipe_name_parts[1]);
        $search_query = str_replace($pipe_name, $pipe_eid, $search_query);

        // run the query and return the results
        $elasticsearch = \Flexio\System\System::getSearchCache();
        $result = $elasticsearch->searchSql($search_query);
        $result = json_encode($result);

        echo($result);
        exit(0);
    }

    private static function checkServerSettings() : array
    {
        $messages = array();

        $val = self::convertToNumber(ini_get('post_max_size'));
        if ($val < 1048576000)
            $messages[] = 'post_max_size must be 1000M or greater';

        $val = self::convertToNumber(ini_get('upload_max_filesize'));
        if ($val < 1048576000)
            $messages[] = 'upload_max_filesize must be 1000M or greater';

        $val = self::convertToNumber(ini_get('memory_limit'));
        if ($val < 268435456)
            $messages[] = 'memory_limit must be 256M or greater';

        $val = self::convertToNumber(ini_get('max_execution_time'));
        if ($val > 0 && $val < 3600)
            $messages[] = 'max_execution_time must be 3600 or greater.  Current value: ' . $val;

        $val = self::convertToNumber(ini_get('max_input_time'));
        if ($val < 3600)
            $messages[] = 'max_input_time must be 3600 or greater';

        $val = function_exists('mcrypt_get_iv_size');
        if (!$val)
            $messages[] = 'mcrypt library not installed; please install php5-mcrypt';

        $val = function_exists('curl_init');
        if (!$val)
            $messages[] = 'curl library not installed; please install php5-curl';

        $val = function_exists('imagecreatefrompng');
        if (!$val)
            $messages[] = 'php gd library not installed; please install php5-gd';

        $val = class_exists("SQLite3", false);
        if (!$val)
            $messages[] = 'php sqlite library not installed; please install php5-sqlite';

        //$val = class_exists("Imagick", false);
        //if (\Flexio\System\System::isPlatformLinux() && !$val)
        //    $messages[] = 'php imagick library not installed; please install php5-imagick';

        $val = file_exists(\Flexio\System\System::getBinaryPath('php'));
        if (!$val)
            $messages[] = 'cannot find php command line executable. On Linux, install php5-cli. On Windows, make sure $g_config->dir_home is set.';

        if (\Flexio\System\System::isPlatformWindows() && !class_exists("COM", false))
            $messages[] = 'please enable extension=php_com_dotnet.dll in php.ini';

        if (\Flexio\System\System::isPlatformLinux())
        {
            // make sure certain debian/ubuntu packages are installed
        }

        return $messages;
    }

    private static function convertToNumber(string $size_str) : int
    {
        switch (strtoupper(substr($size_str, -1)))
        {
            case 'G': return (int)$size_str * 1073741824;
            case 'M': return (int)$size_str * 1048576;
            case 'K': return (int)$size_str * 1024;
            default:  return (int)$size_str;
        }
    }

    private static function convertUserIdentifierQueryParams(array $params) : array
    {
        // converts user owned_by and created_by query params that
        // are identifiers into eids
        $converted_params = $params;

        if (isset($converted_params['owned_by']))
        {
            $owned_by_param = $converted_params['owned_by'];
            if (!\Flexio\Base\Eid::isValid($owned_by_param))
            {
                $converted_owned_by_param = \Flexio\Object\User::getEidFromIdentifier($owned_by_param);
                if ($converted_owned_by_param)
                    $converted_params['owned_by'] = $converted_owned_by_param;
            }
        }

        if (isset($converted_params['created_by']))
        {
            $created_by_param = $converted_params['created_by'];
            if (!\Flexio\Base\Eid::isValid($created_by_param))
            {
                $converted_created_by_param = \Flexio\Object\User::getEidFromIdentifier($created_by_param);
                if ($converted_created_by_param)
                    $converted_params['created_by'] = $converted_created_by_param;
            }
        }

        return $converted_params;
    }

    private static function accumulateProcessStats(array $filter, string $object_column) : array
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
}
