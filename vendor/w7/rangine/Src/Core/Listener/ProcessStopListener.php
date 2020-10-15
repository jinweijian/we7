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

namespace W7\Core\Listener;

use W7\App;
use W7\Core\Facades\Container;
use W7\Core\Facades\Logger;

class ProcessStopListener extends ListenerAbstract {
	public function run(...$params) {
		$userProcess = App::getApp()->userProcess;
		if (empty($userProcess)) {
			return false;
		}

		try {
			$userProcess->onStop();
		} catch (\Throwable $e) {
			Logger::debug('stop process fail with error ' . $e->getMessage());
		}

		Container::clear();
	}
}
