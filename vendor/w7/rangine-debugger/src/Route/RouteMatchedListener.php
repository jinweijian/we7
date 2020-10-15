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

namespace W7\Debugger\Route;

use W7\Core\Listener\ListenerAbstract;
use W7\Core\Route\Event\RouteMatchedEvent;

class RouteMatchedListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var RouteMatchedEvent $event
		 */
		$event = $params[0];

		$this->log($event);
	}

	protected function log(RouteMatchedEvent $event) {
		$handler = $event->route['controller'] instanceof \Closure ? 'closure' : $event->route['controller'] . '@' . $event->route['method'];
		$middleWares = [];
		array_walk_recursive($event->route['middleware'], function ($middleware) use (&$middleWares) {
			$middleWares[] = $middleware;
		});

		itrace('route', 'name: ' . $event->route['name'] . ', module: ' . $event->route['module'] . ', handler: ' . $handler);
		itrace('middleware', implode(',', $middleWares));
	}
}
