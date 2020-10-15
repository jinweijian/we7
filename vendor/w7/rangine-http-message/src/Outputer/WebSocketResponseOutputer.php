<?php

/**
 * Rangine Http Message
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\Http\Message\Outputer;

use Swoole\WebSocket\Server;

class WebSocketResponseOutputer extends ResponseOutputerAbstract {
	/**
	 * @var Server
	 */
	private $response;
	private $fd;

	public function __construct(Server $server, $fd) {
		$this->response = $server;
		$this->fd = $fd;
	}

	public function sendBody($content) {
		if ($this->response->isEstablished($this->getFd())) {
			return $this->response->push($this->getFd(), $content);
		} else {
			throw new \RuntimeException('Client(fd: ' . $this->getFd() . ') has lost connection');
		}
	}

	public function sendHeader($headers) {
	}

	public function sendCookie($cookies) {
	}

	public function sendStatus($code) {
	}

	public function sendFile($file) {
	}

	public function sendChunk($content) {
		return $this->sendBody($content);
	}

	public function disConnect() {
		return $this->response->disconnect($this->getFd());
	}

	private function getFd() {
		if (!empty($this->sendFd)) {
			return $this->sendFd;
		} else {
			return $this->fd;
		}
	}
}
