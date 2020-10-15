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

namespace W7\Http\Message\Stream;

/**
 * 数据格式
 * {
 *      method|cmd[GET|POST|DELETE...],
 *      uri,
 *      data,
 *      header : {
 *          Content-Type : text/html
 *      }
 * }
 * Class FrameStream
 * @package W7\WebSocket\Message
 */
class FrameStream {
	const OPCODE_TEXT = WEBSOCKET_OPCODE_TEXT;
	const OPCODE_BINARY = WEBSOCKET_OPCODE_BINARY;
	const OPCODE_PING = WEBSOCKET_OPCODE_PING;

	private $body = [];
	private $method = 'POST';
	private $uri = '';
	private $raw = '';

	public function __construct($data, $opcode = self::OPCODE_TEXT) {
		if ($opcode == self::OPCODE_BINARY) {
			$this->unpackBinary($data);
		} elseif ($opcode == self::OPCODE_TEXT) {
			$this->unpackText($data);
		} else {
			$this->raw = $data;
		}
	}

	protected function unpackBinary($data) {
		// @todo 处理解开二进制数据
		return $data;
	}

	protected function unpackText($data) {
		$dataJson = \json_decode($data, true);
		if (\json_last_error() != JSON_ERROR_NONE) {
			$this->raw = $data;
		} else {
			$this->raw = $data;
			if (!empty($dataJson['data']) && is_array($dataJson['data'])) {
				$this->body = $dataJson['data'];
			}
			$this->method = strtoupper($dataJson['method'] ?? $this->method);

			if (!empty($dataJson['uri'])) {
				$this->uri = '/' . trim($dataJson['uri'], '/');
			} elseif (!empty($dataJson['cmd'])) {
				$this->uri = '/' . trim($dataJson['cmd'], '/');
			} else {
				$this->uri = '';
			}
		}
		return true;
	}

	public function getBody() : array {
		return $this->body;
	}

	public function getMethod(): string {
		return $this->method;
	}

	public function getUri(): string {
		return $this->uri;
	}

	public function getRaw(): string {
		return $this->raw;
	}
}
