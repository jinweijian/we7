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
use W7\Core\Facades\Config;
use W7\Core\Process\ProcessAbstract;

class ProcessStartListener extends ListenerAbstract {
	public function run(...$params) {
		list($serverType, $process, $workerId, $processFactory, $mqKey) = $params;
		//重新播种随机因子
		mt_srand();

		/**
		 * @var ProcessAbstract $userProcess
		 */
		$userProcess = $processFactory->make($workerId);
		$userProcess->setProcess($process);
		$userProcess->setServerType($serverType);
		$userProcess->setWorkerId($workerId);
		$name = $userProcess->getName();

		$mqKey = Config::get("process.process.$name.message_queue_key", $mqKey);
		$mqKey = (int)$mqKey;
		$userProcess->setMq($mqKey);

		isetProcessTitle($userProcess->getProcessName());

		//用临时变量保存该进程中的用户进程对象
		App::getApp()->userProcess = $userProcess;

		$userProcess->onStart();
	}
}
