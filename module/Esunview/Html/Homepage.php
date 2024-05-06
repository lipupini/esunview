<?php

/*
 * License: https://github.com/lipupini/esunview/blob/master/LICENSE.md
 * Homepage: https://c.dup.bz
*/

require(__DIR__ . '/Core/Open.php') ?>

<div class="view-collections">
	<p><strong><a href="<?php echo $this->system->baseUri ?>@" class="button border">View Collections</a></strong></p>
</div>

<?php echo $this->sections['readme'] ?>


<?php echo $this->sections['selling'] ?>


<?php

require(__DIR__ . '/Core/Close.php');
