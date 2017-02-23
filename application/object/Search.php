<?php
/**
 *
 * Copyright (c) 2017, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2017-02-23
 *
 * @package flexio
 * @subpackage Object
 */


namespace Flexio\Object;


class Search
{
    public static function exec($query)
    {
        Store::getModel()->search($search_path);
    }
}
