<?php

/**
 * Rangine debugger
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\Debugger\Request;

use W7\Core\Facades\Context;
use W7\Core\Listener\ListenerAbstract;

class AfterRequestListener extends ListenerAbstract {
	public function run(...$params) {
		$this->log();
	}

	public function getCookies() {
		$cookies = [];
		foreach ((array)Context::getResponse()->getCookies() as $name => $cookie) {
			$cookies[] = [
				'name' => $cookie->getName(),
				'value' => $cookie->getValue() ? : 1,
				'expire' => $cookie->getExpiresTime(),
				'path' => $cookie->getPath(),
				'domain' => $cookie->getDomain(),
				'secure' => $cookie->isSecure(),
				'http_only' => $cookie->isHttpOnly()
			];
		}

		return $cookies;
	}

	protected function log() {
		$beginMemory = Context::getContextDataByKey('memory_usage');
		$memoryUsage = memory_get_usage() - $beginMemory;
		$time = round(microtime(true) - Context::getContextDataByKey('time'), 3);

		itrace('response-header', serialize(Context::getResponse()->getHeaders()));
		itrace('response-cookies', serialize($this->getCookies()));
		itrace('response-content', Context::getResponse()->getBody()->getContents());
		itrace('end-request', 'memory_usage: ' . round($memoryUsage/1024/1024, 2).'MB' . ', time: ' . $time . 's');
	}
}
