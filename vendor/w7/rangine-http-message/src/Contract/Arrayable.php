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

namespace W7\Http\Message\Contract;

/**
 * Interface Arrayable
 * @package W7\Contract
 */
interface Arrayable {
	/**
	 * @return array
	 */
	public function toArray(): array;
}
