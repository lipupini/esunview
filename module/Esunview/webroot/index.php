<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

use Module\Lipupini\Request;
use Module\Lipupini\State;

// `realpath` resolves symlinks and returns absolute path
$projectRootDir = realpath(__DIR__ . '/../../../');
/** @var State $systemState */
$systemState = require($projectRootDir . '/system/config/state.php');

return (new Request\Queue(
	$systemState
))->render();
