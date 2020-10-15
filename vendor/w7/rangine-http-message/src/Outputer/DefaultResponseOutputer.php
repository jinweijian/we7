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

/**
 * 当进入服务之前就发生的错误
 * 使用默认输出器以报错的形式向客户端返回错误
 *
 * Class DefaultResponseOutputer
 * @package W7\Http\Message\Outputer
 */
class DefaultResponseOutputer extends ResponseOutputerAbstract {
	public function sendBody($content) {
		trigger_error($content, E_USER_ERROR);
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
	}
}
