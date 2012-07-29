<?php
namespace Beehive\Clients;

class Telnet implements \Beehive\Client
{
	protected $id = '';
	protected $server = null;
	protected $connection = null;
	protected $buffer = null;
	protected $input_buffer = [];
	protected $last_input = '';
	protected $read_callback = null;
	
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
	
	public function getLastInput()
	{
		return $this->last_input;
	}

	public function pushInputBuffer($message)
	{
		$this->input_buffer[] = $message;
	}
}
