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

namespace W7\Http\Message\Server;

use Psr\Http\Message\ResponseInterface;
use W7\Http\Message\Base\CookieTrait;
use W7\Http\Message\Base\ResponseSugarTrait;
use w7\Http\Message\File\File;
use W7\Http\Message\Outputer\ResponseOutputerAbstract;
use W7\Http\Message\Outputer\ResponseOutputerInterface;
use W7\Http\Message\Stream\SwooleStream;

/**
 * Class Request
 * @package W7\Http\Message\Server
 */
class Response extends \W7\Http\Message\Base\Response implements ResponseInterface {
	use CookieTrait;
	use ResponseSugarTrait;

	/**
	 * Original response data. When this is not null, it will be converted into stream content
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * @var File
	 */
	protected $file;

	/**
	 * @var \Throwable|null
	 */
	protected $exception;

	/**
	 * @var ResponseOutputerAbstract
	 */
	protected $outputer;

	protected $fd = null;

	public function __construct() {
	}

	/**
	 * 设置输出器，Swoole Fpm WebSocket Tcp
	 * @param ResponseOutputerInterface $outputer
	 */
	public function setOutputer(ResponseOutputerInterface $outputer): void {
		$this->outputer = $outputer;
	}

	/**
	 * @return ResponseOutputerInterface
	 */
	private function getOutputer(): ResponseOutputerInterface {
		if (!empty($this->fd)) {
			return $this->outputer->withFd($this->fd);
		} else {
			return $this->outputer;
		}
	}

	/**
	 * 不关闭连接，持续向客户端写入数据
	 * 相当于 flush() 强制输出缓冲区内容到浏览器
	 * 也可以用于发送Chunk数据
	 */
	public function write() {
		$this->withSendHeader();

		$this->getOutputer()->sendChunk($this->getBody()->getContents());
	}

	/**
	 * 处理 Response 并发送数据
	 */
	public function send() {
		$this->withSendHeader();

		/**
		 * 发送文件, 调用 sendfile 不必再调用 send
		 */
		if (!empty($this->file)) {
			return $this->getOutputer()->sendFile($this->file);
		} else {
			return $this->getOutputer()->sendBody($this->getBody()->getContents());
		}
	}

	/**
	 * 适用于长连接或是http发送chunk数据时主动关闭连接
	 */
	public function close() {
		return $this->getOutputer()->disConnect();
	}

	/**
	 * 设置Body内容，使用默认的Stream
	 *
	 * @param string $content
	 * @return static
	 */
	public function withContent($content): Response {
		$new = clone $this;
		$new->stream = new SwooleStream($content);
		return $new;
	}

	public function withFile(File $file) {
		$clone = clone $this;
		$clone->file = $file;
		return $clone;
	}

	public function withFd($fd) {
		$clone = clone $this;
		$clone->fd = $fd;
		return $clone;
	}

	private function withSendHeader() {
		$response = $this;

		if (strpos($this->getContentType(), 'charset=') === false) {
			$response = $this->withCharset($this->getCharset());
		}
		$this->getOutputer()->sendHeader($response->getHeaders());
		$this->getOutputer()->sendCookie($response->getCookies());
		$this->getOutputer()->sendStatus($response->getStatusCode());
		return $this;
	}
}
