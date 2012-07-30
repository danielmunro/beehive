<?php
namespace Beehive\Clients;

class Telnet implements \Beehive\Client
{
	protected $id = '';
	protected $server = null;
	protected $connection = null;
	protected $buffer = null;
	
	public function __construct(\Beehive\Server $server, $id, $connection)
	{
		$this->server = $server;
		$this->id = $id;
		$this->connection = $connection;
	}

	public function getID()
	{
		return $this->id;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function setBuffer($buffer)
	{
		$this->buffer = $buffer;
	}

	public function getBuffer()
	{
		return $this->buffer;
	}

	public function wrote($message)
	{
		return $message;
	}

	public function write($message)
	{
		event_buffer_write($this->buffer, $message, strlen($message));
	}
}
