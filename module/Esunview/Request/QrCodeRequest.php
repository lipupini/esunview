<?php

namespace Module\Esunview\Request;

require(__DIR__ . '/../vendor/autoload.php');

use Module\Esunview\Request\Html\GiftRequest;
use Module\Lipupini\Collection;
use Module\Lipupini\Exception;
use Module\Lipupini\Request;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeRequest extends Request\Queued {
	use Collection\Trait\CollectionRequest;

	public function initialize(): void {
		// URLs start with `/@` (but must be followed by something and something other than `/` or `?`)
		if (!preg_match('#^' . preg_quote($this->system->baseUriPath) . 'qr/.#', $_SERVER['REQUEST_URI_DECODED'])) return;
		// To be considered a folder request, there must not be an extension
		if (pathinfo($_SERVER['REQUEST_URI_DECODED'], PATHINFO_EXTENSION)) return;
		$this->collectionNameFromSegment(2);
		$this->collectionFolder = preg_replace(
			'#^' . preg_quote($this->system->baseUriPath) . 'qr/' . preg_quote($this->collectionName) . '/?#', '',
			$_SERVER['REQUEST_URI_DECODED']
		);
		(new Collection\Utility($this->system))->validateCollectionFolder($this->collectionName, $this->collectionFolder);

		$url = $this->system->baseUri;
		if (!empty($_GET['type'])) {
			if (!in_array($_GET['type'], GiftRequest::$words) && $_GET['type'] !== 'g') {
				throw new Exception('Invalid type');
			}
			$url .= $_GET['type'] . '/';
		} else {
			$url .= '@';
		}
		$url .= rtrim($this->collectionName . '/' . $this->collectionFolder, '/');
		$result = Builder::create()
			->writer(new PngWriter())
			->writerOptions([])
			->data($url)
			->encoding(new Encoding('UTF-8'))
			->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
			->size(300)
			->margin(10)
			->roundBlockSizeMode(RoundBlockSizeMode::Margin)
			/*->logoPath(__DIR__.'/assets/symfony.png')
			->logoResizeToWidth(50)
			->logoPunchoutBackground(true)*/
			->validateResult(false)
			->build()
		;

		$this->system->responseType = 'image/png';
		$this->system->responseContent = $result->getString();
		$this->system->shutdown = true;
	}
}
