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

namespace W7\Core\Bootstrap;

use W7\App;
use W7\Core\Cache\Provider\CacheProvider;
use W7\Core\Database\Provider\DatabaseProvider;
use W7\Core\Events\Provider\EventProvider;
use W7\Core\Facades\Config;
use W7\Core\Log\Provider\LogProvider;
use W7\Core\Provider\IlluminateProvider;
use W7\Core\Provider\ProviderManager;
use W7\Core\Provider\ValidateProvider;
use W7\Core\Route\Provider\RouterProvider;
use W7\Core\Session\Provider\SessionProvider;
use W7\Core\Task\Provider\TaskProvider;
use W7\Core\View\Provider\ViewProvider;

class ProviderBootstrap implements BootstrapInterface {
	private $providerMap = [
		'illuminate' => IlluminateProvider::class,
		'event' => EventProvider::class,
		'log' => LogProvider::class,
		'router' => RouterProvider::class,
		'database' => DatabaseProvider::class,
		'cache' => CacheProvider::class,
		'task' => TaskProvider::class,
		'view' => ViewProvider::class,
		'validate' => ValidateProvider::class,
		'session' => SessionProvider::class
	];

	public function bootstrap(App $app) {
		$app->getContainer()->set(ProviderManager::class, function () use ($app) {
			return new ProviderManager($app->getContainer());
		});

		$providers = Config::get('provider', []);
		$providers = array_merge($this->providerMap, $providers);

		$app->getContainer()->singleton(ProviderManager::class)->register($providers)->boot();
	}
}
