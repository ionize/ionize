<div class="toolbox divider nobr">
	<input type="button" id="pageFormSubmit" class="button yes" value="<?= lang('ionize_button_save_page') ?>" />
</div>

<div class="toolbox divider nobr" id="tPageDeleteButton">
	<input type="button" id="pageDeleteButton" class="button no" value="<?= lang('ionize_button_delete') ?>" />
</div>

<div class="toolbox divider">
	<input type="button" class="toolbar-button" id="sidecolumnSwitcher" value="<?= lang('ionize_label_hide_options') ?>" />
</div>

<div class="toolbox divider" id="tPageAddContentElement">
	<input type="button" id="addContentElement" class="toolbar-button element" value="<?= lang('ionize_label_add_content_element') ?>" />
</div>

<div class="toolbox divider" id="tPageMediaButton">
	<input type="button" id="addMedia" class="fmButton toolbar-button pictures" value="<?= lang('ionize_label_attach_media') ?>"/>
</div>

<div class="toolbox" id="tPageAddArticle">
	<input type="button" id="addArticle" class="toolbar-button plus" value="<?= lang('ionize_label_add_article') ?>" />
</div>




<script type="text/javascript">

	/**
	 * Form save action
	 * see init.js for more information about this method
	 *
	 */
	ION.setFormSubmit('pageForm', 'pageFormSubmit', 'page/save');


	/**
	 * Delete & Duplicate button buttons
	 *
	 */
	var id = $('id_page').value;

	if ( ! id )
	{
		$('tPageDeleteButton').hide();
		$('tPageAddContentElement').hide();
		$('tPageMediaButton').hide();
		$('tPageAddArticle').hide();
	}
	else
	{
		// Delete button
//		$('pageDeleteButton').setProperty('rel', id);
//		ION.initItemDeleteEvent($('pageDeleteButton'), 'page');

	 	var url = admin_url + 'page/delete/';
		ION.initRequestEvent($('pageDeleteButton'), url + id, {'redirect':true}, {'confirm':true, 'message': Lang.get('ionize_confirm_element_delete')})



		// Add Content Element button
		$('addContentElement').addEvent('click', function(e)
		{
			ION.dataWindow('contentElement', 'ionize_title_add_content_element', 'element/add_element', {width:500, height:350}, {'parent':'page', 'id_parent': id});
		});


		$('addMedia').addEvent('click', function(e)
		{
//			var e = new Event(e).stop();
			e.stop();
			mediaManager.initParent('page', $('id_page').value);
			mediaManager.toggleFileManager();
		});


		/**
		 * Article create button link
		 */
		$('addArticle').addEvent('click', function(e)
		{
//			var e = new Event(e).stop();
			e.stop();
			
			MUI.Content.update({
				'element': $('mainPanel'),
				'loadMethod': 'xhr',
				'url': admin_url + 'article/create/' + id,
				'title': Lang.get('ionize_title_create_article')
			});
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
	ION.addFormSaveEvent('pageFormSubmit');

</script>
