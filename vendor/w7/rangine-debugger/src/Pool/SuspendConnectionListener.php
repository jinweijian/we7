<?php

namespace W7\Debugger\Pool;

use W7\Core\Pool\Event\SuspendConnectionEvent;

class SuspendConnectionListener extends PoolListenerAbstract {
	public function run(...$params) {
		/**
		 * @var SuspendConnectionEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	protected function log($event) {
		itrace($event->type, $event->name . ' suspend connection , count ' . $event->pool->getIdleCount() . '. wait count ' . $event->pool->getWaitCount());
	}
}