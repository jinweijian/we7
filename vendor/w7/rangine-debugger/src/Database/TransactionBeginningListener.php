<?php

namespace W7\Debugger\Database;

use W7\Core\Database\Event\TransactionBeginningEvent;

class TransactionBeginningListener extends DatabaseListenerAbstract {
	public function run(...$params) {
		/**
		 * @var TransactionBeginningEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}
}