<?php

/**
 * Rangine Http Message
 *
 * (c) We7Team 2019 <https://www.rangine.com/>
 *
 * document http://s.w7.cc/index.php?c=wiki&do=view&id=317&list=2284
 *
 * visited https://www.rangine.com/ for more details
 */

namespace W7\Http\Message\Contract;

interface Session {
	public function setId($sessionId);
	public function getId();
	public function set($key, $value);
	public function get($key, $default = '');
	public function has($key);
	public function delete($keys);
	public function destroy();
	public function close();
}
