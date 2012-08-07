<?php
namespace Beehive;

class Server
{
	protected $host = '';
	protected $port = 0;
	protected $client_type = '';
	protected $connection = null;
	protected $connect_callback = null;

	const CONNECTION_TIMEOUT = 1200;
	const EVENT_PRIORITY = 1;
	const MAX_READ_LENGTH = 256;
	
	public function __construct($host, $port)
	{
		$this->host = $host;
		$this->port = $port;
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

	public function setConnectCallback($callback)
	{
		$this->connect_callback = $callback;
	}

	public function setupListener()
	{
		if(!$this->client_type) {
			throw new \Exception('Client type was not defined, please call setClientType() with the type of client (ie \Beehive\Clients\Telnet) before calling setupListener()');
		}

		$this->connection = stream_socket_server('tcp://'.$this->host.':'.$this->port, $errno, $errstr);
		stream_set_blocking($this->connection, 0);
	}

	public function listen()
	{
		$read = [$this->connection];
		$n = null;
		$new_connection = stream_select($read, $n, $n, 0, 0);
		if($new_connection) {
			foreach($read as $r) {
				$this->addClient($r);
			}
		}
	}
	
	protected function addClient($socket)
	{
		$connection = stream_socket_accept($socket);
		stream_set_blocking($connection, 0);

		$id = md5(time().rand());
		$client = new $this->client_type($this, $id, $connection);
		if($this->connect_callback) {
			call_user_func_array($this->connect_callback, [$client]);
		}
	}
	
	public function __destruct()
	{
		if(is_resource($this->connection)) {
			fclose($this->connection);
		}
	}

	public function __toString()
	{
		return '[beehive:'.$this->host.':'.$this->port.']';
	}
}
