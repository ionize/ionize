
<h2 class="main languages"><?php echo lang('ionize_title_translation') ?></h2>


<form name="defaultTranslationLangCodeForm" id="defaultTranslationLangCodeForm" method="post" action="<?php echo admin_url(); ?>translation/set_default_lang_code">

	<div class="ml50">
		<p><?php echo lang('ionize_msg_translation_select_source_language') ?></p>

		<p>
			<select id="default_translation_lang_code" name="default_translation_lang_code" class="select">
				<?php foreach(Settings::get_languages() as $language) :?>
					<option value="<?php echo $language['lang']; ?>"<?php if($language['lang'] == $default_lang_code): ?> selected="selected"<?php endif; ?>><?php echo $language['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<input id="defaultTranslationLangCodeSubmit" type="submit" class="submit" value="<?php echo lang('ionize_button_save'); ?>" />
		</p>

	</div>
</form>

<script type="text/javascript">

	// Empty toolbox
	ION.getToolbox();


	ION.setFormSubmit('defaultTranslationLangCodeForm','defaultTranslationLangCodeSubmit','translation/set_default_lang_code');
</script>