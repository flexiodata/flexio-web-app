<?php
/*!
 *
 * Copyright (c) 2008-2011, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams; David Z. Williams
 * Created:  2008-09-05
 *
 */

// loads the application class and then runs it,
// everything else should happen inside the run() method

require_once '../application/bootstrap.php';
Flexio::getInstance()->run();
