<?php

namespace W7\Debugger\Pool;

use W7\Core\Pool\Event\PushConnectionEvent;

class PushConnectionListener extends PoolListenerAbstract {
	public function run(...$params) {
		/**
		 * @var PushConnectionEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	protected function log($event) {
		itrace($event->type, $event->name . ' release push connection , count ' . $event->pool->getIdleCount() . '. busy count ' . $event->pool->getBusyCount());
	}
}