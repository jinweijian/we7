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

namespace W7\Http\Message\Base;

use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class Cookie extends SymfonyCookie {
	/**
	 * Default cookie properties
	 */
	public static $DEFAULTS = [
		'value' => '',
		'domain' => null,
		'path' => '/',
		'expires'  => 0,
		'secure' => null,
		'httpOnly' => true,
		'sameSite' => ''
	];

	public static function create(string $name, string $value = null, $expire = 0, ?string $path = null, string $domain = null, bool $secure = null, bool $httpOnly = null, bool $raw = false, ?string $sameSite = SymfonyCookie::SAMESITE_LAX): SymfonyCookie {
		//指定默认值
		is_null($expire) && $expire = intval(self::$DEFAULTS['expires']);
		is_null($path) && $path = self::$DEFAULTS['path'];
		is_null($domain) && $domain = self::$DEFAULTS['domain'];
		is_null($secure) && $secure = self::$DEFAULTS['secure'];
		is_null($httpOnly) && $httpOnly = boolval(self::$DEFAULTS['httpOnly']);
		empty($sameSite) && $sameSite = self::$DEFAULTS['sameSite'];

		return new self($name, $value, $expire, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void {
		$this->name = $name;
	}
}
