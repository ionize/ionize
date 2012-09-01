<div class="toolbox divider nobr">
	<input id="existingMenuFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save') ?>" />
</div>

<div class="divider">
	<a class="button light" id="newMenuToolbarButton">
		<i class="icon-plus"></i><?= lang('ionize_button_create_menu') ?>
	</a>
</div>

<script type="text/javascript">

	/**
	 * Adds action to the existing menus form
	 * See mocha-init.js for more information about this method
	 *
	 */
	ION.setFormSubmit('existingMenuForm', 'existingMenuFormSubmit', 'menu/update');

	$('newMenuToolbarButton').addEvent('click', function(e)
	{
		ION.formWindow(
			'menu',
			'menuForm',
			Lang.get('ionize_title_create_menu'),
			admin_url + 'menu/get_form',
			{
				'width':350,
				'height':130
			}
		);
	});

	/**
	 * Save with CTRL+s
	 *
	 */
	ION.addFormSaveEvent('existingMenuFormSubmit');

</script>

