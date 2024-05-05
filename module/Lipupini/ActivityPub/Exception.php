<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

namespace Module\Lipupini\ActivityPub;

class Exception extends \Module\Lipupini\Exception {
	public function __toString(): string {
		http_response_code($this->getCode());
		return json_encode([
			'error' => $this->getMessage(),
			'code' => $this->getCode(),
		]);
	}
}
