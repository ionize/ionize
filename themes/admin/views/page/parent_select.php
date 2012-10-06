<?php

/**
 * View used by Page and Article controller to display parent list (pages) in the parents select dropdown
 * When the selected menu is changed, the page parent select dropdown is reloaded
 *
 */

$element_id = (!empty($element_id)) ? $element_id : 'id_parent';

?>

<select name="<?php echo $element_id; ?>" id="<?php echo $element_id; ?>" class="select">

	<?php foreach($pages as $id => $title) :?>
		<option value="<?php echo $id; ?>"<?php if ($id_selected==$id) :?> selected="selected"<?php endif; ?>><?php echo strip_tags($title); ?></option>
	<?php endforeach ;?>

</select>

<script type="text/javascript">

	if (Browser.ie || (Browser.firefox && Browser.version < 4))
	{
		var selected = $('<?php echo $element_id; ?>').getElement('option[selected=selected]');
		selected.setProperty('selected', 'selected');

		if ('<?php echo $element_id; ?>' == 'id_parent')
		{
			if ($('origin_id_parent').value == '0')
				$('id_parent').getFirst('option').setProperty('selected', 'selected');
		}
	}

</script>