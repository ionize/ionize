
<div class="toolbox nobr">
	<input id="settingsFormSubmit" type="button" class="submit" value="<?php echo lang('ionize_button_save_settings'); ?>" />
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
	ION.addFormSaveEvent('settingsFormSubmit');

</script>
