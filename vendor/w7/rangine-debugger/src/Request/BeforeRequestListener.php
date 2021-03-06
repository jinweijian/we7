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
use W7\Http\Message\Server\Request;

class BeforeRequestListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var Request $request
		 */
		$request = $params[0];
		Context::setRequest($request);
		Context::setContextDataByKey('trace_group', '[ url: ' . $request->getUri()->getPath() . ' ]');
		Context::setContextDataByKey('memory_usage', memory_get_usage());
		Context::setContextDataByKey('time', microtime(true));

		$this->log($request);
	}

	protected function log(Request $request) {
		itrace('begin-request', 'method: ' . $request->getMethod() . ', url: ' . $request->getUri()->getPath() . ', ip: ' . serialize(getClientIp()) . ', time: ' . date('Y-m-d H:i:s'));
		itrace('request-header', serialize($request->getHeaders()));
		itrace('request-data', 'post: ' . serialize($request->getParsedBody()) . ', query: ' . serialize($request->getQueryParams()));
	}
}
