<?php
/**
 *
 */

// Check if at least on Item instance exists (same for Content Element Definition)
$is_item_empty = $this->base_model->is_empty(NULL, 'item');
$is_element_empty = $this->base_model->is_empty(NULL, 'element_definition');

?>

<?php if(Authority::can('edit', 'admin/page') && Authority::can('edit', 'backend/page/' . $id_page, null, true)) :?>

	<div class="divider nobr" id="tPageFormSubmit">
		<a id="pageFormSubmit" class="button submit">
			<?php echo lang('ionize_button_save_page'); ?>
		</a>
	</div>

<?php endif;?>

<?php if(Authority::can('delete', 'admin/page') && Authority::can('delete', 'backend/page/' . $id_page, null, true)) :?>

	<div class="divider nobr" id="tPageDeleteButton">
		<a id="pageDeleteButton" class="button no">
			<?php echo lang('ionize_button_delete'); ?>
		</a>
	</div>

<?php endif;?>

<div class="divider" id="tSideColumnSwitcher">
	<a class="button light" id="sideColumnSwitcher">
		<i class="icon-options"></i><?php echo lang('ionize_label_options'); ?>
	</a>
</div>

<?php if( ! $is_element_empty && Authority::can('add', 'admin/page/element')) :?>

	<div class="divider" id="tPageAddContentElement">
		<a id="addContentElement" class="button light" >
			<i class="icon-element"></i><?php echo lang('ionize_label_add_content_element'); ?>
		</a>
	</div>

<?php endif;?>

<?php if( ! $is_item_empty && Authority::can('add', 'admin/item')) :?>

	<div class="divider" id="tPageAddItem">
		<a id="btnAddItem" class="button light" >
			<i class="icon-items"></i><?php echo lang('ionize_label_add_item'); ?>
		</a>
	</div>

<?php endif;?>

<?php if (
		(Authority::can('add', 'admin/page/article') OR Authority::can('create', 'admin/article'))
		&& Authority::can('add_article', 'backend/page/' . $id_page, null, true)
	)
:?>
	<div class="divider" id="tPageAddArticle">
		<a id="addArticle" class="fmButton button light">
			<i class="icon-article add"></i><?php echo lang('ionize_label_add_article'); ?>
		</a>
	</div>

<?php endif;?>


<script type="text/javascript">

	<?php if(Authority::can('edit', 'admin/page')) :?>

		// Form save action
		ION.setFormSubmit('pageForm', 'pageFormSubmit', 'page/save');

	<?php endif;?>


	// Delete & Duplicate buttons
	var id = $('id_page').value;

	if ( ! id )
	{
		if ($('tPageDeleteButton')) $('tPageDeleteButton').hide();
        if ($('tPageAddContentElement')) $('tPageAddContentElement').hide();
        if ($('tPageAddItem')) $('tPageAddItem').hide();
        if ($('tPageAddArticle')) $('tPageAddArticle').hide();
        if ($('tSideColumnSwitcher')) $('tSideColumnSwitcher').hide();
	}
	else
	{
		<?php if(Authority::can('delete', 'admin/page') && Authority::can('delete', 'backend/page/' . $id_page, null, true)) :?>

    		// Delete button
	 		var url = admin_url + 'page/delete/';
			ION.initRequestEvent($('pageDeleteButton'), url + id, {'redirect':true}, {'confirm':true, 'message': Lang.get('ionize_confirm_element_delete')})

		<?php endif;?>


		<?php if(! $is_element_empty && Authority::can('add', 'admin/page/element')) :?>

			// Add Content Element button
			$('addContentElement').addEvent('click', function()
			{
				ION.dataWindow('contentElement', 'ionize_title_add_content_element', 'element/add_element', {width:500, height:350}, {'parent':'page', 'id_parent': id});
			});

		<?php endif;?>

		<?php if( ! $is_item_empty && Authority::can('add', 'admin/item')) :?>

			$('btnAddItem').addEvent('click', function()
			{
				staticItemManager.openListWindow();
			});

		<?php endif;?>

		<?php if(
			(Authority::can('add', 'admin/page/article') OR Authority::can('create', 'admin/article'))
			&& Authority::can('add_article', 'backend/page/' . $id_page, null, true)
		)
		:?>

			// Article create button link
			$('addArticle').addEvent('click', function(e)
			{
				e.stop();

				ION.contentUpdate({
					'element': $('mainPanel'),
					'loadMethod': 'xhr',
					'url': admin_url + 'article/create/' + id,
					'title': Lang.get('ionize_title_create_article')
				});
			});

		<?php endif;?>

    }

	// Options column switcher
	ION.initSideColumn('sideColumnSwitcher');

	<?php if(Authority::can('edit', 'admin/page')) :?>

    	// Save with CTRL+s
		ION.addFormSaveEvent('pageFormSubmit');

	<?php endif;?>

</script>
