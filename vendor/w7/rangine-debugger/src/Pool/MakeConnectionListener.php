<?php

namespace W7\Debugger\Pool;

use W7\Core\Pool\Event\MakeConnectionEvent;

class MakeConnectionListener extends PoolListenerAbstract {
	public function run(...$params) {
		/**
		 * @var MakeConnectionEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	protected function log($event) {
		itrace($event->type, $event->name . ' create connection , count ' . $event->pool->getIdleCount() . '. busy count ' . $event->pool->getBusyCount());
	}
}