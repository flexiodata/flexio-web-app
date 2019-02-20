<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-02-22
 *
 * @package flexio
 * @subpackage Controller
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Result
{
    public $name;
    public $description;
    public $passed;
    public $message;

    public function __construct($name = '', $description = '', $passed = false, $message = '')
    {
        $this->name = $name;
        $this->description = $description;
        $this->passed = $passed;
        $this->message = $message;
    }
}

