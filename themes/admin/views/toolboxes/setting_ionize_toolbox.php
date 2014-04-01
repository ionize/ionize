
<div class="toolbox nobr">
	<input id="ionizeSettingsFormSubmit" type="button" class="submit" value="<?php echo lang('ionize_button_save_settings'); ?>" />
</div>


<script type="text/javascript">

	/**
	 * Views form
	 * see ionize-form.js for more information about this method
	 */
	ION.setFormSubmit('ionizeSettingsForm', 'ionizeSettingsFormSubmit', 'setting/save_ionize');

	/**
	 * Save with CTRL+s
	 *
	 */
	ION.addFormSaveEvent('ionizeSettingsForm');

</script>
