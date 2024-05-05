<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

namespace Module\Lipupini\Request;

use Module\Lipupini\State;

class Queue {
	private bool $serveStaticRequest = false;

	public function __construct(protected State $system) {
		if (
			// Using PHP's builtin webserver, this will return a static file (e.g. CSS, JS, image) if it exists at the requested path
			$_SERVER['PHP_SELF'] !== '/index.php' &&
			PHP_SAPI === 'cli-server' &&
			file_exists($this->system->dirWebroot . $_SERVER['PHP_SELF'])
		) {
			$this->serveStaticRequest = true;
			return;
		}

		if ($this->system->debug) {
			error_log('Incoming request details:');
			error_log(print_r($_REQUEST, true));
			error_log(print_r($_SERVER, true));
			error_log(print_r(file_get_contents('php://input'), true));
		}
	}

	public function processRequestQueue(): void {
		if ($this->serveStaticRequest) {
			return;
		}

		foreach ($this->system->request as $requestClassName => $initialState) {
			$this->loadRequestModule($requestClassName);
			if ($this->system->shutdown) {
				return;
			}
		}
	}

	public function loadRequestModule(string $requestClassName): void {
		if (
			array_key_exists($requestClassName, $this->system->request) &&
			!is_null($this->system->request[$requestClassName])
		) {
			throw New Exception('Already loaded request: ' . $requestClassName);
		}

		if (!class_exists($requestClassName)) {
			throw new Exception('Could not load module: ' . $requestClassName);
		}

		$request = new $requestClassName($this->system);

		$this->system->request[$requestClassName] = $request;
	}

	public function render(): bool {
		if ($this->serveStaticRequest) {
			return false;
		}

		try {
			$this->processRequestQueue();
		} catch (\Exception $e) {
			http_response_code($e->getCode() ?: 500);
			$message = [
				htmlentities($e->getMessage()),
				'File: ' . str_replace($this->system->dirRoot, '', $e->getFile()),
				'Line: ' . $e->getLine(),
			];
			$this->system->responseContent = '<p>' . implode('</p><p>', $message) . '</p>';
		}

		$microtimeLater = microtime(true);
		$this->system->executionTimeSeconds = $microtimeLater - $this->system->microtimeInit;

		header('X-Powered-By: Lipupini');

		if ($this->system->debug) {
			header('Server-Timing: app;dur=' . $this->system->executionTimeSeconds);
		}

		if ($this->system->responseType) {
			header('Content-type: ' . $this->system->responseType);
		}

		if ($this->system->clientCache) {
			$expiresOffset = 86400; // 1 day
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expiresOffset) . ' GMT');
			header('Cache-Control: public, max-age=' . $expiresOffset);
		} else {
			header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
			header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
		}

		if ($this->system->responseContent) {
			echo $this->system->responseContent;
		} else {
			http_response_code(404);
			echo '<pre>404 Not found' . "\n\n";
			if ($this->system->debug) {
				echo '<!-- ' . $this->system->executionTimeSeconds . ' -->';
			}
		}

		return true;
	}
}
