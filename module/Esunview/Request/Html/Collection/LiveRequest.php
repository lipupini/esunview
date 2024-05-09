<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

namespace Module\Esunview\Request\Html\Collection;

use Module\Lipupini\Collection;
use Module\Lipupini\Request;

class LiveRequest extends Request\Html {
	use Collection\Trait\CollectionRequest;

	public function initialize(): void {
		// The URL path must be `/@` or `/@/`
		if (!preg_match('#^' . preg_quote($this->system->baseUriPath) . 'live/(?!\?|$)#', parse_url($_SERVER['REQUEST_URI_DECODED'], PHP_URL_PATH))) {
			return;
		}
		$this->collectionNameFromSegment(2);
		$this->pageTitle = 'live@' . $this->collectionName;
		$this->addStyle('/css/Global.css');
		$this->addStyle('/css/Live.css');
		$this->addStyle('/lib/videojs/video-js.min.css');
		$this->addScript('/lib/videojs/video.min.js');
		$this->addScript('/lib/videojs/videojs-http-streaming.min.js');
		$this->htmlFoot .= <<<HEREDOC
<script>
(function() {
const player = videojs('livestream', {
	liveui: true
})
const vidURL = 'https://e85b-2605-59c0-146-e910-851f-9d7-1bdb-b8.ngrok-free.app/hls/stream.m3u8'
player.src({ type: 'application/x-mpegurl', src: vidURL })
player.ready(function(){
	player.muted(true);
	player.play()
})
})();
</script>
HEREDOC;


		$this->renderHtml();
		$this->system->shutdown = true;
	}

	public function renderHtml(): void {
		ob_start();
		require($this->system->dirModule . '/' . $this->system->frontendModule . '/Html/Collection/Live.php');
		$this->system->responseContent = ob_get_clean();
		$this->system->responseType = 'text/html';
	}
}
