<?php
/**
 *
 * Copyright (c) 2020, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-03-31
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;


class Mount
{
    // utility functions that can be run as functions in a process using queue
    // for mounting a connection and importing items; here as a job because
    // the process is long enough that it needs to be run in the background,
    // and therefore, needs to be able to be added to the process queue

    private $connection;
    private $properties;

    public static function create()
    {
        $object = new static();
        return $object;
    }

    public static function import(\Flexio\Jobs\Process $process, array $callback_params) : void
    {
        // note: imports the items for a mount or integration

        // create the object and set the connection
        $object = new static();
        $connection_eid = $callback_params['connection_eid'] ?? '';
        $object->connection = \Flexio\Object\Connection::load($connection_eid);
        $object->properties = $callback_params;

        // if the connection mode isn't a mount; there are no associated pipes
        $connection_info = $object->getConnection()->get();
        $connection_eid = $connection_info['eid'];
        $connection_mode = $connection_info['connection_mode'];

        if ($connection_mode !== \Model::CONNECTION_MODE_FUNCTION)
            return;

        // set the status to updating
        $object->getConnection()->set(array('eid_status' => \Model::STATUS_UPDATING));

        // delete the pipes that are no longer in the connection
        // don't delete associated connections since we're only re-adding
        // the pipes, but we want to keep the configuration information
        // and associated connections
        $object->deleteAssociatedPipes();
        // $object->deleteAssociatedConnections();

        // create pipes for new items in the connection
        $object->createAssociatedPipes();

        // populate associated pipe caches
        $object->populatePipeIndexCaches();

        // set the status back to what it was
        $object->getConnection()->set(array('eid_status' => \Model::STATUS_AVAILABLE));
    }

    public static function delete(\Flexio\Jobs\Process $process, array $callback_params) : void
    {
        // create the object and set the connection
        $object = new static();
        $connection_eid = $callback_params['connection_eid'] ?? '';
        $object->connection = \Flexio\Object\Connection::load($connection_eid);

        // delete the associated pipes
        $object->deleteAssociatedPipes();
        $object->deleteAssociatedConnections();

        // TODO: delete associated cache
    }

    private function getConnection() : \Flexio\Object\Connection
    {
        return $this->connection;
    }

    private function getProperties() : array
    {
        return $this->properties;
    }

    private function deleteAssociatedPipes()
    {
        // note: deletes associated pipes for a mounted connection

        $connection_info = $this->getConnection()->get();
        $connection_eid = $connection_info['eid'];
        $connection_mode = $connection_info['connection_mode'];

        // if the connection mode isn't a mount; there are no associated pipes
        if ($connection_mode !== \Model::CONNECTION_MODE_FUNCTION)
            return;

        // get a list of pipes for the connection that haven't already
        // been deleted
        $pipe_model = \Flexio\System\System::getModel()->pipe;
        $pipes_to_update = array(
            'parent_eid' => $connection_eid,
            'eid_status' => [\Model::STATUS_PENDING, \Model::STATUS_UPDATING, \Model::STATUS_AVAILABLE]
        );
        $pipes = \Flexio\Object\Pipe::list($pipes_to_update);

        // delete any associated index for these pipes
        $elasticsearch = \Flexio\System\System::getSearchCache();

        $result = array();
        foreach ($pipes as $p)
        {
            $info = $p->get();
            $result[] = array(
                'eid' => $info['eid'],
                'eid_status' => $info['eid_status'],
                'name' => $info['name']
            );

            $pipe_eid = $p->getEid();
            $elasticsearch->deleteIndex($pipe_eid);
        }

        // set the deleted flag
        $process_arr = array('eid_status' => \Model::STATUS_DELETED, 'name' => '');
        $pipe_model->update($pipes_to_update, $process_arr);
    }

    private function deleteAssociatedConnections()
    {
        // note: deletes associated connections for a mounted connection

        $connection_info = $this->getConnection()->get();
        $connection_eid = $connection_info['eid'];
        $connection_mode = $connection_info['connection_mode'];

        // if the connection mode isn't a mount; there are no associated connections
        if ($connection_mode !== \Model::CONNECTION_MODE_FUNCTION)
            return;

        $connection_model = \Flexio\System\System::getModel()->connection;
        $connections_to_update = array('parent_eid' => $connection_eid);
        $process_arr = array('eid_status' => \Model::STATUS_DELETED, 'name' => '');
        $connection_model->update($connections_to_update, $process_arr);
    }

    private function createAssociatedPipes() : void
    {
        // note: creates associated pipes for a mounted connection
        $connection =  $this->getConnection();
        $connection_info =  $connection->get();
        $connection_mode = $connection_info['connection_mode'];
        $properties = $this->getProperties();

        // if the connection mode isn't a mount; there are no associated pipes
        if ($connection_mode !== \Model::CONNECTION_MODE_FUNCTION)
            return;

        // STEP 1: get a list of existing pipe names for this owner so
        // we can make sure the pipe name created is unique
        $existing_pipe_names = array();
        $filter = array('owned_by' =>  $connection->getOwner(), 'eid_status' => \Model::STATUS_AVAILABLE);
        $existing_pipes = \Flexio\Object\Pipe::list($filter);
        foreach ($existing_pipes as $p)
        {
            $pipe_info = $p->get();
            $existing_pipe_names[$pipe_info['name']] = 1;
        }

        // STEP 2: create pipes from an installation function
        $function_items = $this->getFunctionsFromManifest('installation');
        foreach ($function_items as $item_info)
        {
            // get the file extension
            $extension = strtolower(\Flexio\Base\File::getFileExtension($item_info['path']));

            // get the pipe content and info
            $pipe_info = null;
            switch ($extension)
            {
                // if we have a script, get the info from the front-matter
                case 'yml':
                case 'py':
                case 'js':
                    $content = '';
                    $vfs = new \Flexio\Services\Vfs($connection->getOwner());
                    $vfs->read($item_info['path'], function($data) use (&$content) {
                        $content .= $data;
                    });
                    $pipe_info = \Flexio\Object\Factory::getPipeInfoFromContent($content, $extension);
                    break;
            }

            // if we can't get the pipe info, move on
            if (!isset($pipe_info))
                continue;

            // create a new process engine for running a process
            $mount_properties = array(
                'connection_eid' => $connection->getEid()
            );
            $process_engine = \Flexio\Jobs\Process::create();
            $process_engine->setOwner($owner_user_eid);
            $process_engine->queue('\Flexio\Jobs\ProcessHandler::addMountParams', $mount_properties);
            $process_engine->queue('\Flexio\Jobs\Task::run', $pipe_info['task']);
            $process_engine->run();
        }

        // STEP 3: create the pipes from files indicated by function parameter
        $function_items = $this->getFunctionsFromManifest('functions');
        foreach ($function_items as $item_info)
        {
            // get the file extension
            $extension = strtolower(\Flexio\Base\File::getFileExtension($item_info['path']));

            // get the pipe content and info
            $pipe_info = null;
            switch ($extension)
            {
                // if we have a script, get the info from the front-matter
                case 'yml':
                case 'py':
                case 'js':
                    $content = '';
                    $vfs = new \Flexio\Services\Vfs($this->getConnection()->getOwner());
                    $vfs->read($item_info['path'], function($data) use (&$content) {
                        $content .= $data;
                    });
                    $pipe_info = \Flexio\Object\Factory::getPipeInfoFromContent($content, $extension);
                    break;
            }

            // if we can't get the pipe info, move on
            if (!isset($pipe_info))
                continue;

            // get the pipe name and make sure it's unique
            $pipe_name = $pipe_info['name'] ?? '';
            $pipe_name = self::getUniquePipeName($pipe_name, $existing_pipe_names);

            if (\Flexio\Base\Identifier::isValid($pipe_name) === false)
                continue; // TODO: throw exception

            $existing_pipe_names[$pipe_name] = 1;

            // set basic pipe info
            $pipe_params = $pipe_info;
            $pipe_params['eid_status'] = \Model::STATUS_PENDING; // set initial status to pending; this will be set to available when final pipe content is loaded
            $pipe_params['parent_eid'] = $connection->getEid();
            $pipe_params['name'] = $pipe_name; // override supplied name with name that's unique
            $pipe_params['owned_by'] = $connection_info['owned_by']['eid'];
            $pipe_params['created_by'] = $connection_info['created_by']['eid'];

            // create the new pipe
            $item = \Flexio\Object\Pipe::create($pipe_params);
        }
    }

    private function populatePipeIndexCaches()
    {
        $connection =  $this->getConnection();
        $properties = $this->getProperties();
        $owner_user_eid = $connection->getOwner();

        // get a list of pending pipes for this connection
        $filter = array('owned_by' => $owner_user_eid, 'eid_status' => \Model::STATUS_PENDING, 'parent_eid' => $connection->getEid());
        $pipes = \Flexio\Object\Pipe::list($filter);

        // get the newly created pipes; if we have an index pipe, populate the cache
        // TODO: populating the cache should take place in ConnectionMount::sync(); however,
        // it's here because ConnectionMount is in the object layer, and the process mechanism
        // which currently loads data into the cache is in the api layer becasue of a rights
        // check; need to move things into logical places, but a lot has been in flux with
        // the application and what connections/pipes/process are and how they function

        // populate the initial cache for index pipes
        foreach ($pipes as $p)
        {
            $pipe_properties = $p->get();

            // if we don't have an index pipe, don't populate the cache
            if ($pipe_properties['run_mode'] !== \Model::PIPE_RUN_MODE_INDEX)
            {
                $p->set(array('eid_status' => \Model::STATUS_AVAILABLE));
                continue;
            }

            // create a new process engine for running a process
            $mount_properties = array(
                'connection_eid' => $connection->getEid()
            );
            $elastic_search_params = array(
                'index' => $p->getEid()
            );
            $process_engine = \Flexio\Jobs\Process::create();
            $process_engine->setOwner($owner_user_eid);
            $process_engine->queue('\Flexio\Jobs\ProcessHandler::addMountParams', $mount_properties);
            $process_engine->queue('\Flexio\Jobs\Task::run', $pipe_properties['task']);
            $process_engine->queue('\Flexio\Jobs\ProcessHandler::saveStdoutToElasticSearch', $elastic_search_params);
            $process_engine->run();

            // pipe is ready
            $p->set(array('eid_status' => \Model::STATUS_AVAILABLE));
        }
    }

    private function getFunctionsFromManifest(string $tag) : array
    {
        $connection_info = $this->getConnection()->get();

        // get the manifest; if we have an HTTP connection, the manifest is at
        // a specific URL; if we have any other connection, look for the flexio.yml
        // file in the base directory
        $manifest_path = 'flexio.yml';
        if ($connection_info['connection_type'] === \Model::CONNECTION_TYPE_HTTP && isset($connection_info['connection_info']['url']))
            $manifest_path = $connection_info['connection_info']['url'];

        try
        {
            $content = '';
            $this->getConnection()->getService()->read(['path' => $manifest_path], function($data) use (&$content) {
                $content .= $data;
            });

            $manifest_info = \Flexio\Base\Yaml::parse($content);

            if (!$manifest_info)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            if (!isset($manifest_info[$tag]))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            $functions = $manifest_info[$tag];
            $result = [];
            foreach ($functions as $f)
            {
                if (!isset($f['path']))
                    continue;

                $function_path = '';
                if ($connection_info['connection_type'] === \Model::CONNECTION_TYPE_HTTP)
                {
                    $manifest_path_base = dirname($manifest_path);
                    $function_path = \Flexio\Base\Util::appendUrlPath($manifest_path_base, $f['path']);
                }

                // a manifest can have a relative path or a full http path; if we have
                // a full http path, leave it as is, otherwise set the relative path based
                // on the local connection eid
                if (\Flexio\Base\Util::isValidUrl($function_path) === false)
                {
                    $manifest_path_base = $connection_info['eid'];
                    $function_path = $manifest_path_base . ':/' . $f['path']; // vfs path
                }

                $function_path = $function_path;
                $item = [];
                $item['type'] = 'FILE';
                $item['path'] = $function_path;
                $item['hash'] = '';
                $item['size'] = 0;
                $result[] = $item;
            }

            return $result;
        }
        catch (\Exception $e)
        {
            // fall through
        }

        return array();
    }

    private static function getUniquePipeName(string $pipe_name, array $existing_pipe_names) : string
    {
        // see if the pipe is in the existing list of pipes; if it isn't,
        // simply return the pipe
        if (array_key_exists($pipe_name, $existing_pipe_names) === false)
            return $pipe_name;

        // try to add a count suffix and see if we can find something
        for ($idx = 1; $idx <= 100; ++$idx)
        {
            $adjusted_pipe_name = $pipe_name . '-dup' . $idx;
            if (array_key_exists($adjusted_pipe_name, $existing_pipe_names) === false)
                return $adjusted_pipe_name;
        }

        // if we haven't found anything, return something that will be unique
        // TODO: different approach?
        $pipe_name = $pipe_name . '-' . \Flexio\Base\Util::generateRandomString(10);
    }
}
