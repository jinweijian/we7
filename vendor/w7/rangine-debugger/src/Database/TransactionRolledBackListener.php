<?php

namespace W7\Debugger\Database;

use W7\Core\Database\Event\TransactionRolledBackEvent;

class TransactionRolledBackListener extends DatabaseListenerAbstract {
	public function run(...$params) {
		/**
		 * @var TransactionRolledBackEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}
}