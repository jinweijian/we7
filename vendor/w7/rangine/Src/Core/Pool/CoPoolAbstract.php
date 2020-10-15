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

namespace W7\Core\Pool;

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use W7\Core\Facades\Event;
use W7\Core\Pool\Event\MakeConnectionEvent;
use W7\Core\Pool\Event\PopConnectionEvent;
use W7\Core\Pool\Event\PushConnectionEvent;
use W7\Core\Pool\Event\ResumeConnectionEvent;
use W7\Core\Pool\Event\SuspendConnectionEvent;

abstract class CoPoolAbstract implements PoolInterface {
	protected $poolName;

	protected $type;

	/**
	 * 最大连接数据
	 * @var int
	 */
	protected $maxActive = 100;

	/**
	 * 执行中连接队列
	 * @var \SplQueue $busyQueue
	 */
	protected $busyCount;

	/**
	 * 空间连接队列
	 * @var \SplQueue $idleQueue
	 */
	protected $idleQueue;

	/**
	 * 挂起协程ID队列，恢复时按顺序恢复
	 * @var \SplQueue
	 */
	protected $waitQueue;

	/**
	 * 等待数
	 * @var int
	 */
	protected $waitCount = 0;

	public function __construct($name) {
		$this->poolName = $name;

		$this->busyCount = 0;
		$this->waitCount = 0;

		$this->waitQueue = new \SplQueue();
	}

	public function getPoolName() {
		return $this->poolName;
	}

	abstract public function createConnection();

	public function getConnection() {
		//如果 执行队列数 等于 最大连接数，则挂起协程
		if ($this->busyCount >= $this->getMaxCount()) {
			//等待进程数++
			$this->waitCount++;

			Event::dispatch(new SuspendConnectionEvent($this->type, $this->poolName, $this));

			if ($this->suspendCurrentCo() == false) {
				//挂起失败时，抛出异常，恢复等待数
				$this->waitCount--;
				throw new \RuntimeException('Reach max connections! Cann\'t pending fetch!');
			}
			//回收连接时，恢复了协程，则从空闲中取出连接继续执行
			Event::dispatch(new ResumeConnectionEvent($this->type, $this->poolName, $this));
		}

		if ($this->getIdleCount() > 0) {
			Event::dispatch(new PopConnectionEvent($this->type, $this->poolName, $this));

			$connect = $this->getConnectionFromPool();
			$this->busyCount++;
			return $connect;
		}

		$connect = $this->createConnection();
		$this->busyCount++;

		Event::dispatch(new MakeConnectionEvent($this->type, $this->poolName, $this));

		return $connect;
	}

	public function releaseConnection($connection) {
		$this->busyCount--;
		if ($this->getIdleCount() < $this->getMaxCount()) {
			$this->setConnectionFormPool($connection);
			Event::dispatch(new PushConnectionEvent($this->type, $this->poolName, $this));

			if ($this->waitCount > 0) {
				$this->waitCount--;
				$this->resumeCo();
			}
			return true;
		}
	}

	public function getMaxCount() {
		return $this->maxActive;
	}

	/**
	 * @param int $maxActive
	 */
	public function setMaxCount(int $maxActive) {
		$this->maxActive = $maxActive;
		$this->idleQueue = new Channel($this->maxActive);
	}

	/**
	 * 挂起当前协程，以便之后恢复
	 */
	private function suspendCurrentCo() {
		$coid = Coroutine::getuid();
		$this->waitQueue->push($coid);
		return Coroutine::suspend($coid);
	}

	/**
	 * 从队列里恢复一个挂起的协程继续执行
	 * @return bool
	 */
	private function resumeCo() {
		$coid = $this->waitQueue->shift();
		if (!empty($coid)) {
			Coroutine::resume($coid);
		}
		return true;
	}

	private function getConnectionFromPool() {
		return $this->idleQueue->pop();
	}

	private function setConnectionFormPool($connection) {
		return $this->idleQueue->push($connection);
	}

	public function getIdleCount() {
		return $this->idleQueue->length();
	}

	public function getBusyCount() {
		return $this->busyCount;
	}

	public function getWaitCount() {
		return $this->waitCount;
	}
}
