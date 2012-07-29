<?php

require_once('autoloader.php');

$beehive = new Beehive\Server('127.0.0.1', 9000);
$beehive->setClientType('\Beehive\Clients\Telnet');
$beehive->listen();
