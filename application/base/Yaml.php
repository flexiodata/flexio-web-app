<?php
/**
 *
 * Copyright (c) 2019, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2019-09-04
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Yaml
{
    public static function parse(string $yaml) : ?array
    {
        // TODO: handling commented-out yaml

        $result = \Symfony\Component\Yaml\Yaml::parse($yaml);
        if ($result === false)
            return null;

        return $result;
    }

    public static function extract(string $content) : string
    {
        // extracts out yaml from front matter

        $frontmatter_delimiter = "---";
        $frontmatter_boundary_count = 0;
        $frontmatter = array();

        $lines = preg_split('/\r\n|\r|\n/', $content);
        foreach ($lines as $l)
        {
            // see if we're on a front matter boundary
            $frontmatter_boundary_count += strpos($l, $frontmatter_delimiter) !== false ? 1 : 0;

            if ($frontmatter_boundary_count < 1)
                continue;

            if ($frontmatter_boundary_count >= 2)
                break;

            // if we're in the front matter, save the line without the comment
            $frontmatter[] = preg_replace('/^\s*([#*]|\/\/) /', '', $l);
        }

        if (count($frontmatter) === 0)
            return '';

        // remove the initial frontmatter boundary marker
        array_shift($frontmatter);

        // reassemble the lines as a string
        $result = '';
        foreach ($frontmatter as $l)
            $result .= $l . '';

        return $result;
    }
}
