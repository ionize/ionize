<div id="maincolumn">
    <h2 class="main translation"><?php echo lang('ionize_title_translation') ?></h2>
    <hr />
    <div class="tabcolumn pt15">
        <form name="defaultTranslationLangCodeForm" id="defaultTranslationLangCodeForm" method="post" action="<?php echo admin_url(); ?>translation/set_default_lang_code">
            <dl>
                <dt>&nbsp;</dt>
                <dd>Please select default translation language for translation source.</dd>
            </dl>
            <dl>
                <dt>Default Lang</dt>
                <dd>
                    <select id="default_translation_lang_code" name="default_translation_lang_code" class="select">
                        <?php foreach(Settings::get_languages() as $language) :?>
                            <option value="<?php echo $language['lang']; ?>"<?php if($language['lang'] == $default_lang_code): ?> selected="selected"<?php endif; ?>><?php echo $language['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </dd>
            </dl>
            <dl>
                <dt>&nbsp;</dt>
                <dd><input id="defaultTranslationLangCodeSubmit" type="submit" class="submit" value="<?php echo lang('ionize_button_save'); ?>" /></dd>
            </dl>
        </form>
    </div>
</div>
<script type="text/javascript">
    ION.setFormSubmit('defaultTranslationLangCodeForm', 'defaultTranslationLangCodeSubmit', 'translation/set_default_lang_code');
</script>