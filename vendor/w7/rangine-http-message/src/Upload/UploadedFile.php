<?php

/**
 * Rangine Http Message
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\Http\Message\Upload;

use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

/**
 * Class Request
 * @package W7\Http\Message\Server
 */
class UploadedFile extends SymfonyUploadedFile implements UploadedFileInterface {
	public function getStream() {
		if (!$this->isFile() || empty($this->getRealPath())) {
			throw new FileNotFoundException($this->getRealPath());
		}
		return file_get_contents($this->getRealPath());
	}

	public function moveTo($targetPath) {
		if (php_sapi_name() == 'cli') {
			return rename($this->getPathname(), $targetPath);
		}
		$pathInfo = pathinfo($targetPath);
		return $this->move($pathInfo['dirname'], $pathInfo['basename']);
	}

	public function getSize() {
		return parent::getSize();
	}

	public function getError() {
		return parent::getError();
	}

	public function getClientFilename() {
		return $this->getClientOriginalName();
	}

	public function getClientMediaType() {
		return $this->getMimeType();
	}
}
