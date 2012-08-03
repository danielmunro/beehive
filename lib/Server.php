<?php
namespace Beehive;

class Server
{
	protected $host = '';
	protected $port = 0;
	protected $client_type = '';
	protected $connection = null;
	protected $read_callback = null;
	protected $connect_callback = null;
	protected $disconnect_callback = null;
	protected $event_base = null;

	const CONNECTION_TIMEOUT = 1200;
	const EVENT_PRIORITY = 1;
	const MAX_READ_LENGTH = 256;
	
	public function __construct($host, $port)
	{
		$this->host = $host;
		$this->port = $port;

		$this->event_base = event_base_new();
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getPort()
	{
		return $this->port;
	}

	public function setClientType($client_type)
	{
		$this->client_type = $client_type;
	}

	public function setConnectCallback(callable $callback)
	{
		$this->connect_callback = $callback;
	}

	public function setDisconnectCallback(callable $callback)
	{
		$this->disconnect_callback = $callback;
	}

	public function setReadCallback(callable $callback)
	{
		$this->read_callback = $callback;
	}

	public function listen()
	{
		if(!$this->client_type) {
			throw new \Exception('Client type was not defined, please call setClientType() with the type of client (ie \Beehive\Clients\Telnet) before listen()');
		}
		if(!is_callable($this->read_callback)) {
			throw new \Exception('Read callback is not defined with a valid callback, please call setReadCallback() with a valid callback');
		}

		$this->connection = stream_socket_server('tcp://'.$this->host.':'.$this->port, $errno, $errstr);
		stream_set_blocking($this->connection, 0);
		$event = event_new();
		event_set($event, $this->connection, EV_READ | EV_PERSIST, [$this, 'addClient'], $this->event_base);
		event_base_set($event, $this->event_base);
		event_add($event);
		event_base_loop($this->event_base);
	}
	
	protected function addClient($socket, $flag, $base)
	{
		$connection = stream_socket_accept($socket);
		stream_set_blocking($connection, 0);

		$id = md5(time().rand().$flag);
		$client = new $this->client_type($this, $id, $connection);

		$buffer = event_buffer_new($connection, [$this, 'read'], NULL, [$this, 'error'], $client);
		event_buffer_base_set($buffer, $base);
		event_buffer_timeout_set($buffer, self::CONNECTION_TIMEOUT, self::CONNECTION_TIMEOUT);
		event_buffer_watermark_set($buffer, EV_READ, 0, 0xffffff);
		event_buffer_priority_set($buffer, self::EVENT_PRIORITY);
		event_buffer_enable($buffer, EV_READ | EV_PERSIST);
		$client->setBuffer($buffer);
	}

	protected function read($buffer, Client $client)
	{
		$message = trim(event_buffer_read($buffer, self::MAX_READ_LENGTH));
		if(!$client->getHandshake()) {
			if($client->handshake($message) && $this->connect_callback) {
				call_user_func_array($this->connect_callback, [$client]);
			}
		}
		call_user_func_array($this->read_callback, [$client, $client->decodeIncoming($message)]);
	}

	protected function error($buffer, $error, Client $client)
	{
		$this->removeClient($client);
	}
	
	public function removeClient(Client $client)
	{
		if($this->disconnect_callback) {
			call_user_func_array($this->disconnect_callback, [$client]);
		}
		$buf = $client->getBuffer();
		$conn = $client->getConnection();

		event_buffer_disable($buf, EV_READ | EV_WRITE);
		event_buffer_free($buf);
		fclose($conn);
	}
	
	public function __destruct()
	{
		if(is_resource($this->connection)) {
			socket_close($this->connection);
		}
	}

	public function __toString()
	{
		return '[beehive:'.$this->host.':'.$this->port.']';
	}
}
