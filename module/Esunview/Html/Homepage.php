<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

require(__DIR__ . '/Core/Open.php') ?>

<div class="view-collections">
	<p><strong><a href="<?php echo $this->system->baseUri ?>@">View Collections</a></strong></p>
</div>

<?php foreach ($this->sections as $section => $content): ?>

<section id="<?php echo htmlentities($section) ?>"><?php echo $content ?></section>
<?php endforeach;

require(__DIR__ . '/Core/Close.php');
