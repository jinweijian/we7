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

namespace W7\Fpm\Listener;

use W7\Core\Helper\Storage\Context;
use W7\Core\Listener\ListenerAbstract;
use W7\Core\Facades\Context as ContextFacade;

class AfterWorkerShutDownListener extends ListenerAbstract {
	public function run(...$params) {
		$contexts = ContextFacade::all();
		foreach ($contexts as $id => $context) {
			if (!empty($context[Context::RESPONSE_KEY]) && !empty($context['data']['server-type']) && $context['data']['server-type'] == 'fpm') {
				echo '发生致命错误，请在日志中查看错误原因，workid：' . ($context['data']['workid'] ?? '') . '，coid：' . ContextFacade::getLastCoId() . '。';
			}
		}
	}
}
