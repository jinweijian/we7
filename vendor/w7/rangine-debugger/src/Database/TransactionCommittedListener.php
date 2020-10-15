<?php

namespace W7\Debugger\Database;

use W7\Core\Database\Event\TransactionCommittedEvent;

class TransactionCommittedListener extends DatabaseListenerAbstract {
	public function run(...$params) {
		/**
		 * @var TransactionCommittedEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}
}