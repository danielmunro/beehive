<?php

require_once('../autoloader.php');

$beehive = new Beehive\Server('127.0.0.1', 9000);
$beehive->setClientType('\Beehive\Clients\WebSocketHybi10');
$clients = [];
$beehive->setConnectCallback(function($client) use (&$clients) {
	$clients[] = $client;
});
$beehive->setReadCallback(function($sender, $input) use ($beehive, &$clients) {
	$data = json_encode(['client' => $sender->getID(), 'message' => $input."\r\n"]);
	foreach($clients as $client) {
		$client->write($data);
	}
});
$beehive->listen();
