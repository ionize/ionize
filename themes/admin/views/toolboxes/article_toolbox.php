<div class="toolbox divider nobr" id="tArticleFormSubmit">
	<input id="articleFormSubmit" type="button" class="submit" value="<?= lang('ionize_button_save_article') ?>" />
</div>

<div class="toolbox divider nobr" id="tArticleDeleteButton">
	<input id="articleDeleteButton" type="button" class="button no" value="<?= lang('ionize_button_delete') ?>" />
</div>

<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="sidecolumnSwitcher" value="<?= lang('ionize_label_hide_options') ?>" />
</div>

<div class="toolbox divider" id="tArticleDuplicateButton">
	<span class="iconWrapper" id="articleDuplicateButton"><img src="<?= theme_url() ?>images/icon_16_copy_article.gif" width="16" height="16" alt="<?= lang('ionize_button_duplicate_article') ?>" title="<?= lang('ionize_button_duplicate_article') ?>" /></span>
</div>

<div class="toolbox divider" id="tArticleAddContentElement">
	<input id="addContentElement" type="button" class="toolbar-button element" value="<?= lang('ionize_label_add_content_element') ?>" />
</div>

<div class="toolbox" id="tArticleMediaButton">
	<input id="addMedia" type="button" class="fmButton toolbar-button pictures" value="<?= lang('ionize_label_attach_media') ?>"/>
</div>

<script type="text/javascript">


	/**
	 * Form save action
	 * see init.js for more information about this method
	 *
	 */
	ION.setFormSubmit('articleForm', 'articleFormSubmit', 'article/save');
	
	
	/**
	 * Delete & Duplicate button buttons
	 *
	 */
	var id = $('id_article').value;

	if ( ! id )
	{
		$('tArticleDeleteButton').hide();
		$('tArticleDuplicateButton').hide();
		$('tArticleAddContentElement').hide();
		$('tArticleMediaButton').hide();
	}
	else
	{
		// Delete button
/*		
		$('articleDeleteButton').setProperty('rel', id);
		ION.initItemDeleteEvent($('articleDeleteButton'), 'article');
*/
	 	var url = admin_url + 'article/delete/';
		ION.initRequestEvent($('articleDeleteButton'), url + id, {'redirect':true}, {'message': Lang.get('ionize_confirm_element_delete')})

		
		// Duplicate button
		$('articleDuplicateButton').addEvent('click', function(e)
		{
			var url = $('name').value;
			
			// Article's current context (page)
			var rel = ($('rel').value).split(".");
			
			var data = {'id_page': rel[0]};
			
			ION.formWindow(	'DuplicateArticle', 'newArticleForm', 'ionize_title_duplicate_article', 'article/duplicate/' + id + '/' + url, {width:520, height:280}, data);
		});
		
		$('addMedia').addEvent('click', function(e)
		{
			var e = new Event(e).stop();
			mediaManager.initParent('article', $('id_article').value);
			mediaManager.toggleFileManager();
		});
		
		// Add Content Element button
		$('addContentElement').addEvent('click', function(e)
		{
			ION.dataWindow('contentElement', 'ionize_title_add_content_element', 'element/add_element', {width:500, height:300}, {'parent':'article', 'id_parent': id});
		});
		
		
	}
		
	/**
	 * Options show / hide button
	 *
	 */
	ION.initSideColumn();
	
	/**
	 * Save with CTRL+s
	 *
	 */
	ION.addFormSaveEvent('articleFormSubmit');

</script>
