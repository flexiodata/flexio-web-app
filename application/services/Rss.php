<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-10-29
 *
 * @package flexio
 * @subpackage Services
 */


declare(strict_types=1);
namespace Flexio\Services;

/*
// include for RSS library
if (!isset($GLOBALS['simplepie_included']))
{
    $GLOBALS['simplepie_included'] = true;
    set_include_path(get_include_path() . PATH_SEPARATOR . (dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'simplepie' . DIRECTORY_SEPARATOR . 'library'));
}
*/

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library'. DIRECTORY_SEPARATOR .'simplepie'. DIRECTORY_SEPARATOR . 'autoloader.php';


//require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'library'. DIRECTORY_SEPARATOR .'simplepie'. DIRECTORY_SEPARATOR . 'idn' . DIRECTORY_SEPARATOR . 'idna_convert.class.php';



class Rss implements \Flexio\Services\IConnection
{
    ////////////////////////////////////////////////////////////
    // member variables
    ////////////////////////////////////////////////////////////



    ////////////////////////////////////////////////////////////
    // IConnection interface
    ////////////////////////////////////////////////////////////

    public static function create(array $params = null) : \Flexio\Services\Rss
    {
        $service = new self;

        if (isset($params))
            $service->connect($params);

        return $service;
    }

    public function connect(array $params) : bool
    {
        return true;
    }

    public function isOk() : bool
    {
        return true;
    }

    public function close()
    {
    }

    public function listObjects(string $path = '') : array
    {
        // TODO: show the RSS links for a given URL?
        return array();
    }

    public function exists(string $path) : bool
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return false;
    }

    public function getInfo(string $path) : array
    {
        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
        return array();
    }

    public function read(array $params, callable $callback)
    {
        $feed = new \SimplePie();
        $feed->enable_cache(false);
        if (isset($params['data']))
            $feed->set_raw_data($params['data']);
        else if (isset($params['path']))
            $feed->set_feed_url($params['path']);
        $feed->init();
        $feed->handle_content_type();
        $items = $feed->get_items();

        foreach ($items as $item)
        {
            $link = $item->get_link();
            $title = $item->get_title();
            $description = html_entity_decode(strip_tags($item->get_description()));
            $content = $item->get_content();
            $source = $item->get_source();
            $author = $item->get_author();
            $date = $item->get_date();

            $row = array(
                'link' => $link,
                'title' => $title,
                'description' => $description,
                'content' => $content,
                'source' => $source,
                'author' => $author,
                'date' => $date
            );

            $callback($row);
        }
    }

    public function write(array $params, callable $callback)
    {
        $path = $params['path'] ?? '';
        $content_type = $params['content_type'] ?? \Flexio\Base\ContentType::MIME_TYPE_STREAM;

        // TODO: implement
        throw new \Flexio\Base\Exception(\Flexio\Base\Error::UNIMPLEMENTED);
    }


    ////////////////////////////////////////////////////////////
    // additional functions
    ////////////////////////////////////////////////////////////

    public function describeTable(string $path) : array
    {
        $structure = array(
            array('name' => 'link', 'type' => 'text'),
            array('name' => 'title', 'type' => 'text'),
            array('name' => 'description', 'type' => 'text'),
            array('name' => 'content', 'type' => 'text'),
            array('name' => 'source', 'type' => 'text'),
            array('name' => 'author', 'type' => 'text'),
            array('name' => 'date', 'type' => 'text')
        );

        return $structure;
    }
}
