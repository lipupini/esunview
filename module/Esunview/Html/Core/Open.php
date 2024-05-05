<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

use Module\Lipupini\L18n\A;
A::$path = realpath(__DIR__ . '/../../../Esunview/');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if (!empty($this->pageImagePreviewUri)) : ?>
<meta property="og:image" content="<?php echo htmlentities($this->pageImagePreviewUri) ?>">
<?php endif ?>
<title><?php echo htmlentities($this->pageTitle) ?></title>
<?php echo $this->htmlHead ?? '' ?>
</head>
<body>
