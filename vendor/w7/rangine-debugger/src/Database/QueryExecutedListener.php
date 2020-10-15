<?php

namespace W7\Debugger\Database;

use W7\Core\Database\Event\QueryExecutedEvent;

class QueryExecutedListener extends DatabaseListenerAbstract {
	public function run(...$params) {
		/**
		 * @var QueryExecutedEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}
}