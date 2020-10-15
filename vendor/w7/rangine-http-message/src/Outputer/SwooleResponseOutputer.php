<?php

/**
 * WeEngine Api System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\Http\Message\Outputer;

use Swoole\Http\Response;
use W7\Http\Message\Base\Cookie;

class SwooleResponseOutputer extends ResponseOutputerAbstract {

	/**
	 * @var Response
	 */
	private $response;

	public function __construct(Response $response) {
		$this->response = $response;
	}

	public function sendChunk($content) {
		return $this->response->write($content);
	}

	public function sendFile($file) {
		return $this->response->sendfile($file->getRealPath(), $file->getOffset(), $file->getLength());
	}

	public function sendBody($content) {
		return $this->response->end($content);
	}

	public function sendHeader($headers) {
		if (empty($headers)) {
			return true;
		}
		foreach ($headers as $key => $value) {
			$this->response->header($key, implode(';', $value));
		}
		return true;
	}

	public function sendStatus($code) {
		$this->response->status($code);
		return true;
	}

	public function sendCookie($cookies) {
		if (empty($cookies)) {
			return true;
		}
		/**
		 * @var Cookie $cookie;
		 */
		foreach ($cookies as $name => $cookie) {
			$this->response->cookie(
				$cookie->getName(),
				$cookie->getValue() ? : 1,
				$cookie->getExpiresTime(),
				$cookie->getPath(),
				$cookie->getDomain(),
				$cookie->isSecure(),
				$cookie->isHttpOnly()
			);
		}

		return true;
	}
}
