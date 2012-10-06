
<div class="divider nobr" id="tArticleFormSubmit">
	<a id="articleFormSubmit" class="button submit">
		<?php echo lang('ionize_button_save_article'); ?>
	</a>
</div>

<div class="divider nobr" id="tArticleDeleteButton">
	<a id="articleDeleteButton" class="button no">
		<?php echo lang('ionize_button_delete'); ?>
	</a>
</div>

<div class="divider">
	<a class="button light" id="sideColumnSwitcher">
		<i class="icon-options"></i><?php echo lang('ionize_label_options'); ?>
	</a>
</div>

<div class="divider" id="tArticleDuplicateButton">
	<a class="icon duplicate" id="articleDuplicateButton" title="<?php echo lang('ionize_button_duplicate_article'); ?>"></a>
</div>

<div class="divider" id="tArticleAddContentElement">
	<a id="addContentElement" class="button light" >
		<i class="icon-element"></i><?php echo lang('ionize_label_add_content_element'); ?>
	</a>
</div>

<div class="divider" id="tArticleMediaButton">
	<a id="addMedia" class="fmButton button light">
		<i class="icon-pictures"></i><?php echo lang('ionize_label_attach_media'); ?>
	</a>
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
        $('sideColumnSwitcher').hide();
	}
	else
	{
		// Delete button
	 	var url = admin_url + 'article/delete/';
		ION.initRequestEvent($('articleDeleteButton'), url + id, {'redirect':true}, {'confirm':true,'message': Lang.get('ionize_confirm_element_delete')})

		
		// Duplicate button
		$('articleDuplicateButton').addEvent('click', function(e)
		{
			var url = $('name').value;
			
			// Article's current context (page)
			var rel = ($('rel').value).split(".");
			var data = {'id_page': rel[0]};
			ION.formWindow(
				'DuplicateArticle',
				'newArticleForm',
				'ionize_title_duplicate_article',
				'article/duplicate/' + id + '/' + url,
				{width:520, height:320},
				data
			);
		});
		
		$('addMedia').addEvent('click', function(e)
		{
			e.stop();
			mediaManager.initParent('article', $('id_article').value);
			mediaManager.toggleFileManager();
		});
		
		// Add Content Element button
		$('addContentElement').addEvent('click', function(e)
		{
			ION.dataWindow('contentElement', 'ionize_title_add_content_element', 'element/add_element', {width:500, height:350}, {'parent':'article', 'id_parent': id});
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
