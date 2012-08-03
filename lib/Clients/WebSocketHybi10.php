<?php
namespace Beehive\Clients;

class WebSocketHybi10 implements \Beehive\Client, \jsonSerializable
{
	protected $id = '';
	protected $server = null;
	protected $connection = null;
	protected $buffer = null;
	protected $handshake = false;

	public function jsonSerialize()
	{
		return ['id' => $this->id];
	}

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

	public function decodeIncoming($message)
	{
		$wrote = self::_hybi10DecodeData($message);
		return $wrote;
	}

	public function write($message)
	{
		$data = self::_hybi10EncodeData($message);
		event_buffer_write($this->buffer, $data, strlen($data));
	}

	public function getHandshake()
	{
		return $this->handshake;
	}

	public function handshake($headers)
	{
		$fnSockKey = function($headers) {
			$lines = explode("\r\n", $headers);
			foreach($lines as $line) {
				if(strpos($line, 'Sec-WebSocket-Key') === 0) {
					$ex = explode(": ", $line);
					return $ex[1];
				}
			}
		};
		$this->handshake = "HTTP/1.1 101 Switching Protocols\r\n" .
		"Upgrade: websocket\r\n" .
		"Connection: Upgrade\r\n" .
		"WebSocket-Origin: http://localhost\r\n" .
		"WebSocket-Location: ws://localhost:".$this->server->getPort()."\r\n" .
		"Sec-WebSocket-Accept: ".base64_encode(pack('H*', sha1($fnSockKey($headers)."258EAFA5-E914-47DA-95CA-C5AB0DC85B11")))."\r\n\r\n";
		event_buffer_write($this->buffer, $this->handshake, strlen($this->handshake));
		return true;
	}

	protected function _hybi10EncodeData($data)
	{
		$length = strlen($data);
		$buffer = "\x00\x00";
		$buffer[0] = chr((0x0f & 1) | (0x80 & PHP_INT_MAX));
		$masked_bit = (0x80 & 0);

		if ($length <= 125) {
			$buffer[1] = chr((0x7f & $length) | $masked_bit);
		} elseif ($length <= 65536) {
			$buffer[1] = chr((0x7f & 126) | $masked_bit);
			$buffer .= pack('n', $length);
		} else {
			$buffer[1] = chr((0x7f & 127) | $masked_bit);
			if (PHP_INT_MAX > 2147483647) {
				$buffer .= pack('NN', $length >> 32, $length);
			} else {
				$buffer .= pack('NN', 0, $length);
			}
		}
		$buffer .= $data;
		return $buffer;
	}

	protected function _hybi10DecodeData($data)
	{		
		$bytes = $data;
		$dataLength = '';
		$mask = '';
		$coded_data = '';
		$decodedData = '';
		$secondByte = sprintf('%08b', ord($bytes[1]));		
		$dataLength = ord($bytes[1]) & 127;
		if($dataLength === 126) {
			$mask = substr($bytes, 4, 4);
			$coded_data = substr($bytes, 8);
		} elseif($dataLength === 127) {
			$mask = substr($bytes, 10, 4);
			$coded_data = substr($bytes, 14);
		} else {
			$mask = substr($bytes, 2, 4);		
			$coded_data = substr($bytes, 6);		
		}	
		for($i = 0; $i < strlen($coded_data); $i++) {		
			$decodedData .= $coded_data[$i] ^ $mask[$i % 4];
		}

		return $decodedData;
	}
}
