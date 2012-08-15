
<div class="divider nobr" id="tPageFormSubmit">
	<a id="pageFormSubmit" class="button submit">
		<?= lang('ionize_button_save_page') ?>
	</a>
</div>

<div class="divider nobr" id="tPageDeleteButton">
	<a id="pageDeleteButton" class="button no">
		<?= lang('ionize_button_delete') ?>
	</a>
</div>

<div class="divider" id="tSideColumnSwitcher">
	<a class="button light" id="sideColumnSwitcher">
		<i class="icon-options"></i><?= lang('ionize_label_options') ?>
	</a>
</div>

<div class="divider" id="tPageAddContentElement">
	<a id="addContentElement" class="button light" >
		<i class="icon-element"></i><?= lang('ionize_label_add_content_element') ?>
	</a>
</div>

<div class="divider" id="tPageMediaButton">
	<a id="addMedia" class="fmButton button light">
		<i class="icon-pictures"></i><?= lang('ionize_label_attach_media') ?>
	</a>
</div>

<div class="divider" id="tPageAddArticle">
	<a id="addArticle" class="fmButton button light">
		<i class="icon-article add"></i><?= lang('ionize_label_add_article') ?>
	</a>
</div>


<script type="text/javascript">

	/**
	 * Form save action
	 * see init.js for more information about this method
	 *
	 */
	ION.setFormSubmit('pageForm', 'pageFormSubmit', 'page/save');
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
		$('tSideColumnSwitcher').hide();
	}
	else
	{
		// Delete button
	 	var url = admin_url + 'page/delete/';
		ION.initRequestEvent($('pageDeleteButton'), url + id, {'redirect':true}, {'confirm':true, 'message': Lang.get('ionize_confirm_element_delete')})

		// Add Content Element button
		$('addContentElement').addEvent('click', function(e)
		{
			ION.dataWindow('contentElement', 'ionize_title_add_content_element', 'element/add_element', {width:500, height:350}, {'parent':'page', 'id_parent': id});
		});


		$('addMedia').addEvent('click', function(e)
		{
			e.stop();
			mediaManager.initParent('page', $('id_page').value);
			mediaManager.toggleFileManager();
		});


		/**
		 * Article create button link
		 */
		$('addArticle').addEvent('click', function(e)
		{
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
