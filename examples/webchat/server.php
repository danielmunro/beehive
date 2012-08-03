<?php

// Include the autoloader for beehive -- probably psr-0 compliant
require_once(__DIR__.'/../../autoloader.php');

/**
 * Run the server with the desired host and port, and set the type of client
 * beehive should expect.
 */

$beehive = new Beehive\Server('127.0.0.1', 9000);
$beehive->setClientType('\Beehive\Clients\WebSocketHybi10');

$clients = [];



/**
 * Define functions to execute at client connect, disconnect, and when reading
 * from them.
 */

$beehive->setConnectCallback(function(Beehive\Client $client) use (&$clients) {
	// Remember the clients when they connect. Since $clients is passed by
	// reference, any changes will affect the $clients variable outside the
	// scope of this function.
	$clients[] = $client;
	clientAdded($client);
	clientList($client);
});

$beehive->setReadCallback(function(Beehive\Client $sender, $input) use (&$clients) {
	// For any valid json request sent to the server, relay a message to all
	// connected clients with the sender and the message.
	$incoming = json_decode($input);
	if($incoming) {
		$message = $incoming->message;
		client($sender, $message);
	}
});

$beehive->setDisconnectCallback(function(Beehive\Client $client) use (&$clients) {
	// Remove disconnected clients from the $clients array.
	$i = array_search($client, $clients);
	unset($clients[$i]);
	clientRemoved($client);
});



/**
 * Tell beehive to start listening.
 */
$beehive->listen();

function clientList(Beehive\Client $client) {
	global $clients;
	$client->write(json_encode(['action' => 'clientList', 'clients' => $clients]));
}

function clientAdded(Beehive\Client $client) {
	broadcast(json_encode(['action' => 'clientAdded', 'client' => $client]), [$client]);
}

function clientRemoved(Beehive\Client $client) {
	broadcast(json_encode(['action' => 'clientRemoved', 'client' => $client]));
}

function client(Beehive\Client $sender, $message) {
	broadcast(json_encode(['action' => 'client', 'client' => $sender->getID(), 'message' => $message."\r\n"]));
}

function broadcast($message, $clientsToIgnore = []) {
	global $clients;
	foreach($clients as $client) {
		if(!in_array($client, $clientsToIgnore)) {
			$client->write($message);
		}
	}
}
