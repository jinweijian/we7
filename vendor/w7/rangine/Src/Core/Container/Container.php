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

namespace W7\Core\Container;

use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container as PsrContainer;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface {
	private $container;
	private $psrContainer;
	private $deferredServices = [];
	private $deferredServiceLoaders = [];

	public function __construct() {
		$this->container = new PimpleContainer();
		$this->psrContainer = new PsrContainer($this->container);
	}

	public function registerDeferredService($services) {
		$services = (array)$services;
		$this->deferredServices = array_unique(array_merge($this->deferredServices, $services));
	}

	public function registerDeferredServiceLoader(\Closure $loader) {
		$this->deferredServiceLoaders[] = $loader;
	}

	public function loadDeferredService($service) {
		if (in_array($service, $this->deferredServices)) {
			//如果触发过一次后，不再进行下次触发
			unset($this->deferredServices[array_search($service, $this->deferredServices)]);
			foreach ($this->deferredServiceLoaders as $loader) {
				$loader($service);
			}
		}
	}

	/**
	 * @param $name
	 * @param $handle
	 * @param mixed ...$params
	 * @return void
	 */
	public function set($name, $handle, ...$params) {
		if (is_string($handle) && class_exists($handle)) {
			$handle = function () use ($handle, $params) {
				return new $handle(...$params);
			};
		}
		$this->container[$name] = $handle;
	}

	public function has($name) {
		return $this->psrContainer->has($name);
	}

	/**
	 * @param $name
	 * @param array $params  当参数为标量或者数组时，可按参数进行单例
	 * @return mixed
	 */
	public function get($name, array $params = []) {
		$support = true;
		foreach ($params as $param) {
			if (!is_scalar($param) && !is_array($param)) {
				$support = false;
			}
		}
		if (!$support) {
			throw new \RuntimeException('when an object is included in a parameter, it cannot be singularized by a parameter');
		}
		$instanceKey = $name;
		if ($support && $params) {
			$instanceKey = md5($instanceKey . json_encode($params));
		}

		//检测是否为延迟加载服务，并触发加载器
		$this->loadDeferredService($name);

		if (!$this->has($instanceKey)) {
			//如果说这里的name不是类名的话，无法使用
			$this->set($instanceKey, $name, ...$params);
		}

		return $this->psrContainer->get($instanceKey);
	}

	/**
	 * 往键值上追回数据，只允许往数组和对象上追加。
	 * 键值不存的时候新建一个空数组
	 *
	 * 对象上追回等于设置属性
	 * @param $dataKey
	 * @param $value
	 * @param array $default
	 * @return void
	 */
	public function append($dataKey, array $value, $default = []) {
		if (!$this->has($dataKey)) {
			$this->set($dataKey, $default);
		}
		$data = $this->get($dataKey) ?? [];

		if (is_object($data)) {
			foreach ($value as $key => $item) {
				$data->$key = $item;
			}
		} elseif (is_array($data)) {
			foreach ($value as $key => $item) {
				$data[$key] = $item;
			}
		} else {
			throw new \RuntimeException('Only append data to array and object');
		}
		$this->set($dataKey, $data);
	}

	public function clone($name, array $params = []) {
		return clone $this->get($name, $params);
	}

	public function delete($name) {
		if ($this->has($name)) {
			unset($this->container[$name]);
		}
	}

	/**
	 * 语义上的别名，用于处理单例对象
	 * @param $name
	 * @param array $params
	 * @return mixed
	 */
	public function singleton($name, array $params = []) {
		return $this->get($name, $params);
	}

	public function clear() {
		foreach ($this->container->keys() as $key) {
			$this->delete($key);
		}
	}

	public function __call($name, $arguments) {
		return $this->container->$name(...$arguments);
	}
}
