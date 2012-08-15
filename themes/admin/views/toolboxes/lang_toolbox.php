
<div class="divider">
	<a class="button light" id="newLangToolbarButton">
		<i class="icon-plus"></i><?= lang('ionize_label_new_lang') ?>
	</a>
</div>
<!--
<div class="toolbox divider nobr">
	<input id="existingLangFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save') ?>" />
</div>

<div class="divider">
	<a class="button light" id="sideColumnSwitcher">
		<i class="icon-options"></i><?= lang('ionize_label_hide_options') ?>
	</a>
</div>
-->
<script type="text/javascript">

	/**
	 * New lang button
	 *
	 */
	$('newLangToolbarButton').addEvent('click', function(e)
	{
		ION.formWindow(
			'lang',
			'langForm',
			Lang.get('ionize_label_new_lang'),
			'lang/get_form'
		);
	});

	/**
	 * Adds action to the existing languages form
	 * See mocha-init.js for more information about this method
	 *
	 */
	// ION.setFormSubmit('existingLangForm', 'existingLangFormSubmit', 'lang/update');


	/**
	 * Save with CTRL+s
	 *
	 */
	// ION.addFormSaveEvent('existingLangFormSubmit');

</script>
