<?php
namespace Beehive;

interface Client
{
	public function __construct(Server $server, $id, $connection, $buffer);

	public function getID();

	public function getConnection();

	public function getBuffer();
}
