<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-12-19
 *
 * @package flexio
 * @subpackage Api
 */


declare(strict_types=1);
namespace Flexio\Api;


class Vfs
{
    public static function list(array $params, string $requesting_user_eid = null) : array
    {
        $validator = \Flexio\Base\Validator::create();
        if (($params = $validator->check($params, array(
                'q' => array('type' => 'string', 'required' => false)
            ))->getParams()) === false)
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);

        $vfs = new \Flexio\Services\Vfs();

        $path = $params['q'] ?? '';
        $result = $vfs->listObjects($path);
        
        if (!is_array($result))
            throw new \Flexio\Base\Exception(\Flexio\Base\Error::READ_FAILED);

        return $result;
    }
}
