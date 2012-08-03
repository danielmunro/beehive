<?php

require_once(__DIR__.'/../autoloader.php');

$beehive = new Beehive\Server('127.0.0.1', 9000);
$beehive->setClientType('\Beehive\Clients\Telnet');
$clients = [];
$beehive->setConnectCallback(function($client) use (&$clients) {
	$clients[] = $client;
});
$beehive->setReadCallback(function($sender, $input) use (&$clients) {
	// tell the server to echo any input back as output
	foreach($clients as $client) {
		$client->write('[client '.$sender->getID()."] ".$input."\r\n");
	}
});
$beehive->setDisconnectCallback(function($client) use (&$clients) {
	$i = array_search($client, $clients);
	unset($clients[$i]);
});
$beehive->listen();
