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

namespace W7\Core\Cache;

use Psr\SimpleCache\CacheInterface;

class CacheManager {
	protected $caches = [];
	protected $defaultChannel;
	/**
	 * @var ConnectionResolver
	 */
	protected $connectionResolver;

	public function __construct($defaultChannel = 'default') {
		$this->defaultChannel = $defaultChannel;
	}

	public function setConnectionResolver($connectionResolver) {
		$this->connectionResolver = $connectionResolver;
	}

	public function channel($name = 'default') : CacheInterface {
		return $this->getCache($name);
	}

	protected function getCache($channel) {
		if (empty($this->caches[$channel])) {
			$cache = new Cache($channel);
			$cache->setConnectionResolver($this->connectionResolver);
			$this->caches[$channel] = $cache;
		}

		return $this->caches[$channel];
	}

	public function registerCache(CacheAbstract $cache) {
		$this->caches[$cache->getName()] = $cache;
	}

	public function __call($name, $arguments) {
		return $this->channel()->$name(...$arguments);
	}
}
