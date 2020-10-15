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

namespace W7\Http\Message;

use W7\Core\Provider\ProviderAbstract;
use W7\Http\Message\Base\Cookie;

class ServiceProvider extends ProviderAbstract {
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->initCookieEnv();
	}

	private function initCookieEnv() {
		$config = $this->config->get('app.cookie');

		Cookie::$DEFAULTS['httpOnly'] = isset($config['http_only']) ? $config['http_only'] : ini_get('session.cookie_httponly');
		Cookie::$DEFAULTS['path'] = isset($config['path']) ? $config['path'] : ini_get('session.cookie_path');
		Cookie::$DEFAULTS['domain'] = isset($config['domain']) ? $config['domain'] : ini_get('session.cookie_domain');
		Cookie::$DEFAULTS['secure'] = isset($config['secure']) ? $config['secure'] : ini_get('session.cookie_secure');
		Cookie::$DEFAULTS['sameSite'] = isset($config['same_site']) ? $config['same_site'] : Cookie::$DEFAULTS['sameSite'];
		Cookie::$DEFAULTS['expires'] = ini_get('session.gc_maxlifetime');

		$config = $this->config->get('app.session');
		if (isset($config['expires']) && $config['expires'] >= 0) {
			Cookie::$DEFAULTS['expires'] = (int)$config['expires'];
		}
	}
}
