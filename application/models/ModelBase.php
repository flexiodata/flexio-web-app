<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2012-01-06
 *
 * @package flexio
 * @subpackage Model
 */


class ModelBase
{
    private $model;

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getDatabase()
    {
        return $this->model->getDatabase();
    }

    public function fail($code, $message = null, $exception_message = null)
    {
        return $this->model->fail($code, $message, $exception_message);
    }
}
