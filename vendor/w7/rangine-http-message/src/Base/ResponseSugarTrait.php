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

namespace W7\Http\Message\Base;

use W7\Http\Message\Contract\Arrayable;
use W7\Http\Message\Helper\JsonHelper;

trait ResponseSugarTrait {
	/**
	 * Redirect to a URL
	 *
	 * @param string $url
	 * @param null|int $status
	 * @return static
	 */
	public function redirect($url, $status = 302) {
		$response = $this;
		$response = $response->withAddedHeader('Location', (string)$url)->withStatus($status);
		return $response;
	}

	/**
	 * return a Raw format response
	 *
	 * @param string $data The data
	 * @param int $status The HTTP status code.
	 * @param string $contentType
	 * @return static
	 */
	public function raw(string $data = '', int $status = 200, $contentType = 'text/plain') {
		$response = $this;
		$response = $response->withoutHeader('Content-Type')->withAddedHeader('Content-Type', $contentType);
		$this->getCharset() && $response = $response->withCharset($this->getCharset());
		($data || is_numeric($data)) && $response = $response->withContent($data);
		$status && $response = $response->withStatus($status);
		return $response;
	}

	/**
	 * return a Json format response
	 *
	 * @param  array|Arrayable $data            The data
	 * @param  int             $status          The HTTP status code.
	 * @param  int             $encodingOptions Json encoding options
	 * @return static
	 * @throws \InvalidArgumentException
	 */
	public function json($data = [], int $status = 200, int $encodingOptions = JSON_UNESCAPED_UNICODE) {
		$response = $this;

		// Headers
		$response = $response->withoutHeader('Content-Type')->withAddedHeader('Content-Type', 'application/json');
		$this->getCharset() && $response = $response->withCharset($this->getCharset());

		if (is_string($data) || is_numeric($data) || is_bool($data)) {
			$data = ['data' => $data];
		}
		// Content
		if (is_array($data) || ($data instanceof \ArrayObject) || $data instanceof Arrayable) {
			$content = JsonHelper::encode($data, $encodingOptions);
			$response = $response->withContent($content);
		} else {
			$response = $response->withContent('{}');
		}

		// Status code
		$status && $response = $response->withStatus($status);

		return $response;
	}

	public function html(string $data = '', int $status = 200, $contentType = 'text/html') {
		return $this->raw($data, $status, $contentType);
	}
}
