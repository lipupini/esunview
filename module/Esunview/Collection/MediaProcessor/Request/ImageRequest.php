<?php

namespace Module\Esunview\Collection\MediaProcessor\Request;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

use Imagine;
use Module\Esunview\Payment\Gateway;
use Module\Lipupini\Collection;
use Module\Esunview\Collection\MediaProcessor\Image;
use Module\Lipupini\Collection\MediaProcessor\Request\MediaProcessorRequest;

class ImageRequest extends MediaProcessorRequest {
	public function initialize(): void {
		if (!($mediaRequest = $this->validateMediaProcessorRequest())) return;

		if (!preg_match('#^image/(' . implode('|', array_keys($this->system->mediaSize)) . ')/(.+\.(' . implode('|', array_keys($this->system->mediaType['image'])) . '))$#', $mediaRequest, $matches)) {
			return;
		}

		// If the URL has matched, we're going to shut down after this module returns no matter what
		$this->system->shutdown = true;

		$sizePreset = $matches[1];
		$imagePath = $matches[2];
		$extension = $matches[3];

		// We can use the same function that `Module\Lipupini\Collection\Request` uses
		// Doing it again here because this one comes from a different part of a URL from the regex
		(new Collection\Utility($this->system))->validateCollectionName($this->collectionName);

		$watermarkImageFile = $this->system->dirCollection . '/' . $this->collectionName . '/.lipupini/watermark.png';
		if (
			$sizePreset === 'large' && file_exists($watermarkImageFile) &&
			!(new Gateway($this->system))->itemPurchased($this->collectionName, $imagePath)
		) {
			header('Location: ' . '/@' . Collection\Utility::urlEncodeUrl($this->collectionName . '/' . $imagePath . '.html'));
			exit();
		}

		$this->serve(
			Image::processAndCache($this->system, $this->collectionName, 'image', $sizePreset, $imagePath),
			$this->system->mediaType['image'][$extension]
		);
	}
}
