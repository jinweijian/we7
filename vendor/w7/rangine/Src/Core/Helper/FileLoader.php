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

namespace W7\Core\Helper;

use W7\Core\Facades\Config;

class FileLoader {
	protected $ignoreFiles;
	protected $loadRules;
	protected $loadDir;

	public function __construct($loadDir = BASE_PATH) {
		$this->loadDir = $loadDir;
		$this->loadRules = Config::get('app.setting.file_ignore', []);
	}

	public function getIgnoreFiles() {
		if (isset($this->ignoreFiles)) {
			return $this->ignoreFiles;
		}

		$loadDir = $this->loadDir;
		$matches = array();
		foreach ($this->loadRules as $loadRule) {
			$loadRule = trim($loadRule);
			if ($loadRule === '') {
				continue;
			}
			if (substr($loadRule, 0, 1) == '#') {
				continue;
			}
			if (substr($loadRule, 0, 1) == '!') {
				$loadRule = substr($loadRule, 1);

				//!route/test.php 只会处理route目录下的包含关系
				$parentDir = dirname($loadRule);
				$parentLoadRule = $loadDir;
				if ($parentDir != '.' && $parentDir != '..') {
					$parentLoadRule .= DIRECTORY_SEPARATOR . $parentDir;
				}
				$files = array_diff(glob("$parentLoadRule/*"), glob("$loadDir/$loadRule"));
			} else {
				$files = glob("$loadDir/$loadRule");
			}
			$matches = array_merge($matches, $files);
		}

		return $this->ignoreFiles = $matches;
	}

	public function isIgnoreFile($file) {
		$ignoreFiles = $this->getIgnoreFiles();
		foreach ($ignoreFiles as $ignoreFile) {
			if ($file == $ignoreFile || strpos($file, $ignoreFile) === 0) {
				return true;
			}
		}

		return false;
	}

	public function loadFile($file) {
		if ($this->isIgnoreFile($file)) {
			return null;
		}

		return include $file;
	}
}
