<?php

/**
 * View used by Page and Article controller to display parent list (pages) in the parents select dropdown
 * When the selected menu is changed, the page parent select dropdown is reloaded
 *
 */
?>


<?php foreach($pages as $id => $title) :?>
	<option value="<?= $id ?>"<?php if ($id_selected==$id) :?> selected="selected"<?php endif; ?>><?= $title ?></option>
<?php endforeach ;?>

