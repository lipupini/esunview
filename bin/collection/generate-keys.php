#!/usr/bin/env php
<?php

/*
 * License: Donationware
 * Homepage: https://c.dup.bz
*/

cli_set_process_title('Generate RSA Keys');

use Module\Lipupini\Collection;
use Module\Lipupini\State;
use Module\Lipupini\Encryption;

$readlineSupport = false;

/** @var State $systemState */
$systemState = require(__DIR__ . '/../../system/config/state.php');

if (empty($argv[1])) {
	if ($readlineSupport) {
		readline('No collection folder specified. Do you want to process all collections? [Y/n] ');
	} else {
		$confirm = 'Y';
	}
	if (strtoupper($confirm) !== 'Y') {
		exit(0);
	}
	foreach ((new Collection\Utility($systemState))->allCollectionFolders() as $collectionFolder) {
		passthru(__FILE__ . ' ' . $collectionFolder);
	}
	exit(0);
}

$collectionName = $argv[1];

(new Collection\Utility($systemState))->validateCollectionName($collectionName);

$lipupiniPath = $systemState->dirCollection . '/' . $collectionName . '/.lipupini';

// Create the `.lipupini` subfolder if needed
if (!is_dir($lipupiniPath)) {
	echo 'Creating `.lipupini` folder...' . "\n";
	mkdir($lipupiniPath, 0755, true);
}

echo 'About to generate new RSA keys in `collection/' . $collectionName . '/.lipupini/`...' . "\n";

if ($readlineSupport) {
	$confirm = readline('Proceed? [Y/n] ');
} else {
	$confirm = 'Y';
}

if (strtoupper($confirm) !== 'Y') {
	exit(0);
}

if (
	file_exists($lipupiniPath . '/rsakey.private') ||
	file_exists($lipupiniPath . '/rsakey.private')
) {
	echo 'Already exists, doing nothing. Manually delete to regenerate.' . "\n";
	exit(1);
}

(new Encryption\Key)->generateAndSave(
	privateKeyPath: $lipupiniPath . '/rsakey.private',
	publicKeyPath: $lipupiniPath . '/rsakey.public',
	privateKeyBits: 2048,
);

echo 'Done.' . "\n";

exit(0);
