<?php
namespace Beehive\Clients;

class Telnet implements \Beehive\Client
{
	protected $id = '';
	protected $server = null;
	protected $connection = null;
	protected $buffer = null;
	
	public function __construct(\Beehive\Server $server, $id, $connection, $buffer)
	{
		$this->server = $server;
		$this->id = $id;
		$this->connection = $connection;
		$this->buffer = $buffer;
	}

	public function getID()
	{
		return $this->id;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function getBuffer()
	{
		return $this->buffer;
	}
}
