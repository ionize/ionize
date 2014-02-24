<?php
/**
 *
 */

// Check if at least on Item instance exists (same for Content Element Definition)
$is_item_empty = $this->base_model->is_empty(NULL, 'item');
$is_element_empty = $this->base_model->is_empty(NULL, 'element_definition');

?>

<?php if(Authority::can('edit', 'admin/article')) :?>

	<div class="divider nobr" id="tArticleFormSubmit">
		<a id="articleFormSubmit" class="button submit">
			<?php echo lang('ionize_button_save_article'); ?>
		</a>
	</div>

<?php endif;?>

<?php if(Authority::can('delete', 'admin/article')) :?>

	<div class="divider nobr" id="tArticleDeleteButton">
		<a id="articleDeleteButton" class="button no">
			<?php echo lang('ionize_button_delete'); ?>
		</a>
	</div>

<?php endif;?>


<div class="divider">
    <a class="button light" id="sideColumnSwitcher">
        <i class="icon-options"></i><?php echo lang('ionize_label_options'); ?>
    </a>
</div>


<?php if(Authority::can('edit', 'admin/article')) :?>

	<div class="divider">
		<a class="button light" id="editionModeSwitcher">
			<i class="icon-edit_article"></i><?php echo lang('ionize_button_edit_mode'); ?>
		</a>
	</div>

<?php endif;?>

<?php if(Authority::can('duplicate', 'admin/article')) :?>

	<div class="divider" id="tArticleDuplicateButton">
		<a class="icon duplicate" id="articleDuplicateButton" title="<?php echo lang('ionize_button_duplicate_article'); ?>"></a>
	</div>

<?php endif;?>

<?php if( ! $is_element_empty && Authority::can('add', 'admin/article/element')) :?>

	<div class="divider" id="tArticleAddContentElement">
		<a id="addContentElement" class="button light" >
			<i class="icon-element"></i><?php echo lang('ionize_label_add_content_element'); ?>
		</a>
	</div>

<?php endif;?>

<?php if( ! $is_item_empty && Authority::can('add', 'admin/item')) :?>

	<div class="divider" id="tArticleAddItem">
		<a id="btnAddItem" class="button light" >
			<i class="icon-items"></i><?php echo lang('ionize_label_add_item'); ?>
		</a>
	</div>

<?php endif;?>



<script type="text/javascript">


	<?php if(Authority::can('edit', 'admin/article')) :?>

		// Form save action
		ION.setFormSubmit('articleForm', 'articleFormSubmit', 'article/save');

	<?php endif;?>

	// Delete & Duplicate button buttons
	var id = $('id_article').value;

	if ( ! id )
	{
		if ($('tArticleDeleteButton')) $('tArticleDeleteButton').hide();
        if ($('tArticleDuplicateButton')) $('tArticleDuplicateButton').hide();
        if ($('tArticleAddContentElement')) $('tArticleAddContentElement').hide();
        if ($('tArticleAddItem')) $('tArticleAddItem').hide();
        if ($('sideColumnSwitcher')) $('sideColumnSwitcher').hide();
	}
	else
	{
		<?php if(Authority::can('delete', 'admin/article')) :?>

    		// Delete button
	 		var url = admin_url + 'article/delete/';
			ION.initRequestEvent($('articleDeleteButton'), url + id, {'redirect':true}, {'confirm':true,'message': Lang.get('ionize_confirm_element_delete')})

		<?php endif;?>

		<?php if(Authority::can('duplicate', 'admin/article')) :?>

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

		<?php endif;?>


		<?php if( ! $is_element_empty && Authority::can('add', 'admin/article/element')) :?>

			// Add Content Element button
			$('addContentElement').addEvent('click', function(e)
			{
				ION.dataWindow('contentElement', 'ionize_title_add_content_element', 'element/add_element', {width:500, height:350}, {'parent':'article', 'id_parent': id});
			});

		<?php endif;?>

		<?php if( ! $is_item_empty && Authority::can('add', 'admin/item')) :?>

			$('btnAddItem').addEvent('click', function()
			{
				staticItemManager.openListWindow();
			});

		<?php endif;?>
	}

	<?php if(Authority::can('edit', 'admin/article')) :?>

		// Edition Mode button
		ION.initEditMode('editionModeSwitcher', 'article', '.article-header');

	<?php endif;?>

	// Options show / hide button
	ION.initSideColumn('sideColumnSwitcher');
	

	<?php if(Authority::can('edit', 'admin/article')) :?>

		// Save with CTRL+s
		ION.addFormSaveEvent('articleFormSubmit');
	<?php endif;?>

</script>
