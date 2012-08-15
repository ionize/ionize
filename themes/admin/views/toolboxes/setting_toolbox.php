<div class="toolbox divider nobr">
	<input id="settingsFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save_settings') ?>" />
</div>


<script type="text/javascript">

	/**
	 * Views form
	 * see ionize-form.js for more information about this method
	 */
	ION.setFormSubmit('settingsForm', 'settingsFormSubmit', 'setting/save');

	/**
	 * Save with CTRL+s
	 *
	 */
	ION.addFormSaveEvent('settingsForm');

</script>
