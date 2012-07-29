<?php
namespace Beehive;

class Client
{
	protected $id = '';
	protected $connection = null;
	protected $buffer = null;
	protected $input_buffer = [];
	protected $last_input = '';
	
	public function __construct($id, $connection, $buffer)
	{
		$this->id = $id;
		$this->connection = $connection;
		$this->buffer = $buffer;
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
