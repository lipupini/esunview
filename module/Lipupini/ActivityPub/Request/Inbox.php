<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

namespace Module\Lipupini\ActivityPub\Request;

use Module\Lipupini\ActivityPub\Exception;
use Module\Lipupini\ActivityPub\RemoteActor;
use Module\Lipupini\ActivityPub\Request;
use Module\Lipupini\Request\Http;
use Module\Lipupini\Request\Signature;

class Inbox extends Request {
	public function initialize(): void {
		if ($this->system->debug) {
			error_log('DEBUG: ' . get_called_class());
		}

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			throw new Exception('Expected POST request', 400);
		}

		$requestBody = file_get_contents('php://input');
		$requestData = json_decode($requestBody);

		if (!$requestData) {
			throw new Exception('Could not load activity JSON', 400);
		}

		if (empty($requestData->actor)) {
			throw new Exception('Could not determine request actor', 400);
		}

		if (empty($requestData->type)) {
			throw new Exception('Could not determine request type', 400);
		}

		if (empty($requestData->id)) {
			throw new Exception('Could not determine request ID', 400);
		}

		if ($this->system->debug) {
			error_log('DEBUG: Received ' . $requestData->type . ' request from ' . $requestData->actor);
		}

		if (empty($_SERVER['HTTP_SIGNATURE'])) {
			throw new Exception('Expected request to be signed', 403);
		}

		$remoteActor = RemoteActor::fromUrl(
			url: $requestData->actor,
			cacheDir: __DIR__ . '/../cache'
		);

		if (!(new Signature)->verify(
			$remoteActor->getPublicKeyPem(),
			$_SERVER,
			parse_url($_SERVER['REQUEST_URI_DECODED'], PHP_URL_PATH), // Path without query string
			$requestBody
		)) {
			throw new Exception('HTTP Signature did not validate', 403);
		}

		$this->collectionNameFromSegment(2);

		/* BEGIN STORE INBOX ACTIVITY */
		if ($this->system->activityPubLog) {
			$inboxFolder = $this->system->dirCollection . '/'
				. $this->collectionName
				. '/.lipupini/inbox/';

			if (!is_dir($inboxFolder)) {
				mkdir($inboxFolder, 0755, true);
			}

			$activityQueueFilename = $inboxFolder
				. date('Ymdhis')
				. '-' . microtime(true)
				. '-' . preg_replace('#[^\w]#', '', $requestData->type) . '.json';

			file_put_contents($activityQueueFilename, json_encode($requestData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		}
		/* END STORE INBOX ACTIVITY */

		switch ($requestData->type) {
			case 'Follow' :
				http_response_code(202);
				$jsonData = [
					'@context' => 'https://www.w3.org/ns/activitystreams',
					'id' => $this->system->baseUri . 'ap/' . $this->collectionName . '/profile#accept/' . md5(rand(0, 1000000) . microtime(true)),
					'type' => 'Accept',
					'actor' => $this->system->baseUri . 'ap/' . $this->collectionName . '/profile',
					'object' => $requestData,
				];
				$this->system->responseContent = '"accepted"';
				break;
			case 'Undo' :
			case 'Accept' :
				$this->system->responseContent = '"ok"';
				return;
			default :
				throw new Exception('Unsupported ActivityPub type: ' . $requestData->type, 400);
		}

		$activityJson = json_encode($jsonData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

		Http::sendSigned(
			keyId: $this->system->baseUri . 'ap/' . $this->collectionName . '/profile#main-key',
			privateKeyPem: file_get_contents($this->system->dirCollection . '/' . $this->collectionName . '/.lipupini/rsakey.private'),
			inboxUrl: $remoteActor->getInboxUrl(),
			body: $activityJson,
			headers: [
				'Content-type' => $this::$mimeType,
				'Accept' => $this::$mimeType,
				'User-agent' => $this->system->userAgent,
			]
		);
	}
}
