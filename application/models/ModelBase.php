<?php
/**
 *
 * Copyright (c) 2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2012-01-06
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class ModelBase
{
    private $model;

    public function setModel($model) : void
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
}
