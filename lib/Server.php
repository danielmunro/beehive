<?php
namespace Beehive;

class Server
{
	protected $host = '';
	protected $port = 0;
	protected $connection = null;
	protected $clients = [];

	const CONNECTION_TIMEOUT = 1200;
	const EVENT_PRIORITY = 1;
	const MAX_READ_LENGTH = 256;
	
	public function __construct($host, $port)
	{
		$this->host = $host;
		$this->port = $port;

		$this->connection = stream_socket_server('tcp://'.$this->host.':'.$this->port, $errno, $errstr);
		stream_set_blocking($this->connection, 0);
		$base = event_base_new();
		$event = event_new();
		event_set($event, $this->connection, EV_READ | EV_PERSIST, [$this, 'addClient'], $base);
		event_base_set($event, $base);
		event_add($event);
		event_base_loop($base);
	}
	
	protected function addClient($socket, $flag, $base)
	{
		$id = md5(time().rand().$flag);
		
		$connection = stream_socket_accept($socket);
		stream_set_blocking($connection, 0);

		$buffer = event_buffer_new($connection, [$this, 'read'], NULL, [$this, 'error'], $id);
		event_buffer_base_set($buffer, $base);
		event_buffer_timeout_set($buffer, self::CONNECTION_TIMEOUT, self::CONNECTION_TIMEOUT);
		event_buffer_watermark_set($buffer, EV_READ, 0, 0xffffff);
		event_buffer_priority_set($buffer, self::EVENT_PRIORITY);
		event_buffer_enable($buffer, EV_READ | EV_PERSIST);
		$this->clients[$id] = new Client($id, $connection, $buffer);
	}

	protected function read($buffer, $id)
	{
		$message = trim(event_buffer_read($buffer, self::MAX_READ_LENGTH));
		$this->clients[$id]->pushInputBuffer($message);
	}

	protected function error($buffer, $error, $id)
	{
		$this->removeClient($id);
	}
	
	protected function removeClient($id)
	{
		$buf = $this->clients[$id]->getBuffer();
		$conn = $this->clients[$id]->getConnection();

		event_buffer_disable($buf, EV_READ | EV_WRITE);
		event_buffer_free($buf);
		fclose($conn);
		unset($this->clients[$id]);
	}
	
	public function __destruct()
	{
		if(is_resource($this->connection)) {
			socket_close($this->connection);
		}
	}

	public function __toString()
	{
		return $this->host.':'.$this->port;
	}
}
