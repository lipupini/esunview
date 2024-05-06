<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

namespace Module\Esunview\Request\Html;

use Module\Lipupini\Collection\MediaProcessor\Parsedown;
use Module\Lipupini\Request;

class HomepageRequest extends Request\Html {
	protected array $sections = [];
	public function initialize(): void  {
		if (parse_url($_SERVER['REQUEST_URI_DECODED'], PHP_URL_PATH) !== $this->system->baseUriPath) {
			return;
		}

		$this->sections = [
			'readme' => Parsedown::instance()->text(file_get_contents($this->system->dirRoot . '/README.md')),
			'selling' => Parsedown::instance()->text(file_get_contents($this->system->dirRoot . '/SELLING.md')),
		];

		$this->pageTitle = 'Homepage@' . $this->system->host;
		$this->addStyle('/css/Global.css');
		$this->addStyle('/css/Homepage.css');
		$this->renderHtml();
		$this->system->responseType = 'text/html';
		$this->system->shutdown = true;
	}

	public function renderHtml(): void {
		ob_start();
		header('Content-type: text/html');
		require(__DIR__ . '/../../Html/Homepage.php');
		$this->system->responseContent = ob_get_clean();
	}
}
