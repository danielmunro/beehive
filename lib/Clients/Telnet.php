<?php
namespace Beehive\Clients;

class Telnet implements \Beehive\Client
{
	protected $id = '';
	protected $server = null;
	protected $connection = null;
	protected $buffer = null;
	protected $handshake = false;
	
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

	public function handshake($headers)
	{
		return $this->handshake = true;
	}

	public function getHandshake()
	{
		return $this->handshake;
	}

	public function decodeIncoming($message)
	{
		return $message;
	}

	public function write($message)
	{
		event_buffer_write($this->buffer, $message, strlen($message));
	}
}
