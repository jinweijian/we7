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

namespace W7;

use W7\Console\Application;
use W7\Core\Bootstrap\BootstrapInterface;
use W7\Core\Bootstrap\LoadConfigBootstrap;
use W7\Core\Bootstrap\ProviderBootstrap;
use W7\Core\Bootstrap\RegisterHandleExceptionsBootstrap;
use W7\Core\Bootstrap\RegisterRuntimeEnvBootstrap;
use W7\Core\Bootstrap\RegisterSecurityDirBootstrap;
use W7\Core\Config\Config;
use W7\Core\Container\Container;
use W7\Core\Facades\Output;
use W7\Core\Server\ServerAbstract;

class App {
	const NAME = 'w7-rangine';
	const VERSION = '2.3.10';

	public static $self;
	/**
	 * 服务器对象
	 *
	 * @var ServerAbstract
	 */
	public static $server;
	/**
	 * @var Container
	 */
	protected $container;

	protected $bootstrapMap = [
		LoadConfigBootstrap::class,
		RegisterRuntimeEnvBootstrap::class,
		RegisterHandleExceptionsBootstrap::class,
		ProviderBootstrap::class,
		RegisterSecurityDirBootstrap::class
	];

	public function __construct() {
		self::$self = $this;

		$this->bootstrap();
	}

	protected function bootstrap() {
		foreach ($this->bootstrapMap as $bootstrap) {
			/**
			 * @var BootstrapInterface $bootstrap
			 */
			$bootstrap = new $bootstrap();
			$bootstrap->bootstrap($this);
		}
	}

	public static function getApp() {
		if (!self::$self) {
			new static();
		}
		return self::$self;
	}

	public function runConsole() {
		try {
			$this->getContainer()->singleton(Application::class)->run();
		} catch (\Throwable $e) {
			Output::error($e->getMessage());
		}
	}

	public function getContainer() {
		if (empty($this->container)) {
			$this->container = new Container();
		}
		return $this->container;
	}

	public function getConfigger() {
		return $this->getContainer()->singleton(Config::class);
	}

	public function bootstrapCachePath($path = '') {
		return BASE_PATH . DIRECTORY_SEPARATOR . 'bootstrap/cache' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
	}

	public function getRouteCachePath() {
		return $this->bootstrapCachePath('route/');
	}

	public function getConfigCachePath() {
		return $this->bootstrapCachePath('config/');
	}

	public function exit() {
		$this->container->clear();
	}
}
