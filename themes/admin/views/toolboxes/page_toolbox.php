<?php if(Authority::can('edit', 'admin/page')) :?>

	<div class="divider nobr" id="tPageFormSubmit">
		<a id="pageFormSubmit" class="button submit">
			<?php echo lang('ionize_button_save_page'); ?>
		</a>
	</div>

<?php endif;?>

<?php if(Authority::can('delete', 'admin/page')) :?>

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

<?php if(Authority::can('add', 'admin/page/element')) :?>

	<div class="divider" id="tPageAddContentElement">
		<a id="addContentElement" class="button light" >
			<i class="icon-element"></i><?php echo lang('ionize_label_add_content_element'); ?>
		</a>
	</div>

<?php endif;?>

<?php if
(
	Authority::can('link', 'admin/page/media/picture')
	OR Authority::can('link', 'admin/page/media/video')
	OR Authority::can('link', 'admin/page/media/music')
	OR Authority::can('link', 'admin/page/media/file')
)
:?>

	<div class="divider" id="tPageMediaButton">
		<a id="addMedia" class="fmButton button light">
			<i class="icon-pictures"></i><?php echo lang('ionize_label_attach_media'); ?>
		</a>
	</div>

<?php endif;?>

<?php if(Authority::can('add', 'admin/page/article') OR Authority::can('create', 'admin/article')) :?>

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
        if ($('tPageMediaButton')) $('tPageMediaButton').hide();
        if ($('tPageAddArticle')) $('tPageAddArticle').hide();
        if ($('tSideColumnSwitcher')) $('tSideColumnSwitcher').hide();
	}
	else
	{
		<?php if(Authority::can('delete', 'admin/page')) :?>

    		// Delete button
	 		var url = admin_url + 'page/delete/';
			ION.initRequestEvent($('pageDeleteButton'), url + id, {'redirect':true}, {'confirm':true, 'message': Lang.get('ionize_confirm_element_delete')})

		<?php endif;?>


		<?php if(Authority::can('add', 'admin/page/element')) :?>

			// Add Content Element button
			$('addContentElement').addEvent('click', function(e)
			{
				ION.dataWindow('contentElement', 'ionize_title_add_content_element', 'element/add_element', {width:500, height:350}, {'parent':'page', 'id_parent': id});
			});

		<?php endif;?>


		<?php if(
			Authority::can('link', 'admin/page/media/picture')
			OR Authority::can('link', 'admin/page/media/video')
			OR Authority::can('link', 'admin/page/media/music')
			OR Authority::can('link', 'admin/page/media/file')
		)
		:?>

			$('addMedia').addEvent('click', function(e)
			{
				e.stop();
				mediaManager.initParent('page', $('id_page').value);
				mediaManager.toggleFileManager();
			});

		<?php endif;?>


		<?php if(Authority::can('add', 'admin/page/article') OR Authority::can('create', 'admin/article')) :?>

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
