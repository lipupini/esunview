<?php

namespace Module\Lipupini\ActivityPub;

use Module\Lipupini\Collection;
use Module\Lipupini\Request\Http;
use Module\Lipupini\Request\Ping;
use Module\Lipupini\State;

class Follow {
	use Collection\Trait\CollectionRequest;

	public function __construct(protected State $system) { }

	public function request(string $remote): bool {
		if ($this->system->debug) {
			error_log('DEBUG: ' . get_called_class());
		}

		if (empty($remote)) {
			throw new Exception('No remote account specified', 400);
		}

		if ($remote[0] === '@') {
			$remote = preg_replace('#^@#', '', $remote);
		}

		if (substr_count($remote, '@') !== 1) {
			throw new Exception('Invalid remote account format (E1)', 400);
		}

		// The reason to do this way is even if ActivityPub doesn't support paths in handles, I still want to in case it does somewhere
		// I even want it to support @example@localhost:1234/path
		// Ideally it will always be possible to test between localhost ports
		$exploded = explode('@', $remote);

		if (!Ping::host($exploded[1])) {
			throw new Exception('Could not ping remote host @ ' . $exploded[1] . ', giving up', 400);
		}

		$remoteActor = RemoteActor::fromHandle(
			handle: $remote,
			cacheDir: __DIR__ . '/cache'
		);

		$sendToInbox = $remoteActor->getInboxUrl();

		if (!filter_var($sendToInbox, FILTER_VALIDATE_URL)) {
			throw new Exception('Could not determine inbox URL', 400);
		}

		$this->collectionNameFromSegment(2);

		// Create the JSON payload for the Follow activity (adjust as needed)
		$followActivity = [
			'@context' => 'https://www.w3.org/ns/activitystreams',
			'id' => $this->system->baseUri . 'ap/' . $this->collectionName . '/profile#follow/' . md5(rand(0, 1000000) . microtime(true)),
			'type' => 'Follow',
			'actor' => $this->system->baseUri . 'ap/' . $this->collectionName . '/profile',
			'object' => $remoteActor->getId(),
		];

		$activityJson = json_encode($followActivity, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

		Http::sendSigned(
			keyId: $this->system->baseUri . 'ap/' . $this->collectionName . '/profile#main-key',
			privateKeyPem: file_get_contents($this->system->dirCollection . '/' . $this->collectionName . '/.lipupini/rsakey.private'),
			inboxUrl: $remoteActor->getInboxUrl(),
			body: $activityJson,
			headers: [
				'Content-type' => Request::$mimeType,
				'Accept' => Request::$mimeType,
				'User-agent' => $this->system->userAgent,
			]
		);

		return true;
	}
}
