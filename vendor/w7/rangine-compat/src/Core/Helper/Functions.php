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

use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use W7\App;
use W7\Core\Exception\ValidatorException;
use W7\Core\Facades\Context;
use W7\Core\Facades\Event;
use W7\Core\Facades\Container;
use W7\Core\Facades\Router;
use W7\Core\Facades\DB;
use W7\Core\Facades\Output;

if (!function_exists('ieventDispatcher')) {
	function ieventDispatcher() {
		/**
		 * @deprecated
		 * @var \W7\Core\Events\Dispatcher $dispatcher
		 */
		return Event::getFacadeRoot();
	}
}

if (!function_exists('ievent')) {
	/**
	 * 派发一个事件
	 * @deprecated
	 * @param $eventName
	 * @param array $args
	 * @param bool $halt
	 * @return array|null
	 */
	function ievent($eventName, $args = [], $halt = false) {
		return Event::dispatch($eventName, $args, $halt);
	}
}
if (!function_exists('iloader')) {

	/**
	 * 别名
	 * @deprecated
	 * @return \W7\Core\Container\Container
	 */
	function iloader() {
		return icontainer();
	}

	/**
	 * 获取容器
	 * @deprecated
	 * @return \W7\Core\Container\Container
	 */
	function icontainer() {
		return Container::getFacadeRoot();
	}
}

if (!function_exists('ioutputer')) {
	/**
	 * 获取输出对象
	 * @deprecated
	 * @return W7\Console\Io\Output
	 */
	function ioutputer() {
		return Output::getFacadeRoot();
	}
}

if (!function_exists('iconfig')) {
	/**
	 * 输入对象
	 * @deprecated
	 * @return \W7\Core\Config\Config
	 */
	function iconfig() {
		return App::getApp()->getConfigger();
	}
}

if (!function_exists('ilogger')) {
	/**
	 * 返回logger对象
	 * @deprecated
	 * @return \W7\Core\Log\Logger
	 */
	function ilogger() {
		return App::getApp()->getLogger();
	}
}

if (!function_exists('idb')) {
	/**
	 * 返回一个数据库连接对象
	 * @deprecated
	 * @return \W7\Core\Database\DatabaseManager
	 */
	function idb() {
		return DB::getFacadeRoot();
	}
}

if (!function_exists('icontext')) {
	/**
	 * 返回logger对象
	 * @deprecated
	 * @return \W7\Core\Helper\Storage\Context
	 */
	function icontext() {
		return App::getApp()->getContext();
	}
}

if (!function_exists('icache')) {
	/**
	 * @deprecated
	 * @return \W7\Core\Cache\Cache
	 */
	function icache() {
		return App::getApp()->getCacher();
	}
}

if (!function_exists('irouter')) {
	/**
	 * @deprecated
	 * @return \W7\Core\Route\Router
	 */
	function irouter() {
		return Router::getFacadeRoot();
	}
}

if (!function_exists('ivalidator')) {
	/**
	 * @deprecated
	 * @return Factory
	 */
	function ivalidator() : Factory {
		return \W7\Core\Facades\Validator::getFacadeRoot();
	}
}

if (!function_exists('ivalidate')) {
	/**
	 * @deprecated
	 * @param array $data
	 * @param array $rules
	 * @param array $messages
	 * @param array $customAttributes
	 * @return array
	 */
	function ivalidate(array $data, array $rules, array $messages = [], array $customAttributes = []) {
		try {
			/**
			 * @var Factory $validate
			 */
			$result = ivalidator()->make($data, $rules, $messages, $customAttributes)
				->validate();
		} catch (ValidationException $e) {
			$errorMessage = [];
			$errors = $e->errors();
			foreach ($errors as $field => $message) {
				$errorMessage[] = $message[0];
			}
			throw new ValidatorException(implode('; ', $errorMessage), 403);
		}

		return $result;
	}
}

if (!function_exists('itask')) {
	/**
	 * 派发一个异步任务
	 * @deprecated
	 * @param string $taskName
	 * @param array $params
	 * @param int $timeout
	 * @return false|int
	 * @throws \W7\Core\Exception\TaskException
	 */
	function itask($taskName, $params = [], int $timeout = 3) {
		return \W7\Core\Facades\Task::dispatch($taskName, $params, $timeout);
	}

	/**
	 * @deprecated
	 * @param $taskName
	 * @param array $params
	 * @param int $timeout
	 * @return mixed
	 */
	function itaskCo($taskName, $params = [], int $timeout = 3) {
		return itask($taskName, $params, $timeout);
	}
}

if (!function_exists('irandom')) {
	/**
	 * @deprecated
	 * @param $length
	 * @param bool $numeric
	 * @return string
	 */
	function irandom($length, $numeric = false) {
		$seed = base_convert(md5(microtime()), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
		if ($numeric) {
			$hash = '';
		} else {
			$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
			$length--;
		}
		$max = strlen($seed) - 1;
		for ($i = 0; $i < $length; $i++) {
			$hash .= $seed[mt_rand(0, $max)];
		}
		return $hash;
	}
}

if (!function_exists('iuuid')) {
	/**
	 * 获取UUID
	 * @deprecated
	 * @return string
	 */
	function iuuid() {
		$len = rand(2, 16);
		$prefix = md5(substr(md5(Context::getCoroutineId()), $len));
		return uniqid($prefix);
	}
}