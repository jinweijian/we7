<?php

/**
 * WeEngine Api System
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\Http\Message\File;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class File extends SymfonyFile {

	private $offset;
	private $length;

	public function __construct(string $path, int $offset = 0, int $length = 0, bool $checkPath = true) {
		parent::__construct($path, $checkPath);

		if (!empty($offset)) {
			if ($offset > $this->getSize()) {
				throw new \InvalidArgumentException('Out of file range');
			}
		}
		$this->offset = $offset;
		$this->length = $length;
	}

	/**
	 * @return int
	 */
	public function getOffset(): int {
		return $this->offset;
	}

	/**
	 * @return int
	 */
	public function getLength(): int {
		return $this->length;
	}
}
