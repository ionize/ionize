<div class="toolbox divider nobr">
	<input id="existingLangFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save') ?>" />
</div>

<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="sidecolumnSwitcher" value="<?= lang('ionize_label_hide_options') ?>" />
</div>

<script type="text/javascript">


	/**
	 * Adds action to the existing languages form
	 * See mocha-init.js for more information about this method
	 *
	 */
	MochaUI.setFormSubmit('existingLangForm', 'existingLangFormSubmit', 'lang/update');


	/**
	 * Options show / hide button
	 *
	 */
	MUI.initSideColumn();


	/**
	 * Save with CTRL+s
	 *
	 */
	MUI.addFormSaveEvent('existingLangFormSubmit');

</script>
