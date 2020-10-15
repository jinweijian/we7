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

namespace W7\Http\Message\Outputer;

use W7\Http\Message\File\File;

interface ResponseOutputerInterface {
	/**
	 * 响就头部
	 * @param array $headers
	 * @return mixed
	 */
	public function sendHeader(array $headers);

	/**
	 * 不关闭连接，持续向客户端写入数据
	 * 相当于 flush() 强制输出缓冲区内容到浏览器
	 * 也可以用于发送Chunk数据
	 * @param string $content
	 * @return mixed
	 */
	public function sendChunk(string $content);

	/**
	 * 响应Cookie
	 * @param array $cookies
	 */
	public function sendCookie(array $cookies);

	/**
	 * 响应文件，与响应内容同时只能使用一个
	 * @param File $file
	 */
	public function sendFile(File $file);

	/**
	 * 响应内容
	 * @param string $content
	 */
	public function sendBody(string $content);

	/**
	 * 响应状态码
	 * @param int $code
	 */
	public function sendStatus(int $code);

	public function disConnect();
}
