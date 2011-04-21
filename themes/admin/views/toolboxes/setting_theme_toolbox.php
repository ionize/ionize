<div class="toolbox divider nobr">
	<input id="viewsFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save') ?>" />
</div>

<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="sidecolumnSwitcher" value="<?= lang('ionize_label_hide_options') ?>" />
</div>

<script type="text/javascript">

	/**
	 * Views form
	 * see mocha/init-forms.js for more information about this method
	 */
	ION.setFormSubmit('viewsForm', 'viewsFormSubmit', 'setting/save_views');

	/**
	 * Options show / hide button
	 *
	 */
	ION.initSideColumn();


	/**
	 * Save with CTRL+s
	 *
	 */
	ION.addFormSaveEvent('viewsFormSubmit');


</script>
