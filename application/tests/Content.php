<?php
/**
 *
 * Copyright (c) 2018, Flex Research LLC. All rights reserved.
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
    public static function getRows(\Flexio\IFace\IStream $stream) : array
    {
        $content = \Flexio\Base\StreamUtil::getStreamContents($stream);
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
}
