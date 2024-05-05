<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

namespace Module\Lipupini\ActivityPub\Request;

use Module\Lipupini\ActivityPub\Request;

class Following extends Request {
	public function initialize(): void {
		if ($this->system->debug) {
			error_log('DEBUG: ' . get_called_class());
		}

		$this->system->responseContent = json_encode([], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
	}
}
