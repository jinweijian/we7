<?php

namespace W7\Debugger\Pool;

use W7\Core\Pool\Event\ResumeConnectionEvent;

class ResumeConnectionListener extends PoolListenerAbstract {
	public function run(...$params) {
		/**
		 * @var ResumeConnectionEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	protected function log($event) {
		itrace($event->type, $event->name . ' resume connection , count ' . $event->pool->getIdleCount());
	}
}