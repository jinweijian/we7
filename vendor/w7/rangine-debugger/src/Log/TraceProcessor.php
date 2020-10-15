<?php

/**
 * Rangine debugger
 *
 * (c) We7Team 2019 <https://www.w7.cc>
 *
 * This is not a free software
 * Using it under the license terms
 * visited https://www.w7.cc for more details
 */

namespace W7\Debugger\Log;

use W7\Core\Facades\Context;

class TraceProcessor {
	public function __invoke(array $record) {
		if (!empty($record['context']['trace_group'])) {
			$record['channel'] = $record['context']['trace_group'];
		}
		if (isset($record['context']['trace_group'])) {
			unset($record['context']['trace_group']);
		}
		if (Context::getContextDataByKey('trace_group')) {
			$record['channel'] = Context::getContextDataByKey('trace_group') . ' ' . $record['channel'];
		}

		return $record;
	}
}
