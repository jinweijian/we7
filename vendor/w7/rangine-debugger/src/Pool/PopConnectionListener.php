<?php

namespace W7\Debugger\Pool;

use W7\Core\Pool\Event\PopConnectionEvent;

class PopConnectionListener extends PoolListenerAbstract {
	public function run(...$params) {
		/**
		 * @var PopConnectionEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	protected function log($event) {
		itrace($event->type, $event->name . ' get by queue , count ' . $event->pool->getIdleCount());
	}
}