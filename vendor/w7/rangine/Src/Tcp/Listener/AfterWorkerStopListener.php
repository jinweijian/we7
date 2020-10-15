<?php

/**
 * This file is part of Rangine
 *
 * (c) We7Team 2019 <https://www.rangine.com/>
 *
 * document http://s.w7.cc/index.php?c=wiki&do=view&id=317&list=2284
 *
 * visited https://www.rangine.com/ for more details
 */

namespace W7\Tcp\Listener;

use W7\App;
use W7\Core\Facades\Container;
use W7\Core\Listener\ListenerAbstract;

class AfterWorkerStopListener extends ListenerAbstract {
	public function run(...$params) {
		if (Container::has('tcp-client')) {
			$clientCollector = Container::get('tcp-client') ?? [];
		} else {
			$clientCollector = [];
		}
		if (empty($clientCollector)) {
			return true;
		}

		foreach ($clientCollector as $fd => $client) {
			App::$server->getServer()->exists($fd) && App::$server->getServer()->close($fd);
		}

		Container::delete('tcp-client');
	}
}
