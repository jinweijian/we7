<?php

/**
 * Rangine database Tool
 *
 * (c) We7Team 2019 <https://www.rangine.com>
 *
 * document http://s.w7.cc/index.php?c=wiki&do=view&id=317&list=2284
 *
 * visited https://www.rangine.com for more details
 */

namespace W7\DatabaseTool\Migrate;

use Illuminate\Database\Migrations\MigrationCreator as MigrationCreatorAbstract;
use Illuminate\Support\Str;

class MigrationCreator extends MigrationCreatorAbstract {
	/**
	 * Get the path to the stubs.
	 *
	 * @return string
	 */
	public function stubPath() {
		return __DIR__ . '/../Command/Migrate/stubs';
	}

	/**
	 * Get the class name of a migration name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function getClassName($name) {
		return Str::studly($name) . $this->getDatePrefix();
	}
}
