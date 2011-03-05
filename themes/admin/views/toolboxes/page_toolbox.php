<div class="toolbox divider nobr">
	<input id="pageFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save_page') ?>" />
</div>

<div class="toolbox divider nobr" id="tPageDeleteButton">
	<input id="pageDeleteButton" type="button" class="button no" value="<?= lang('ionize_button_delete') ?>" />
</div>

<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="sidecolumnSwitcher" value="<?= lang('ionize_label_hide_options') ?>" />
</div>

<script type="text/javascript">

	/**
	 * Form save action
	 * see init.js for more information about this method
	 *
	 */
	MUI.setFormSubmit('pageForm', 'pageFormSubmit', 'page/save');


	/**
	 * Delete & Duplicate button buttons
	 *
	 */
	var id = $('id_page').value;

	if ( ! id )
	{
		$('tPageDeleteButton').hide();
	}
	else
	{
		// Delete button
		$('pageDeleteButton').setProperty('rel', id);
		ION.initItemDeleteEvent($('pageDeleteButton'), 'page');
	}

	/**
	 * Options show / hide button
	 *
	 */
	MUI.initSideColumn();

	/**
	 * Save with CTRL+s
	 *
	 */
	MUI.addFormSaveEvent('pageFormSubmit');

</script>
