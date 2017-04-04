<?php


// TODO: suppress php errors so they are not echoed back to received emails

require_once dirname(__DIR__) . '/stub.php';

$f = fopen('php://stdin', 'r');
\Flexio\Object\Manager::handleEmail($f);

