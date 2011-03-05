<div class="toolbox divider nobr" id="tArticleFormSubmit">
	<input id="articleFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save_article') ?>" />
</div>

<div class="toolbox divider nobr" id="tArticleDeleteButton">
	<input id="articleDeleteButton" type="button" class="button no" value="<?= lang('ionize_button_delete') ?>" />
</div>

<div class="toolbox divider" id="tArticleDuplicateButton">
	<span class="iconWrapper" id="articleDuplicateButton"><img src="<?= theme_url() ?>images/icon_16_copy_article.gif" width="16" height="16" alt="<?= lang('ionize_button_duplicate_article') ?>" title="<?= lang('ionize_button_duplicate_article') ?>" /></span>
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
	MUI.setFormSubmit('articleForm', 'articleFormSubmit', 'article/save');
	
	
	/**
	 * Delete & Duplicate button buttons
	 *
	 */
	var id = $('id_article').value;

	if ( ! id )
	{
		$('tArticleDeleteButton').hide();
		$('tArticleDuplicateButton').hide();
	}
	else
	{
		// Delete button
		$('articleDeleteButton').setProperty('rel', id);
		ION.initItemDeleteEvent($('articleDeleteButton'), 'article');
		
		// Duplicate button
		$('articleDuplicateButton').addEvent('click', function(e)
		{
			var url = $('name').value;

			MUI.formWindow('DuplicateArticle', 'newArticleForm', 'ionize_title_duplicate_article', 'article/duplicate/' + id + '/' + url, {width:520, height:200});
		});
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
	MUI.addFormSaveEvent('articleFormSubmit');

</script>
