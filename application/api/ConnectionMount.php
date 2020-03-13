<?php
/**
 *
 * Copyright (c) 2020, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2020-02-27
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class ConnectionMount
{
    private $connection;

    public static function create(\Flexio\Object\Connection $connection) : \Flexio\Api\ConnectionMount
    {
        $object = new static();
        $object->connection = $connection;
        return $object;
    }

    public function getConnection() : \Flexio\Object\Connection
    {
        return $this->connection;
    }

    public function sync() : void
    {
        // note: syncs pipes in a mounted a connection with the source files in the connection

        $connection_info = $this->getConnection()->get();
        $connection_eid = $connection_info['eid'];
        $connection_mode = $connection_info['connection_mode'];

        // if the connection mode isn't a mount; there are no associated pipes
        if ($connection_mode !== \Model::CONNECTION_MODE_FUNCTION)
            return;

        // delete the pipes that are no longer in the connection
        // don't delete associated connections since we're only re-adding
        // the pipes, but we want to keep the configuration information
        // and associated connections
        $this->deleteAssociatedPipes();
        // $this->deleteAssociatedConnections();

        // create pipes for new items in the connection
        $this->createAssociatedPipes();
    }

    public function delete() : void
    {
        $this->deleteAssociatedPipes();
        $this->deleteAssociatedConnections();
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

        $pipe_model = \Flexio\System\System::getModel()->pipe;
        $pipes_to_update = array('parent_eid' => $connection_eid);
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

    private function getConnectionItemsToImport() : array
    {
        $connection_info = $this->getConnection()->get();

        // if we have a regular file type service, just get the list of files
        // from the service; TODO: use file system interface as test
        if ($connection_info['connection_type'] !== \Model::CONNECTION_TYPE_HTTP)
        {
            $service =  $this->getConnection()->getService();
            return $service->list();
        }

        // in the case of HTTP, the list of files to return is specific to a particular
        // manifest at a URL (which is why a general service list implementation isn't
        // used); get the manifest and build up the list of files from the manifest

        $manifest_url = '';
        if (isset($connection_info['connection_info']) && isset($connection_info['connection_info']['url']))
            $manifest_url = $connection_info['connection_info']['url'];
        $manifest_url_base = dirname($manifest_url);

        try
        {
            $content = '';
            $http_service = \Flexio\Services\Http::create();
            $http_service->read(['path' => $manifest_url], function($data) use (&$content) {
                $content .= $data;
            });

            $manifest_info = \Flexio\Base\Yaml::parse($content);

            if (!$manifest_info)
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);
            if (!isset($manifest_info['functions']))
                throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

            $functions = $manifest_info['functions'];
            $result = [];
            foreach ($functions as $f)
            {
                if (!isset($f['path']))
                    continue;

                $function_path = \Flexio\Base\Util::appendUrlPath($manifest_url_base, $f['path']);
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

    private function createAssociatedPipes() : void
    {
        // note: creates associated pipes for a mounted connection

        $connection_info =  $this->getConnection()->get();
        $connection_eid = $connection_info['eid'];
        $connection_mode = $connection_info['connection_mode'];

        // if the connection mode isn't a mount; there are no associated pipes
        if ($connection_mode !== \Model::CONNECTION_MODE_FUNCTION)
            return;

        // STEP 1: get the list of files to use to create pipes
        $connection_items = $this->getConnectionItemsToImport();

        $connection_item_info = array();
        foreach ($connection_items as $item)
        {
            $item_info = $item;

            // TODO: add constant for "FILE"
            if ($item_info['type'] !== "FILE")
                continue;

            // filter out items that aren't flexio tasks, or javascript/python scripts
            // TODO: adjust filter criteria?
            $name = $item_info['path'];
            $ext = strtolower(\Flexio\Base\File::getFileExtension($name));
            if ($ext !== 'flexio' && $ext !== 'py' && $ext !== 'js')
                continue;

            $connection_item_info[] = $item_info;
        }

        // STEP 2: get a list of existing pipe names for this owner so
        // we can make sure the pipe name created is unique
        $existing_pipe_names = array();
        $filter = array('owned_by' =>  $this->getConnection()->getOwner(), 'eid_status' => \Model::STATUS_AVAILABLE);
        $existing_pipes = \Flexio\Object\Pipe::list($filter);
        foreach ($existing_pipes as $p)
        {
            $pipe_info = $p->get();
            $existing_pipe_names[$pipe_info['name']] = 1;
        }

        // STEP 3: create the pipes
        foreach ($connection_item_info as $item_info)
        {
            // get the file extension and set the language
            $language = false;
            $ext = strtolower(\Flexio\Base\File::getFileExtension($item_info['path']));
            if ($ext === 'flexio')
                $language = 'flexio'; // execute content as a JSON pipe
            if ($ext === 'py')
                $language = 'python';
            if ($ext === 'js')
                $language = 'nodejs';

            if ($language === false)
                continue;

            $content = self::getContentFromCacheOrPath($connection_info, $item_info);

            // get the pipe info from the content; if we can't find any, don't import the pipe
            $pipe_info_from_content = self::getPipeInfoFromContent($content);
            if (!isset($pipe_info_from_content))
                continue;

            // see if the pipe name is in the list of existing pipes; if so, postfix it
            // with a unique identifier
            // get the pipe name and make sure it's unique
            $pipe_name = $pipe_info_from_content['name'] ?? '';
            $pipe_name = self::getUniquePipeName($pipe_name, $existing_pipe_names);

            if (\Flexio\Base\Identifier::isValid($pipe_name) === false)
                continue; // TODO: throw exception

            $existing_pipe_names[$pipe_name] = 1;
            $pipe_deployed = $pipe_info_from_content['deployed'] ?? false; // don't deploy by default

            // set basic pipe info
            $pipe_params = array();
            $pipe_params['parent_eid'] = $connection_eid;
            $pipe_params['name'] = $pipe_name;
            $pipe_params['title'] = $pipe_info_from_content['title'] ?? '';
            $pipe_params['icon'] = $pipe_info_from_content['icon'] ?? '';
            $pipe_params['description'] = $pipe_info_from_content['description'] ?? '';
            $pipe_params['examples'] = $pipe_info_from_content['examples'] ?? [];
            $pipe_params['params'] = $pipe_info_from_content['params'] ?? [];
            $pipe_params['returns'] = $pipe_info_from_content['returns'] ?? [];
            $pipe_params['notes'] = $pipe_info_from_content['notes'] ?? '';
            $pipe_params['deploy_mode'] = $pipe_deployed ? \Model::PIPE_DEPLOY_MODE_RUN : \Model::PIPE_DEPLOY_MODE_BUILD;
            $pipe_params['deploy_schedule'] = \Model::PIPE_DEPLOY_STATUS_INACTIVE;
            $pipe_params['owned_by'] = $connection_info['owned_by']['eid'];
            $pipe_params['created_by'] = $connection_info['created_by']['eid'];

            // set the task info
            if ($language === 'flexio')
            {
                $task = array();
                $pipe = @json_decode($content,true);
                if (!is_null($pipe))
                    $task = $pipe['task'] ?? array();
                $pipe_params['task'] = $task;
            }
            else
            {
                $execute_job_params = array();
                $execute_job_params['op'] = 'execute'; // set the execute operation so this doesn't need to be supplied
                $execute_job_params['lang'] = $language; // TODO: set the language from the extension
                $execute_job_params['code'] = base64_encode($content); // encode the script

                $pipe_params['task'] = [
                    "op" => "sequence",
                    "items" => [
                        $execute_job_params
                    ]
                ];
            }

            // create the new pipe
            $item = \Flexio\Object\Pipe::create($pipe_params);
        }
    }

    private static function getContentFromCacheOrPath(array $connection_info, array $item_info) : ?string
    {
        $connection_eid = $connection_info['eid'];

        // if we have an http connection type, load the content each time; TODO: use
        // etags to get a hash signature, and then we can cache content
        if ($connection_info['connection_type'] === \Model::CONNECTION_TYPE_HTTP)
        {
            $content = '';
            $http_service = \Flexio\Services\Http::create();
            $http_service->read(['path' => $item_info['path']], function($data) use (&$content) {
                $content .= $data;
            });

            return $content;
        }

        // generate a handle for the content signature that will uniquely identify it;
        // use the owner plus a hash of some identifiers that constitute unique content
        $content_handle = '';
        $content_handle .= $connection_info['owned_by']['eid']; // include owner in the identifier so that even if the connection owner changes (later?), the cache will only exist for this owner
        $content_handle .= $item_info['hash']; // not always populated, so also add on info from the file
        $content_handle .= md5(
            $item_info['path'] .
            strval($item_info['size']) .
            $item_info['modified']
        );

        // get the cached content; if it doesn't exist, create the cache
        $stream = \Flexio\Object\Factory::getStreamContentCache($connection_eid, $content_handle);
        if (!isset($stream))
        {
            $remote_path = $item_info['path'];
            $stream = \Flexio\Object\Factory::createStreamContentCache($connection_eid, $remote_path, $content_handle);
        }

        if (!isset($stream))
            return null;

        $content = '';
        $reader = $stream->getReader();
        while (($data = $reader->read()) != false)
        {
            $content .= $data;
        }

        return $content;
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

    private static function getPipeInfoFromContent(string $content) : ?array
    {
        try
        {
            $yaml = \Flexio\Base\Yaml::extract($content);
            return \Flexio\Base\Yaml::parse($yaml);
        }
        catch (\Exception $exception)
        {
            // DEBUG:
            // echo('Unable to parse the YAML string: %s', $exception->getMessage());
        }

        return null;
    }
}

