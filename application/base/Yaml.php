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
}
