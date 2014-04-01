
<div class="toolbox nobr">
	<input id="viewsFormSubmit" type="button" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
</div>


<script type="text/javascript">

	/**
	 * Views form
	 * see ionize-form.js for more information about this method
	 */
	ION.setFormSubmit('viewsForm', 'viewsFormSubmit', 'setting/save_views');


	/**
	 * Save with CTRL+s
	 *
	 */
	ION.addFormSaveEvent('viewsFormSubmit');


</script>
