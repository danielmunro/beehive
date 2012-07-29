<?php
namespace Beehive;

interface Client
{
	public function __construct(Server $server, $id, $connection);

	public function getID();

	public function getConnection();

	public function setBuffer($buffer);

	public function getBuffer();

	public function wrote($message);
}
