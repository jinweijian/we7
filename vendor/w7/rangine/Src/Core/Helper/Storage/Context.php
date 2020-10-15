<?php

/**
 * This file is part of Rangine
 *
 * (c) We7Team 2019 <https://www.rangine.com/>
 *
 * document http://s.w7.cc/index.php?c=wiki&do=view&id=317&list=2284
 *
 * visited https://www.rangine.com/ for more details
 */

namespace W7\Core\Helper\Storage;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Swoole\Coroutine;
use W7\Http\Message\Server\Request;
use W7\Http\Message\Server\Response;

class Context {
	/**
	 * Key of request context share data
	 */
	const DATA_KEY = 'data';

	/**
	 * Key of current Request
	 */
	const REQUEST_KEY = 'request';

	/**
	 * Key of current Response
	 */
	const RESPONSE_KEY = 'response';

	/**
	 * @var array Coroutine context
	 */
	private static $context;

	/**
	 * 中间件
	 */
	const MIDDLEWARE_KEY = 'lastMiddleware';

	private $recoverCallback;

	/**
	 * @var 最后请求的协程号
	 */
	private $lastCoId;

	/**
	 * @return Request|null
	 */
	public function getRequest() {
		return self::getCoroutineContext(self::REQUEST_KEY);
	}

	/**
	 * @return Response|null
	 */
	public function getResponse() {
		return self::getCoroutineContext(self::RESPONSE_KEY);
	}

	/**
	 * @return array|null
	 */
	public function getContextData() {
		return self::getCoroutineContext(self::DATA_KEY);
	}

	/**
	 * Set the object of request
	 *
	 * @param RequestInterface $request
	 */
	public function setRequest(RequestInterface $request) {
		$coroutineId = self::getCoroutineId();
		self::$context[$coroutineId][self::REQUEST_KEY] = $request;
	}

	/**
	 * Set the object of response
	 *
	 * @param ResponseInterface $response
	 */
	public function setResponse($response) {
		$coroutineId = self::getCoroutineId();
		self::$context[$coroutineId][self::RESPONSE_KEY] = $response;
	}

	/**
	 * Set the context data
	 *
	 * @param array $contextData
	 */
	public function setContextData(array $contextData = []) {
		$existContext = [];
		$coroutineId = self::getCoroutineId();
		if (isset(self::$context[$coroutineId][self::DATA_KEY])) {
			$existContext = self::$context[$coroutineId][self::DATA_KEY];
		}
		self::$context[$coroutineId][self::DATA_KEY] = array_merge([], $contextData, $existContext);
	}

	/**
	 * Update context data by key
	 *
	 * @param string $key
	 * @param mixed $val
	 */
	public function setContextDataByKey(string $key, $val) {
		$coroutineId = self::getCoroutineId();
		self::$context[$coroutineId][self::DATA_KEY][$key] = $val;
	}

	/**
	 * Get context data by key
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getContextDataByKey(string $key, $default = null) {
		$data = $this->getContextData();
		if ($data && isset($data[$key])) {
			return $data[$key];
		}

		return $default;
	}

	public function fork($parentCoId) {
		self::$context[self::getCoroutineId()] = self::$context[$parentCoId] ?? [];
	}

	/**
	 * Destroy all current coroutine context data
	 */
	public function destroy($coroutineId = null) {
		if (!$coroutineId) {
			$coroutineId = self::getCoroutineId();
		}
		
		if (isset(self::$context[$coroutineId])) {
			unset(self::$context[$coroutineId]);
		}
	}

	public function all() {
		return self::$context ?? [];
	}

	/**
	 * Get data from coroutine context by key
	 *
	 * @param string $key key of context
	 * @return mixed|null
	 */
	private function getCoroutineContext(string $key) {
		$coroutineId = self::getCoroutineId();
		if (!isset(self::$context[$coroutineId])) {
			return null;
		}

		$coroutineContext = self::$context[$coroutineId];
		if (isset($coroutineContext[$key])) {
			return $coroutineContext[$key];
		}
		return null;
	}

	/**
	 * Get current coroutine ID
	 *
	 * @return int|null Return null when in non-coroutine context
	 */
	public function getCoroutineId() {
		$coId = Coroutine::getuid();
		if ($coId != -1) {
			$this->lastCoId = $coId;
		}

		return $coId;
	}

	public function getLastCoId() {
		return $this->lastCoId;
	}
}
