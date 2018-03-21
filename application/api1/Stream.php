<?php
/**
 *
 * Copyright (c) 2014-2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-04-25
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api1;


class Stream
{
    public static function get(\Flexio\Api1\Request $request) : array
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid' => array('type' => 'identifier', 'required' => true)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $stream_identifier = $validated_params['eid'];

        // check the rights on the object
        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // TODO: re-add
        //if ($stream->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        return $stream->get();
    }

    public static function content(\Flexio\Api1\Request $request) // TODO: set function return type
    {
        $params = $request->getQueryParams();
        $requesting_user_eid = $request->getRequestingUser();

        $validator = \Flexio\Base\Validator::create();
        if (($validator->check($params, array(
                'eid'          => array('type' => 'identifier', 'required' => true),
                'start'        => array('type' => 'integer', 'required' => false),
                'limit'        => array('type' => 'integer', 'required' => false),
                'metadata'     => array('type' => 'string', 'required' => false),
                'content_type' => array('type' => 'string', 'required' => false),
                'encode'       => array('type' => 'string', 'required' => false)
            ))->hasErrors()) === true)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $validated_params = $validator->getParams();
        $stream_identifier = $validated_params['eid'];
        $start = isset($validated_params['start']) ? (int)$validated_params['start'] : 0;  // start isn't specified, start at the beginning
        $limit = isset($validated_params['limit']) ? (int)$validated_params['limit'] : PHP_INT_MAX;  // if limit isn't specified, choose a large value (TODO: stream output in chunks?)
        $metadata = isset($validated_params['metadata']) ? \toBoolean($validated_params['metadata']) : false;
        $content_type = isset($validated_params['content_type']) ? $validated_params['content_type'] : false;
        $encode = $validated_params['encode'] ?? null;

        $stream = \Flexio\Object\Stream::load($stream_identifier);
        if ($stream->getStatus() === \Model::STATUS_DELETED)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::NO_OBJECT);

        // TODO: re-add
        //if ($stream->allows($requesting_user_eid, \Flexio\Object\Right::TYPE_READ) === false)
        //    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INSUFFICIENT_RIGHTS);

        $stream_info = $stream->get();
        if ($stream_info === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        $response_content_type = $stream_info['mime_type'];

        // if the caller wants to override the mime type that will be returned, they may
        if ($content_type !== false)
            $response_content_type = $content_type;

        if ($response_content_type !== \Flexio\Base\ContentType::FLEXIO_TABLE)
        {
            // return content as-is
            header('Content-Type: ' . $response_content_type);
            $result = \Flexio\Base\Util::getStreamContents($stream, $start, $limit);
            if (isset($encode))
            {
                // user wants us to re-encode the data payload on a preview-only basis
                $encoding = mb_detect_encoding($result, 'UTF-8,ISO-8859-1');
                if ($encoding != 'UTF-8')
                    $result = iconv($encoding, 'UTF-8', $result);
            }
            echo($result);
            exit(0);
        }
         else
        {
            // flexio table; return application/json in place of internal mime
            header('Content-Type: ' . \Flexio\Base\ContentType::JSON);

            $result = array();
            $result['success'] = true;
            $result['total_count'] = $stream->getRowCount(); // TODO: fill out

            if ($metadata === true)
                $result['columns'] = $stream->getStructure()->get();

            $result['rows'] = \Flexio\Base\Util::getStreamContents($stream, $start, $limit);
            $result = json_encode($result);

            echo($result);
            exit(0);
        }
    }
}
