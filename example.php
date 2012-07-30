<?php

require_once('autoloader.php');

$beehive = new Beehive\Server('127.0.0.1', 9000);
$beehive->setClientType('\Beehive\Clients\Telnet');
$beehive->setReadCallback(function($client, $input) use ($beehive) {
	// tell the server to echo any input back as output
	$client->write($input."\r\n");
});
$beehive->listen();
