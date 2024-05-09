<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);

const FRONTEND_CACHE_VERSION = '1';

require(__DIR__ . '/../../module/Lipupini/vendor/autoload.php');

$httpHost = php_sapi_name() === 'cli' && empty($_SERVER['HTTP_HOST']) ? 'localhost' : $_SERVER['HTTP_HOST'];
$isHttps = !empty($_SERVER['HTTPS']) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$baseUri = 'http' . ($isHttps ? 's' : '') . '://' . $httpHost . '/';

return new Module\Esunview\State(
	contactEmail: 'your@email.ocm',
	stripeKey: 'sk_test_EXAMPLE',
	//stripeKey: 'sk_live_EXAMPLE',
	baseUri: $baseUri, // Include trailing slash
	staticMediaBaseUri: $baseUri . 'c/', // You can put a CDN URL here. Include trailing slash.
	frontendModule: 'Esunview',
	viewLanguage: 'en',
	itemsPerPage: 36,
	mediaSize: ['large' => [7680, 4320], 'medium' => [1200, 1200], 'watermark' => [1200, 1200], 'thumbnail' => [500, 500]], // Default [width, height] for each preset. You can add more, and renaming the defaults is not recommended
	mediaType: [
		'audio' => [
			'flac' => 'audio/flac',
			'm4a' => 'audio/mp4',
			'mp3' => 'audio/mp3',
		],
		'image' => [
			'avif' => 'image/avif',
			'gif' => 'image/gif',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png' => 'image/png',
		],
		'text' => [
			'html' => 'text/html',
			'md' => 'text/markdown',
		],
		'video' => [
			'mp4' => 'video/mp4',
		],
	],
	imageQuality: ['avif_quality' => 69, 'jpeg_quality' => 86, 'png_compression_level' => 9],
	useFfmpeg: true, // You can try enabling this if you have `ffmpeg` installed for processing videos
	request: [
		// Once instantiated by Module\Lipupini\Request\Incoming\Queue `render()`,
		// each `request` key here will instead hold the module instance itself
		Module\Esunview\Request\Html\HomepageRequest::class => null,
		Module\Esunview\Request\Html\GiftRequest::class => null,
		Module\Lipupini\Api\Request::class => null,
		Module\Lipupini\Rss\Request::class => null,
		Module\Lipupini\WebFinger\Request::class => null,
		Module\Lipupini\ActivityPub\Request::class => null,
		Module\Lipupini\ActivityPub\NodeInfoRequest::class => null,
		Module\Esunview\Request\Html\Collection\FolderRequest::class => null,
		Module\Esunview\Request\Html\Collection\MediaItemRequest::class => null,
		Module\Esunview\Request\Html\Collection\ListRequest::class => null,
		Module\Esunview\Request\Html\Collection\PurchaseRequest::class => null,
		Module\Esunview\Request\Html\Collection\LiveRequest::class => null,
		Module\Esunview\Request\QrCodeRequest::class => null,
		Module\Lipupini\Collection\MediaProcessor\Request\AudioRequest::class => null,
		Module\Lipupini\Collection\MediaProcessor\Request\AudioThumbnailRequest::class => null,
		Module\Lipupini\Collection\MediaProcessor\Request\AudioWaveformRequest::class => null,
		Module\Lipupini\Collection\MediaProcessor\Request\AvatarRequest::class => null,
		Module\Esunview\Collection\MediaProcessor\Request\ImageRequest::class => null,
		Module\Lipupini\Collection\MediaProcessor\Request\TextRequest::class => null,
		Module\Lipupini\Collection\MediaProcessor\Request\VideoRequest::class => null,
		Module\Lipupini\Collection\MediaProcessor\Request\VideoThumbnailRequest::class => null,
	],
	activityPubLog: false,
	debug: false,
);
