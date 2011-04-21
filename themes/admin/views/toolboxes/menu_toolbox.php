<div class="toolbox divider nobr">
	<input id="existingMenuFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save') ?>" />
</div>

<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="sidecolumnSwitcher" value="<?= lang('ionize_label_hide_options') ?>" />
</div>

<script type="text/javascript">


	/**
	 * Adds action to the existing menus form
	 * See mocha-init.js for more information about this method
	 *
	 */
	ION.setFormSubmit('existingMenuForm', 'existingMenuFormSubmit', 'menu/update');


	/**
	 * Options show / hide button
	 *
	 */
	ION.initSideColumn();

	/**
	 * Save with CTRL+s
	 *
	 */
	ION.addFormSaveEvent('existingMenuFormSubmit');

</script>
