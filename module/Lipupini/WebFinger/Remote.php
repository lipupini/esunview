<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

namespace Module\Lipupini\WebFinger;

use Module\Lipupini\Request\Http;

class Remote {
	public static function acct(string $acct) {
		$exploded = explode('@', $acct);
		$webFingerUrl = 'https://' . $exploded[1] . '/.well-known/webfinger?resource=acct:' . $acct;
		return Http::get($webFingerUrl, ['Accept' => Request::$mimeType])['body'];
	}
}
