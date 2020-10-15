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

namespace W7\Core\Task;

interface TaskInterface {
	/**
	 * 线程具体执行内容
	 * @return mixed
	 */
	public function run($server, $taskId, $workId, $data);

	/**
	 * 任务中定义完成回调
	 */
	public function finish($server, $taskId, $data, $params);
}