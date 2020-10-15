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

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class FpmResponseOutputer extends ResponseOutputerAbstract {
	private $header = [];
	private static $response;

	/**
	 * @return SymfonyResponse
	 */
	private static function getResponse() {
		if (empty(self::$response)) {
			self::$response = new SymfonyResponse();
		}
		return self::$response;
	}

	public function sendBody($content) {
		self::getResponse()->setContent($content)->send();
	}

	public function sendHeader($headers) {
		if (empty($headers)) {
			return true;
		}

		foreach ($headers as $key => $value) {
			self::getResponse()->headers->set($key, implode(';', $value));
		}

		return true;
	}

	public function sendCookie($cookies) {
		if (empty($cookies)) {
			return true;
		}

		foreach ($cookies as $key => $value) {
			self::getResponse()->headers->setCookie($value);
		}

		return true;
	}

	public function sendStatus($code) {
		self::getResponse()->setStatusCode($code);
	}

	public function sendFile($file) {
		self::getResponse()->sendHeaders();
		return BinaryFileResponse::create($file)->send();
	}

	public function sendChunk($content) {
		if (!headers_sent()) {
			header('Cache-Control: no-cache');
			header('X-Accel-Buffering: no');
		}
		echo $content;
		ob_flush();
		flush();
	}

	public function disConnect() {
		exit;
	}
}
