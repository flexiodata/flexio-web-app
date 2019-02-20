<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-21
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Content
{
    public static function getTable(\Flexio\IFace\IStream $stream) : array
    {
        $content = \Flexio\Base\Util::getStreamContents($stream);
        $result = array();
        $result['columns'] = $stream->getStructure()->get();
        $result['content'] = is_array($content) ? $content : array();
        return $result;
    }

    public static function getRows(\Flexio\IFace\IStream $stream) : array
    {
        $content = \Flexio\Base\Util::getStreamContents($stream);
        $result = array();
        if (is_array($content))
        {
            foreach ($content as $row)
            {
                $result[] = array_values($row);
            }
        }

        return $result;
    }

    public static function getValues(\Flexio\IFace\IStream $stream, string $key) : array
    {
        $content = \Flexio\Base\Util::getStreamContents($stream);
        if (is_string($content))
            $content = json_decode($content,true);

        $result = array_column($content, $key);
        sort($result);
        return $result;
    }
}
