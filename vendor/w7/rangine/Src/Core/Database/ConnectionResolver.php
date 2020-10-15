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

namespace W7\Core\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\DatabaseManager;
use Swoole\Coroutine;
use W7\Core\Database\Event\MakeConnectionEvent;
use W7\Core\Database\Pool\PoolFactory;
use W7\Core\Facades\Context;

class ConnectionResolver extends DatabaseManager {
	/**
	 * @var PoolFactory
	 */
	protected $poolFactory;

	public function setPoolFactory(PoolFactory $poolFactory) {
		$this->poolFactory = $poolFactory;
	}

	public function createConnection($name = null, $usePool = true) {
		list($database, $type) = $this->parseConnectionName($name);
		$name = $name ?: $database;

		if ($usePool && isCo() && $this->poolFactory && !empty($this->poolFactory->getPoolConfig($name)['enable'])) {
			$connection = $this->poolFactory->getPool($name)->getConnection();
			$connection->poolName = $this->poolFactory->getPool($name)->getPoolName();
			return $connection;
		}

		$connection = $this->configure(
			$this->makeConnection($database),
			$type
		);
		$this->app['events'] && $this->app['events']->dispatch(new MakeConnectionEvent($name, $connection));

		return $connection;
	}

	public function connection($name = null) {
		list($database, $type) = $this->parseConnectionName($name);
		$name = $name ?: $database;

		$contextDbName = $this->getContextKey($name);
		$connection = Context::getContextDataByKey($contextDbName);

		if (! $connection instanceof ConnectionInterface) {
			try {
				$connection = $this->createConnection($name);
				Context::setContextDataByKey($contextDbName, $connection);
			} finally {
				if ($connection && isCo()) {
					Coroutine::defer(function () use ($connection, $contextDbName) {
						$this->releaseConnection($connection);
						Context::setContextDataByKey($contextDbName, null);
					});
				}
			}
		}

		return $connection;
	}

	public function disconnect($name = null) {
		/**
		 * @var Connection $connection
		 */
		$connection = $this->getConnectionByNameFromContext($name);
		if ($connection) {
			$connection->disconnect();
		}
	}

	/**
	 * Reconnect to the given database.
	 *
	 * @param  string|null  $name
	 * @return \Illuminate\Database\Connection
	 */
	public function reconnect($name = null) {
		$this->disconnect($name = $name ?: $this->getDefaultConnection());

		if (!$this->getConnectionByNameFromContext($name)) {
			return $this->connection($name);
		}

		return $this->refreshPdoConnections($name);
	}

	/**
	 * Refresh the PDO connections on a given connection.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Connection
	 */
	protected function refreshPdoConnections($name) {
		$fresh = $this->makeConnection($name);

		/**
		 * @var Connection $connection
		 */
		$connection = $this->getConnectionByNameFromContext($name);
		return $connection->setPdo($fresh->getRawPdo())
			->setReadPdo($fresh->getRawReadPdo());
	}

	/**
	 * @deprecated
	 * @param null $name
	 * @throws \Exception
	 */
	public function beginTransaction($name = null) {
		return $this->connection($name)->beginTransaction();
	}

	private function releaseConnection($connection) {
		if (empty($connection->poolName)) {
			return true;
		}

		$pool = $this->poolFactory->getCreatedPool($connection->poolName);
		if (empty($pool)) {
			return true;
		}
		$pool->releaseConnection($connection);
	}

	private function getConnectionByNameFromContext($name = null) {
		list($database, $type) = $this->parseConnectionName($name);
		$contextDbName = $name ?: $database;
		$contextDbName = $this->getContextKey($contextDbName);
		return Context::getContextDataByKey($contextDbName);
	}

	private function getContextKey($name): string {
		return sprintf('database.connection.%s', $name);
	}
}
